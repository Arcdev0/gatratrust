<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JournalLine;
use Illuminate\Http\Request;

class NeracaController extends Controller
{
    /**
     * Halaman Neraca (view only)
     */
    public function index(Request $request)
    {
        // optional: kirim COA untuk kebutuhan UI/mapping di masa depan
        $coas = Coa::query()
            ->select(['id', 'code_account_id', 'name', 'default_posisi', 'set_as_group'])
            ->orderBy('code_account_id')
            ->get();

        return view('laporan.Neraca.index', compact('coas'));
    }

    /**
     * Endpoint JSON Neraca (dipanggil via jQuery)
     *
     * Params:
     * - date: YYYY-MM-DD (optional)  -> posisi neraca per tanggal tsb (<= date)
     * - status: draft|posted (optional)
     *
     * Output:
     * - assets[]
     * - liabilities_equity[]
     * - totals + check balance
     */
    public function data(Request $request)
    {
        $date = $request->query('date'); // posisi per tanggal
        $status = $request->query('status'); // optional

        $rows = JournalLine::query()
            ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
            ->join('coa', 'coa.id', '=', 'journal_lines.coa_id')
            ->when($date, fn($q) => $q->whereDate('journals.journal_date', '<=', $date))
            ->when($status, fn($q) => $q->where('journals.status', $status))
            ->where('coa.set_as_group', 0)
            ->selectRaw(
                "
                journal_lines.coa_id,
                coa.code_account_id as coa_code,
                coa.name as coa_name,
                UPPER(TRIM(COALESCE(coa.default_posisi,''))) as posisi,
                SUM(journal_lines.debit)  as total_debit,
                SUM(journal_lines.credit) as total_credit
            ",
            )
            ->groupBy('journal_lines.coa_id', 'coa.code_account_id', 'coa.name', 'posisi')
            ->orderBy('coa.code_account_id')
            ->get();

        $assets = [];
        $liabilitiesEquity = [];
        $unknown = [];

        $totalAssets = 0.0;
        $totalLE = 0.0;

        foreach ($rows as $r) {
            $posisi = strtoupper((string) $r->posisi);

            // saldo akun berdasarkan posisi normal
            // debit normal: saldo = debit - credit
            // credit normal: saldo = credit - debit
            $saldoDebitNormal = (float) $r->total_debit - (float) $r->total_credit;
            $saldoCreditNormal = (float) $r->total_credit - (float) $r->total_debit;

            if ($posisi === 'DEBIT') {
                $val = $saldoDebitNormal;

                $assets[] = [
                    'coa_id' => (int) $r->coa_id,
                    'coa_code' => $r->coa_code,
                    'coa_name' => $r->coa_name,
                    'amount' => round($val, 2),
                ];
                $totalAssets += $val;
            } elseif ($posisi === 'CREDIT') {
                $val = $saldoCreditNormal;

                $liabilitiesEquity[] = [
                    'coa_id' => (int) $r->coa_id,
                    'coa_code' => $r->coa_code,
                    'coa_name' => $r->coa_name,
                    'amount' => round($val, 2),
                ];
                $totalLE += $val;
            } else {
                $unknown[] = [
                    'coa_id' => (int) $r->coa_id,
                    'coa_code' => $r->coa_code,
                    'coa_name' => $r->coa_name,
                    'posisi' => $posisi,
                    'debit' => round((float) $r->total_debit, 2),
                    'credit' => round((float) $r->total_credit, 2),
                ];
            }
        }

        // cek keseimbangan neraca
        $diff = round($totalAssets - $totalLE, 2);

        return response()->json([
            'meta' => [
                'date' => $date ?: null,
                'status' => $status ?: null,
            ],
            'assets' => $assets,
            'liabilities_equity' => $liabilitiesEquity,
            'unknown' => $unknown,
            'summary' => [
                'total_assets' => round($totalAssets, 2),
                'total_liabilities_equity' => round($totalLE, 2),
                'difference' => $diff,
                'is_balanced' => $diff == 0.0,
            ],
        ]);
    }
}
