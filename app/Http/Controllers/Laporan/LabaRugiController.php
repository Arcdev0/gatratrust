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
        $status = $request->query('status');

        $rows = \App\Models\Coa::query()
            ->leftJoin('journal_lines', 'journal_lines.coa_id', '=', 'coa.id')
            ->leftJoin('journals', function ($join) use ($start, $end, $status) {
                $join->on('journals.id', '=', 'journal_lines.journal_id');

                if ($start) {
                    $join->whereDate('journals.journal_date', '>=', $start);
                }
                if ($end) {
                    $join->whereDate('journals.journal_date', '<=', $end);
                }
                if ($status) {
                    $join->where('journals.status', $status);
                }
            })
            ->where('coa.set_as_group', 0)
            ->where(function ($q) {
                $q->where('coa.code_account_id', 'like', '4%')   // Pendapatan
                    ->orWhere('coa.code_account_id', 'like', '51%') // HPP
                    ->orWhere('coa.code_account_id', 'like', '52%'); // Operasional
            })
            ->selectRaw("
            coa.id as coa_id,
            coa.code_account_id as coa_code,
            coa.name as coa_name,
            COALESCE(SUM(journal_lines.debit),0)  as total_debit,
            COALESCE(SUM(journal_lines.credit),0) as total_credit
        ")
            ->groupBy('coa.id', 'coa.code_account_id', 'coa.name')
            ->orderBy('coa.code_account_id')
            ->get();


        $revenue = [];
        $hpp = [];
        $operational = [];

        $totalRevenue = 0;
        $totalHpp = 0;
        $totalOperational = 0;

        foreach ($rows as $r) {

            $code = (string)$r->coa_code;

            // =====================
            // PENDAPATAN (4xxx)
            // =====================
            if (str_starts_with($code, '4')) {

                $amount = (float)$r->total_credit - (float)$r->total_debit;

                $revenue[] = [
                    'coa_id'   => $r->coa_id,
                    'coa_code' => $code,
                    'coa_name' => $r->coa_name,
                    'amount'   => round($amount, 2),
                ];

                $totalRevenue += $amount;
            }

            // =====================
            // HPP (51xx)
            // =====================
            elseif (str_starts_with($code, '51')) {

                $amount = (float)$r->total_debit - (float)$r->total_credit;

                $hpp[] = [
                    'coa_id'   => $r->coa_id,
                    'coa_code' => $code,
                    'coa_name' => $r->coa_name,
                    'amount'   => round($amount, 2),
                ];

                $totalHpp += $amount;
            }

            // =====================
            // BIAYA OPERASIONAL (52xx)
            // =====================
            elseif (str_starts_with($code, '52')) {

                $amount = (float)$r->total_debit - (float)$r->total_credit;

                $operational[] = [
                    'coa_id'   => $r->coa_id,
                    'coa_code' => $code,
                    'coa_name' => $r->coa_name,
                    'amount'   => round($amount, 2),
                ];

                $totalOperational += $amount;
            }
        }

        $grossProfit = $totalRevenue - $totalHpp;
        $netProfit   = $grossProfit - $totalOperational;

        return response()->json([
            'meta' => [
                'start'  => $start,
                'end'    => $end,
                'status' => $status ?: null,
            ],
            'revenue' => $revenue,
            'hpp' => $hpp,
            'operational' => $operational,
            'summary' => [
                'total_revenue'      => round($totalRevenue, 2),
                'total_hpp'          => round($totalHpp, 2),
                'gross_profit'       => round($grossProfit, 2),
                'total_operational'  => round($totalOperational, 2),
                'net_profit'         => round($netProfit, 2),
            ],
        ]);
    }
}
