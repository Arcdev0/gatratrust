<?php

namespace App\Http\Controllers;

use App\Models\AccountingSetting;
use App\Models\Coa;
use App\Models\Fpu;
use App\Models\FpuLine;
use App\Models\FpuLineAttachment;
use App\Models\ProjectTbl;
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
                return $fpu->requester_name ?? $fpu->requester?->name ?? '-';
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


    public function create()
    {
        $projects = ProjectTbl::query()
            ->orderBy('no_project')
            ->get(['id', 'no_project']);

        return view('fpus.create', compact('projects'));
    }

    public function searchProjects(Request $request)
    {
        return ProjectTbl::orderBy('no_project')
            ->get(['id', 'no_project'])
            ->map(fn($p) => [
                'id'   => $p->id,
                'text' => $p->no_project,
            ]);
    }


    public function getProjectPakItems($projectId)
    {
        $project = ProjectTbl::with('pak.items.category')->find($projectId);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project tidak ditemukan'
            ], 404);
        }

        if (!$project->pak) {
            return response()->json([
                'success' => true,
                'has_pak' => false,
                'items' => [],
                'message' => 'Project ini belum memiliki PAK'
            ]);
        }

        // Ambil kategori A, B, C
        $allowedCategoryCodes = ['A', 'B', 'C'];

        $items = $project->pak->items()
            ->whereHas('category', function ($q) use ($allowedCategoryCodes) {
                $q->whereIn('code', $allowedCategoryCodes);
            })
            ->get()
            ->map(function ($item) {

                $name = trim((string) $item->name);
                $desc = trim((string) $item->description);

                // anggap '-' atau kosong sebagai tidak ada deskripsi
                $hasDesc = $desc !== '' && $desc !== '-';

                return [
                    'description' => $hasDesc
                        ? "{$name} - {$desc}"
                        : $name,
                    'amount' => (float) $item->total_cost,
                ];
            });


        return response()->json([
            'success' => true,
            'has_pak' => true,
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'request_date' => ['required', 'date'],
            'requester_name' => ['nullable', 'string', 'max:150'],
            'purpose' => ['nullable', Rule::in([
                Fpu::PURPOSE_TAGIHAN,
                Fpu::PURPOSE_MATERIAL,
                Fpu::PURPOSE_AKOMODASI,
                Fpu::PURPOSE_VENDOR,
                Fpu::PURPOSE_LAINNYA,
            ])],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string'],
            'lines.*.amount' => ['required', 'numeric', 'min:0.01'],
            'action' => ['required', Rule::in(['draft', 'submit'])],
        ]);

        return DB::transaction(function () use ($validated) {

            $year = now()->format('Y');
            $seq  = str_pad((string)(Fpu::max('id') + 1), 6, '0', STR_PAD_LEFT);
            $fpuNo = "FPU-{$year}-{$seq}";

            $status = $validated['action'] === 'submit'
                ? Fpu::STATUS_SUBMITTED
                : Fpu::STATUS_DRAFT;

            $fpu = Fpu::create([
                'fpu_no' => $fpuNo,
                'project_id' => $validated['project_id'],
                'request_date' => $validated['request_date'],
                'requester_id' => auth()->id(),
                'requester_name' => $validated['requester_name'] ?? auth()->user()->name,
                'purpose' => $validated['purpose'],
                'notes' => $validated['notes'],
                'status' => $status,
                'submitted_at' => $status === Fpu::STATUS_SUBMITTED ? now() : null,
                'submitted_by' => $status === Fpu::STATUS_SUBMITTED ? auth()->id() : null,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($validated['lines'] as $i => $line) {
                $amount = (float) $line['amount'];

                $fpu->lines()->create([
                    'line_no' => $i + 1,
                    'description' => $line['description'],
                    'amount' => $amount,
                    'has_proof' => false,
                    'proof_count' => 0,
                ]);

                $total += $amount;
            }

            $fpu->update(['total_amount' => $total]);

            return response()->json([
                'success' => true,
                'message' => $status === 'submitted'
                    ? 'FPU berhasil disubmit'
                    : 'FPU berhasil disimpan sebagai draft',
                'data' => $fpu,
            ], 201);
        });
    }



    public function edit($id)
    {
        $fpu = Fpu::with('lines')->findOrFail($id);

        // if ($fpu->status !== Fpu::STATUS_DRAFT) {
        //     abort(403, 'Hanya FPU draft yang bisa diedit');
        // }

        $projects = ProjectTbl::orderBy('no_project')->get();

        return view('fpus.edit', compact('fpu', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $fpu = Fpu::findOrFail($id);

        if ($fpu->status !== Fpu::STATUS_DRAFT) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya FPU draft yang bisa diubah'
            ], 422);
        }

        $validated = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'request_date' => ['required', 'date'],
            'requester_name' => ['nullable', 'string', 'max:150'],
            'purpose' => ['nullable', Rule::in(Fpu::PURPOSES)],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string'],
            'lines.*.amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        return DB::transaction(function () use ($fpu, $validated) {

            // update header
            $fpu->update([
                'project_id' => $validated['project_id'],
                'request_date' => $validated['request_date'],
                'requester_name' => $validated['requester_name'],
                'purpose' => $validated['purpose'],
                'notes' => $validated['notes'],
            ]);

            // replace lines
            $fpu->lines()->delete();

            $total = 0;
            foreach ($validated['lines'] as $i => $line) {
                $amount = (float) $line['amount'];

                $fpu->lines()->create([
                    'line_no' => $i + 1,
                    'description' => $line['description'],
                    'amount' => $amount,
                    'has_proof' => false,
                    'proof_count' => 0,
                ]);

                $total += $amount;
            }

            $fpu->update(['total_amount' => $total]);

            return response()->json([
                'success' => true,
                'message' => 'FPU draft berhasil diperbarui'
            ]);
        });
    }



    public function submit($id)
    {
        $fpu = Fpu::findOrFail($id);

        if ($fpu->status !== Fpu::STATUS_DRAFT) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya FPU draft yang bisa disubmit'
            ], 422);
        }

        $fpu->update([
            'status' => Fpu::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'submitted_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FPU berhasil disubmit'
        ]);
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
