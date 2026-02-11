<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JournalLine;
use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        // Optional: buat dropdown/cek mapping di UI nanti
        $coas = Coa::query()
            ->select(['id', 'code_account_id', 'name', 'default_posisi', 'set_as_group'])
            ->orderBy('code_account_id')
            ->get();

        return view('laporan.LaporanLabaRugi.index', compact('coas'));
    }

    public function data(Request $request)
    {
        $start  = $request->query('start');
        $end    = $request->query('end');
        $status = $request->query('status'); // optional

        $rows = JournalLine::query()
            ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
            ->join('coa', 'coa.id', '=', 'journal_lines.coa_id')
            ->when($start, fn($q) => $q->whereDate('journals.journal_date', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('journals.journal_date', '<=', $end))
            ->when($status, fn($q) => $q->where('journals.status', $status))
            ->where('coa.set_as_group', 0)
            ->selectRaw("
                journal_lines.coa_id,
                coa.code_account_id as coa_code,
                coa.name as coa_name,
                UPPER(TRIM(COALESCE(coa.default_posisi,''))) as posisi,
                SUM(journal_lines.debit)  as total_debit,
                SUM(journal_lines.credit) as total_credit
            ")
            ->groupBy('journal_lines.coa_id', 'coa.code_account_id', 'coa.name', 'posisi')
            ->orderBy('coa.code_account_id')
            ->get();

        $revenue = [];
        $expense = [];
        $unknown = [];

        $totalRevenue = 0.0;
        $totalExpense = 0.0;

        foreach ($rows as $r) {
            $posisi = strtoupper((string) $r->posisi);

            // nilai pendapatan (normal CREDIT): credit - debit
            $valRevenue = (float) $r->total_credit - (float) $r->total_debit;

            // nilai beban (normal DEBIT): debit - credit
            $valExpense = (float) $r->total_debit - (float) $r->total_credit;

            if ($posisi === 'CREDIT') {
                $revenue[] = [
                    'coa_id'   => (int) $r->coa_id,
                    'coa_code' => $r->coa_code,
                    'coa_name' => $r->coa_name,
                    'amount'   => round($valRevenue, 2),
                ];
                $totalRevenue += $valRevenue;
            } elseif ($posisi === 'DEBIT') {
                $expense[] = [
                    'coa_id'   => (int) $r->coa_id,
                    'coa_code' => $r->coa_code,
                    'coa_name' => $r->coa_name,
                    'amount'   => round($valExpense, 2),
                ];
                $totalExpense += $valExpense;
            } else {
                // default_posisi kosong / beda penulisan (misal KREDIT/DEBIT)
                $unknown[] = [
                    'coa_id'   => (int) $r->coa_id,
                    'coa_code' => $r->coa_code,
                    'coa_name' => $r->coa_name,
                    'posisi'   => $posisi,
                    'debit'    => round((float)$r->total_debit, 2),
                    'credit'   => round((float)$r->total_credit, 2),
                ];
            }
        }

        $netProfit = $totalRevenue - $totalExpense;

        return response()->json([
            'meta' => [
                'start'  => $start,
                'end'    => $end,
                'status' => $status ?: null,
            ],
            'revenue' => $revenue,
            'expense' => $expense,
            'unknown' => $unknown, // kalau ada mapping yang belum sesuai, keliatan di UI (optional)
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_expense' => round($totalExpense, 2),
                'net_profit'    => round($netProfit, 2),
            ],
        ]);
    }
}
