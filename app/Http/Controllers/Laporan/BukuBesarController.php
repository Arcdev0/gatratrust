<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JournalLine;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        // ambil semua COA non-group
        $coas = Coa::query()
            ->where('set_as_group', 0)
            ->orderBy('code_account_id')
            ->get();

        // ambil saldo masing-masing akun
        $balances = JournalLine::query()
            ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
            ->selectRaw("
            journal_lines.coa_id,
            SUM(journal_lines.debit) as total_debit,
            SUM(journal_lines.credit) as total_credit
        ")
            ->groupBy('journal_lines.coa_id')
            ->get()
            ->keyBy('coa_id');

        // inject saldo ke masing-masing coa
        foreach ($coas as $coa) {

            $totalDebit  = $balances[$coa->id]->total_debit ?? 0;
            $totalCredit = $balances[$coa->id]->total_credit ?? 0;

            // karena ini buku besar umum, pakai rumus saldo:
            $coa->ending_balance = $totalDebit - $totalCredit;
        }

        return view('laporan.BukuBesar.index', compact('coas'));
    }

    /**
     * Endpoint JSON untuk Buku Besar (dipanggil via jQuery/AJAX)
     *
     * Query params:
     * - start: YYYY-MM-DD (optional)
     * - end:   YYYY-MM-DD (optional)
     * - coa_ids[]: array of int (optional) <-- dari select2 multiple
     *   fallback: coa_id (optional)
     * - status: draft|posted (optional)
     */
    public function data(Request $request)
    {
        $start  = $request->query('start');
        $end    = $request->query('end');
        $status = $request->query('status');

        // multi select: coa_ids[]
        $coaIds = $request->query('coa_ids');

        // fallback single coa_id
        if (empty($coaIds)) {
            $single = $request->query('coa_id');
            if (!empty($single)) {
                $coaIds = [$single];
            }
        }

        // normalisasi -> array int
        if (!is_array($coaIds)) {
            $coaIds = $coaIds ? [$coaIds] : [];
        }
        $coaIds = array_values(array_filter(array_map(function ($v) {
            if ($v === null || $v === '') return null;
            if (!is_numeric($v)) return null;
            return (int) $v;
        }, $coaIds), fn($v) => $v !== null));

        // kalau user tidak kirim filter akun, tampilkan semua akun
        if (empty($coaIds)) {
            $coaIds = Coa::query()->pluck('id')->toArray();
        }

        // master akun (biar akun tanpa transaksi tetap ada)
        $coaMaster = Coa::query()
            ->select(['id', 'code_account_id', 'name'])
            ->whereIn('id', $coaIds)
            ->orderBy('code_account_id')
            ->get();

        // -----------------------------
        // 1) BEGINNING BALANCE (saldo sebelum start)
        // SUM(debit - credit) untuk journal_date < start
        // -----------------------------
        $beginMap = collect();

        if ($start) {
            $beginRows = JournalLine::query()
                ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
                ->whereIn('journal_lines.coa_id', $coaIds)
                ->whereDate('journals.journal_date', '<', $start)
                ->when($status, fn($q) => $q->where('journals.status', $status))
                ->groupBy('journal_lines.coa_id')
                ->selectRaw('journal_lines.coa_id as coa_id, SUM(journal_lines.debit - journal_lines.credit) as saldo')
                ->get();

            $beginMap = $beginRows->keyBy('coa_id')->map(fn($r) => (float) $r->saldo);
        } else {
            // kalau start tidak diisi, beginning balance = 0 (atau kamu bisa ubah sesuai kebutuhan)
            $beginMap = collect();
        }

        // -----------------------------
        // 2) MUTASI PERIODE (start..end)
        // -----------------------------
        $q = JournalLine::query()
            ->with([
                'coa:id,code_account_id,name',
                'journal:id,journal_no,journal_date,status',
            ])
            ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
            ->whereIn('journal_lines.coa_id', $coaIds)
            ->when($start, fn($qq) => $qq->whereDate('journals.journal_date', '>=', $start))
            ->when($end, fn($qq) => $qq->whereDate('journals.journal_date', '<=', $end))
            ->when($status, fn($qq) => $qq->where('journals.status', $status))
            ->orderBy('journal_lines.coa_id')
            ->orderBy('journals.journal_date')
            ->orderBy('journals.journal_no')
            ->orderBy('journal_lines.line_no')
            ->get(['journal_lines.*']); // tetap ambil journal_lines.* agar relation jalan

        $grouped = $q->groupBy('coa_id');

        $accounts = [];
        $grandDebit  = 0.0;
        $grandCredit = 0.0;

        foreach ($coaMaster as $coa) {
            $rows = $grouped->get($coa->id, collect());

            $beginning = (float) ($beginMap->get($coa->id, 0.0));
            $running   = $beginning;

            $totalDebit  = 0.0;
            $totalCredit = 0.0;

            $entries = [];

            foreach ($rows as $r) {
                $debit  = (float) $r->debit;
                $credit = (float) $r->credit;

                $running += ($debit - $credit);
                $totalDebit  += $debit;
                $totalCredit += $credit;

                $entries[] = [
                    'date'       => optional($r->journal)->journal_date?->format('Y-m-d'),
                    'journal_no'  => optional($r->journal)->journal_no,
                    'status'      => optional($r->journal)->status,
                    'description' => $r->description,
                    'debit'       => round($debit, 2),
                    'credit'      => round($credit, 2),
                    'balance'     => round($running, 2),
                ];
            }

            // kalau tidak ada transaksi, tetap tampil 1 baris angka 0
            if (count($entries) === 0) {
                $entries[] = [
                    'date'       => null,
                    'journal_no'  => null,
                    'status'      => null,
                    'description' => 'Tidak ada transaksi',
                    'debit'       => 0.00,
                    'credit'      => 0.00,
                    'balance'     => round($running, 2), // tetap saldo awal
                ];
            }

            $grandDebit  += $totalDebit;
            $grandCredit += $totalCredit;

            $accounts[] = [
                'coa_id'            => (int) $coa->id,
                'coa_code'          => $coa->code_account_id ?? '-',
                'coa_name'          => $coa->name ?? '-',

                'beginning_balance' => round($beginning, 2),

                'total_debit'       => round($totalDebit, 2),
                'total_credit'      => round($totalCredit, 2),

                'ending_balance'    => round($running, 2), // saldo akhir = saldo berjalan terakhir
                'entries'           => $entries,
            ];
        }

        return response()->json([
            'meta' => [
                'start'          => $start,
                'end'            => $end,
                'coa_ids'        => $coaIds,
                'status'         => $status ?: null,
                'accounts_count' => count($accounts),
            ],
            'summary' => [
                'grand_debit'  => round($grandDebit, 2),
                'grand_credit' => round($grandCredit, 2),
            ],
            'accounts' => $accounts,
        ]);
    }
}
