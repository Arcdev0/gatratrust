<?php

namespace App\Http\Controllers;

use App\Models\AccountingSetting;
use App\Models\Coa;
use App\Models\Fpu;
use App\Models\FpuLine;
use App\Models\FpuLineAttachment;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

// ✅ trait jurnal kamu
use App\Traits\JournalTrait;

class FpuController extends Controller
{
    use JournalTrait;

    /**
     * View halaman index (datatable)
     */
    public function index()
    {
        return view('fpus.index');
    }

    /**
     * Endpoint yajra datatable
     */
    public function datatable(Request $request)
    {
        $q = Fpu::query()
            ->with([
                'walletCoa:id,code_account_id,name',
                'requester:id,name',
                'approvedBy:id,name',
            ])
            ->select('fpus.*');

        return DataTables::of($q)
            ->addIndexColumn()
            ->editColumn('request_date', function (Fpu $fpu) {
                return optional($fpu->request_date)->format('d-m-Y');
            })
            ->editColumn('total_amount', function (Fpu $fpu) {
                return number_format((float)$fpu->total_amount, 2);
            })
            ->addColumn('wallet', function (Fpu $fpu) {
                if (!$fpu->walletCoa) return '-';
                return $fpu->walletCoa->code_account_id . ' - ' . $fpu->walletCoa->name;
            })
            ->addColumn('requester', function (Fpu $fpu) {
                return $fpu->requester?->name ?? $fpu->requester_name ?? '-';
            })
            ->addColumn('approved_info', function (Fpu $fpu) {
                if ($fpu->status !== Fpu::STATUS_APPROVED && $fpu->status !== Fpu::STATUS_PAID) return '-';
                $by = $fpu->approvedBy?->name ?? '-';
                $at = $fpu->approved_at ? $fpu->approved_at->format('d-m-Y H:i') : '-';
                return "{$by} • {$at}";
            })
            ->addColumn('status_badge', function (Fpu $fpu) {
                $s = $fpu->status;
                $map = [
                    Fpu::STATUS_DRAFT => 'secondary',
                    Fpu::STATUS_SUBMITTED => 'warning',
                    Fpu::STATUS_APPROVED => 'primary',
                    Fpu::STATUS_REJECTED => 'danger',
                    Fpu::STATUS_PAID => 'success',
                    Fpu::STATUS_CANCELLED => 'dark',
                ];
                $c = $map[$s] ?? 'secondary';
                return '<span class="badge badge-' . $c . '">' . strtoupper($s) . '</span>';
            })
            ->addColumn('action', function (Fpu $fpu) {
                $btn = '';

                // edit hanya draft/rejected
                if (in_array($fpu->status, [Fpu::STATUS_DRAFT, Fpu::STATUS_REJECTED], true)) {
                    $btn .= '<a href="' . route('fpus.edit', $fpu->id) . '" class="btn btn-sm btn-secondary mr-1">
                        <i class="fas fa-edit"></i> Edit
                    </a>';
                } else {
                    $btn .= '<a href="' . route('fpus.show', $fpu->id) . '" class="btn btn-sm btn-outline-secondary mr-1">
                        <i class="fas fa-eye"></i> View
                    </a>';
                }

                // approve hanya submitted (nanti modal approve)
                if ($fpu->status === Fpu::STATUS_SUBMITTED) {
                    $btn .= '<button type="button" class="btn btn-sm btn-primary btnApproveFpu" data-id="' . $fpu->id . '">
                        <i class="fas fa-check"></i> Approve
                    </button>';
                }

                return $btn;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Optional: Show detail (buat page view)
     */
    public function show($id)
    {
        $fpu = Fpu::with(['lines.attachments', 'walletCoa'])->findOrFail($id);
        return view('fpus.show', compact('fpu'));
    }

    /**
     * APPROVE (finance pilih wallet) + BUAT JURNAL ACCRUAL (UMUM)
     * Dr Expense (atau suspense) total
     * Cr AP total
     */
    public function approve(Request $request, $id)
    {
        $fpu = Fpu::with('lines')->findOrFail($id);

        if ($fpu->status !== Fpu::STATUS_SUBMITTED) {
            return response()->json([
                'success' => false,
                'message' => 'FPU hanya bisa di-approve dari status submitted'
            ], 422);
        }

        $validated = $request->validate([
            'wallet_coa_id' => ['required', 'integer', Rule::exists('coa', 'id')],
        ]);

        // validasi wallet_coa_id harus ada di daftar wallets (accounting_setting_id=1)
        $isWalletAllowed = Wallet::where('accounting_setting_id', 1)
            ->where('coa_id', $validated['wallet_coa_id'])
            ->exists();

        if (!$isWalletAllowed) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet yang dipilih tidak termasuk daftar Wallet di Accounting Settings.'
            ], 422);
        }

        if ($fpu->lines->count() < 1) {
            return response()->json([
                'success' => false,
                'message' => 'FPU harus memiliki minimal 1 line'
            ], 422);
        }

        return DB::transaction(function () use ($fpu, $validated) {

            // ambil default mapping
            $d = $this->journalDefaults();

            $ap = $d['ap'] ?? null;
            $expense = $d['expense'] ?? null;
            $suspense = $d['suspense'] ?? null;

            if (!$ap) {
                throw new \Exception('Accounting Settings belum lengkap. Isi default AP terlebih dahulu.');
            }

            // debit ke expense jika ada, kalau kosong fallback ke suspense
            $debitCoa = $expense ?: $suspense;
            if (!$debitCoa) {
                throw new \Exception('Accounting Settings belum lengkap. Isi default Expense atau Suspense terlebih dahulu.');
            }

            $total = (float) $fpu->lines->sum('amount');
            if ($total <= 0) {
                throw new \Exception('Total FPU harus lebih dari 0.');
            }

            // update approve state
            $fpu->update([
                'wallet_coa_id' => $validated['wallet_coa_id'],
                'status' => Fpu::STATUS_APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // kalau sudah pernah dibuat approve journal, jangan buat lagi
            if (!$fpu->approve_journal_id) {
                $journal = $this->createJournal(
                    [
                        'journal_date' => $fpu->request_date,
                        'type' => 'general',
                        'category' => 'fpu_approve',
                        'reference_no' => $fpu->fpu_no,
                        'memo' => "Accrual FPU Approved {$fpu->fpu_no}",
                        'status' => 'posted',
                    ],
                    [
                        [
                            'coa_id' => $debitCoa,
                            'debit' => $total,
                            'credit' => 0,
                            'description' => 'FPU Expense (Accrual)',
                        ],
                        [
                            'coa_id' => $ap,
                            'debit' => 0,
                            'credit' => $total,
                            'description' => 'Accounts Payable (Accrual)',
                        ],
                    ]
                );

                $fpu->update(['approve_journal_id' => $journal->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'FPU berhasil di-approve + jurnal accrual dibuat',
                'data' => $fpu->fresh(['walletCoa']),
            ]);
        });
    }

    /**
     * Upload bukti untuk FPU Line
     * - hanya boleh jika FPU status approved / paid (pilih salah satu)
     * - setiap line first-time proof => buat jurnal cash-out per line (Dr AP, Cr Wallet)
     */
    public function uploadLineAttachment(Request $request, $lineId)
    {
        $line = FpuLine::with('fpu')->findOrFail($lineId);
        $fpu  = $line->fpu;

        if (!in_array($fpu->status, [Fpu::STATUS_APPROVED, Fpu::STATUS_PAID], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Upload bukti hanya bisa setelah FPU di-approve'
            ], 422);
        }

        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:5120'], // 5MB/file (sesuaikan)
        ]);

        return DB::transaction(function () use ($validated, $line, $fpu) {

            $saved = [];

            foreach ($validated['files'] as $file) {
                $original = $file->getClientOriginalName();
                $mime = $file->getClientMimeType();
                $size = $file->getSize();

                $path = $file->store("fpus/{$fpu->fpu_no}/lines/{$line->id}", 'public');

                $att = FpuLineAttachment::create([
                    'fpu_line_id' => $line->id,
                    'file_path'   => $path,
                    'file_name'   => $original,
                    'mime_type'   => $mime,
                    'file_size'   => $size,
                    'uploaded_by' => auth()->id(),
                ]);

                $saved[] = $att;
            }

            // refresh proof_count + has_proof
            $line->refreshProof();

            // ✅ Jika line baru pertama kali punya proof => buat jurnal cash-out per line
            $lineFresh = $line->fresh(['attachments', 'fpu']);

            if ($lineFresh->has_proof && !$lineFresh->paid_journal_id) {

                $d = $this->journalDefaults();

                $ap = $d['ap'] ?? null;
                if (!$ap) {
                    throw new \Exception('Accounting Settings belum lengkap. Isi default AP terlebih dahulu.');
                }

                if (!$fpu->wallet_coa_id) {
                    throw new \Exception('Wallet COA belum dipilih pada FPU. Approve harus memilih wallet.');
                }

                $amt = (float) $lineFresh->amount;
                if ($amt <= 0) {
                    throw new \Exception('Amount line harus > 0.');
                }

                // Jurnal cash-out per line: Dr AP, Cr Wallet
                $journal = $this->createJournal(
                    [
                        'journal_date' => now()->toDateString(),
                        'type' => 'cash_out',
                        'category' => 'fpu_paid_line',
                        'reference_no' => $fpu->fpu_no . '-L' . $lineFresh->line_no,
                        'memo' => "Cash-out FPU {$fpu->fpu_no} Line #{$lineFresh->line_no}",
                        'status' => 'posted',
                    ],
                    [
                        [
                            'coa_id' => $ap,
                            'debit' => $amt,
                            'credit' => 0,
                            'description' => 'Reduce Accounts Payable',
                        ],
                        [
                            'coa_id' => $fpu->wallet_coa_id,
                            'debit' => 0,
                            'credit' => $amt,
                            'description' => 'Wallet Cash/Bank Out',
                        ],
                    ]
                );

                $lineFresh->update([
                    'paid_journal_id' => $journal->id,
                    'paid_at' => now(),
                ]);
            }

            // ✅ Update status FPU => paid jika semua line sudah paid_journal_id
            $fpuFresh = $fpu->fresh(['lines']);
            $fpuFresh->refreshPaidStatus();

            return response()->json([
                'success' => true,
                'message' => 'Bukti berhasil diupload',
                'data' => [
                    'line' => $lineFresh->fresh(['attachments', 'paidJournal']),
                    'fpu'  => $fpuFresh->fresh(),
                    'attachments' => $saved,
                ]
            ]);
        });
    }

