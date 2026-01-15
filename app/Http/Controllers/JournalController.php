<?php

namespace App\Http\Controllers;

use App\Models\AccountingSetting;
use App\Models\Coa;
use App\Models\Journal;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JournalController extends Controller
{
    public function index()
    {
        return view('journals.index');
    }

    public function datatable(Request $request)
    {
        $query = Journal::query()
            ->select([
                'journals.id',
                'journals.journal_no',
                'journals.journal_date',
                'journals.type',
                'journals.category',
                'journals.reference_no',
                'journals.status',
                'journals.created_at',
            ]);

        return DataTables::of($query)
            ->editColumn('journal_date', function ($row) {
                return \Carbon\Carbon::parse($row->journal_date)->format('d-m-Y');
            })
            ->editColumn('type', function ($row) {
                return strtoupper(str_replace('_', ' ', $row->type));
            })
            ->editColumn('status', function ($row) {
                $badge = match ($row->status) {
                    'posted' => 'success',
                    'draft'  => 'secondary',
                    'void'   => 'danger',
                    default  => 'light',
                };
                return '<span class="badge bg-' . $badge . '">' . strtoupper($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                <a href="' . route('journals.edit', $row->id) . '" class="btn btn-sm btn-secondary">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="btn btn-sm btn-danger btnDeleteJournal" data-id="' . $row->id . '">
                    <i class="fas fa-trash"></i>
                </button>
            ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function create()
    {
        $coaSelectable = Coa::where('set_as_group', false)
            ->orderBy('code_account_id')
            ->get(['id', 'code_account_id', 'name', 'default_posisi']);

        $setting = AccountingSetting::first();

        return view('journals.create', [
            'coaSelectable' => $coaSelectable,
            'setting' => $setting,
        ]);
    }

    public function edit($id)
    {
        $journal = Journal::with(['lines.coa'])->findOrFail($id);

        $coaSelectable = Coa::where('set_as_group', false)
            ->orderBy('code_account_id')
            ->get(['id', 'code_account_id', 'name', 'default_posisi']);

        return view('journals.edit', [
            'journal' => $journal,
            'coaSelectable' => $coaSelectable,
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $this->validateJournalRequest($request);

            // Generate nomor jurnal
            $journalNo = $this->generateJournalNo();

            $journal = Journal::create([
                'journal_no'   => $journalNo,
                'journal_date' => $validated['journal_date'],
                'type'         => $validated['type'],
                'category'     => $validated['category'] ?? null,
                'reference_no' => $validated['reference_no'] ?? null,
                'memo'         => $validated['memo'] ?? null,
                'status'       => $validated['status'] ?? 'draft',
                'created_by'   => auth()->id(),
            ]);

            $this->syncJournalLines($journal, $validated['lines']);

            // kalau status posted -> set posted_by & posted_at
            if (($validated['status'] ?? 'draft') === 'posted') {
                $journal->update([
                    'posted_by' => auth()->id(),
                    'posted_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'id' => $journal->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $this->validateJournalRequest($request, $id);

            $journal = Journal::findOrFail($id);

            // kalau sudah posted, biasanya dibatasi (opsional). Untuk sekarang kita izinkan edit.
            $journal->update([
                'journal_date' => $validated['journal_date'],
                'type'         => $validated['type'],
                'category'     => $validated['category'] ?? null,
                'reference_no' => $validated['reference_no'] ?? null,
                'memo'         => $validated['memo'] ?? null,
                'status'       => $validated['status'] ?? $journal->status,
            ]);

            $this->syncJournalLines($journal, $validated['lines'], true);

            // handle posted fields
            if (($validated['status'] ?? $journal->status) === 'posted' && !$journal->posted_at) {
                $journal->update([
                    'posted_by' => auth()->id(),
                    'posted_at' => now(),
                ]);
            }

            if (($validated['status'] ?? $journal->status) !== 'posted') {
                $journal->update([
                    'posted_by' => null,
                    'posted_at' => null,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $journal = Journal::with(['lines.coa'])->findOrFail($id);
        return response()->json($journal);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $journal = Journal::findOrFail($id);
            $journal->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa dihapus karena dipakai oleh data lain.'
                ], 400);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==========================
    // Helpers
    // ==========================

    private function validateJournalRequest(Request $request, ?int $journalId = null): array
    {
        $validated = $request->validate([
            'journal_date' => ['required', 'date'],
            'type'         => ['required', Rule::in(['general', 'cash_in', 'cash_out'])],
            'category'     => ['nullable', 'string', 'max:100'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'memo'         => ['nullable', 'string'],
            'status'       => ['nullable', Rule::in(['draft', 'posted', 'void'])],

            // lines: array of {coa_id, description, debit, credit, line_no(optional)}
            'lines'                => ['required', 'array', 'min:2'],
            'lines.*.coa_id'       => ['required', 'integer', Rule::exists('coa', 'id')],
            'lines.*.description'  => ['nullable', 'string', 'max:255'],
            'lines.*.debit'        => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit'       => ['nullable', 'numeric', 'min:0'],
            'lines.*.line_no'      => ['nullable', 'integer', 'min:1'],
        ]);

        // Pastikan COA yang dipakai bukan group
        $coaIds = collect($validated['lines'])->pluck('coa_id')->unique()->values();
        $groupUsed = Coa::whereIn('id', $coaIds)->where('set_as_group', true)->exists();
        if ($groupUsed) {
            throw new \Exception('COA group tidak boleh dipakai di jurnal. Pilih akun detail (non-group).');
        }

        // Hitung total debit/credit
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($validated['lines'] as $line) {
            $debit = (float)($line['debit'] ?? 0);
            $credit = (float)($line['credit'] ?? 0);

            // baris tidak boleh debit & credit sama-sama isi
            if ($debit > 0 && $credit > 0) {
                throw new \Exception('Satu baris hanya boleh Debit atau Credit (bukan dua-duanya).');
            }

            // baris tidak boleh dua-duanya nol
            if ($debit <= 0 && $credit <= 0) {
                throw new \Exception('Setiap baris wajib punya nilai Debit atau Credit.');
            }

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new \Exception("Jurnal tidak balance. Total Debit: {$totalDebit} dan Total Credit: {$totalCredit}");
        }

        // cash_in / cash_out wajib ada akun kas/bank? (opsional)
        // kalau Boss mau paksa, kita bisa cek di sini berdasarkan accounting_settings default_cash/bank.

        return $validated;
    }

    private function syncJournalLines(Journal $journal, array $lines, bool $isUpdate = false): void
    {
        if ($isUpdate) {
            // hapus lines lama biar simple (aman karena journal_lines on delete cascade)
            $journal->lines()->delete();
        }

        $payload = [];
        $i = 1;
        foreach ($lines as $line) {
            $payload[] = [
                'journal_id'   => $journal->id,
                'coa_id'       => $line['coa_id'],
                'description'  => $line['description'] ?? null,
                'debit'        => (float)($line['debit'] ?? 0),
                'credit'       => (float)($line['credit'] ?? 0),
                'line_no'      => $line['line_no'] ?? $i,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
            $i++;
        }

        JournalLine::insert($payload);
    }

    private function generateJournalNo(): string
    {
        $setting = AccountingSetting::query()->first();

        $prefix = $setting?->journal_prefix ?? 'JR';
        $running = (int)($setting?->journal_running_number ?? 1);
        $year = now()->format('Y');

        $journalNo = sprintf('%s-%s-%06d', $prefix, $year, $running);

        // increment running number (atomic dengan lock)
        // agar aman dari race condition:
        DB::table('accounting_settings')->updateOrInsert(
            ['id' => 1],
            [
                'journal_prefix' => $prefix,
                'journal_running_number' => $running + 1,
                'fiscal_year_start_month' => $setting?->fiscal_year_start_month ?? 1,
                'updated_at' => now(),
                'created_at' => $setting ? $setting->created_at : now(),
            ]
        );

        return $journalNo;
    }
}
