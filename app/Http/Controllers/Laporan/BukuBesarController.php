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
        // Ambil COA untuk dropdown (non-group biasanya lebih dipilih)
        $coas = Coa::query()
            ->select(['id', 'code_account_id', 'name', 'set_as_group'])
            ->orderBy('code_account_id')
            ->get();

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

        $q = JournalLine::query()
            ->with([
                'coa:id,code_account_id,name',
                'journal:id,journal_no,journal_date,status',
            ])
            ->whereHas('journal', function ($jq) use ($start, $end, $status) {
                if ($start)  $jq->whereDate('journal_date', '>=', $start);
                if ($end)    $jq->whereDate('journal_date', '<=', $end);
                if ($status) $jq->where('status', $status);
            });

        if (!empty($coaIds)) {
            $q->whereIn('coa_id', $coaIds);
        }

        $lines = $q->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
            ->orderBy('journal_lines.coa_id')
            ->orderBy('journals.journal_date')
            ->orderBy('journals.journal_no')
            ->orderBy('journal_lines.line_no')
            ->get(['journal_lines.*']);

        $grouped = $lines->groupBy('coa_id');

        $accounts = [];
        $grandDebit  = 0.0;
        $grandCredit = 0.0;

        foreach ($grouped as $coaIdKey => $rows) {
            $coa = $rows->first()->coa;

            $running = 0.0;
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

            $grandDebit  += $totalDebit;
            $grandCredit += $totalCredit;

            $accounts[] = [
                'coa_id'       => (int) $coaIdKey,
                'coa_code'     => $coa?->code_account_id ?? '-',
                'coa_name'     => $coa?->name ?? '-',
                'total_debit'  => round($totalDebit, 2),
                'total_credit' => round($totalCredit, 2),
                'ending'       => round($running, 2),
                'entries'      => $entries,
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