    /**
     * Delete attachment (optional)
     * Catatan: kalau kamu izinkan hapus bukti, perlu aturan:
     * - jika sudah ada paid_journal_id, jangan boleh hapus (biar akuntansi konsisten)
     */
    public function deleteLineAttachment($attachmentId)
    {
        $att = FpuLineAttachment::with('line.fpu')->findOrFail($attachmentId);
        $line = $att->line;
        $fpu  = $line->fpu;

        if ($fpu->status !== Fpu::STATUS_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Hapus bukti hanya bisa saat status approved'
            ], 422);
        }

        // ✅ jika sudah terbentuk paid journal untuk line, jangan izinkan hapus bukti
        if ($line->paid_journal_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus bukti karena jurnal pembayaran line sudah terbentuk.'
            ], 422);
        }

        return DB::transaction(function () use ($att, $line) {

            if ($att->file_path && Storage::disk('public')->exists($att->file_path)) {
                Storage::disk('public')->delete($att->file_path);
            }

            $att->delete();

            $line->refreshProof();

            return response()->json([
                'success' => true,
                'message' => 'Bukti berhasil dihapus',
                'data' => [
                    'line' => $line->fresh(['attachments']),
                    'fpu'  => $line->fpu->fresh(),
                ]
            ]);
        });
    }
}
