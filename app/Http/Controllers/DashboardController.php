<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     // Summary counts
    //     $totalProjects = DB::table('projects')->count();
    //     $activeProjects = DB::table('projects')->where('end', '>=', now())->count();

    //     $totalnominalproject = DB::table('projects')->sum('total_biaya_project');

    //     $invoiceStatus = DB::table('invoices')
    //         ->select('status', DB::raw('COUNT(*) as total'))
    //         ->groupBy('status')
    //         ->pluck('total', 'status')
    //         ->toArray();

    //     $totalPayments = (float) DB::table('invoice_payments')->sum('amount_paid');
    //     $totalInvoiceAmount = (float) DB::table('invoices')->sum('net_total');
    //     $outstanding = $totalInvoiceAmount - $totalPayments;

    //     // Revenue per month (current year) for line chart
    //     $revenueByMonth = DB::table('invoices')
    //         ->selectRaw('MONTH(date) as bulan, SUM(net_total) as total')
    //         ->whereYear('date', now()->year)
    //         ->where('status', 'close')
    //         ->groupBy('bulan')
    //         ->pluck('total', 'bulan');

    //     // Projects list (active)
    //     // NOTE: asumsi tabel clients ada, kalau beda sesuaikan joinnya
    //     $projects = DB::table('projects')
    //         ->join('users', 'projects.client_id', '=', 'users.id')
    //         ->select('projects.*', 'users.name as client_name')
    //         ->where('projects.end', '>=', now())
    //         ->get();

    //     // Invoice counts grouped by YEAR(date) and status -> untuk dropdown tahun
    //     $invoiceYearStatus = DB::table('invoices')
    //         ->selectRaw('YEAR(date) as year, status, COUNT(*) as total')
    //         ->groupBy('year', 'status')
    //         ->orderBy('year')
    //         ->get();

    //     $invoiceDataByYear = [];
    //     foreach ($invoiceYearStatus as $row) {
    //         $y = (int)$row->year;
    //         if (!isset($invoiceDataByYear[$y])) {
    //             $invoiceDataByYear[$y] = ['open' => 0, 'close' => 0];
    //         }
    //         $invoiceDataByYear[$y][$row->status] = (int)$row->total;
    //     }

    //     // Pastikan tahun sekarang ada di data (fallback 0 jika tidak ada invoice tahun ini)
    //     $currentYear = now()->year;
    //     if (!isset($invoiceDataByYear[$currentYear])) {
    //         $invoiceDataByYear[$currentYear] = [
    //             'open'  => $invoiceStatus['open'] ?? 0,
    //             'close' => $invoiceStatus['close'] ?? 0,
    //         ];
    //     }

    //     $currentMonth = now()->month;
    //     $lastMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    //     $lastMonthYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

    //     // Total pendapatan bulan lalu
    //     $lastMonthRevenue = DB::table('invoice_payments')
    //         ->whereMonth('payment_date', $lastMonth)
    //         ->whereYear('payment_date', $lastMonthYear)
    //         ->sum('amount_paid');

    //     // Total pendapatan bulan ini
    //     $currentMonthRevenue = DB::table('invoice_payments')
    //         ->whereMonth('payment_date', $currentMonth)
    //         ->whereYear('payment_date', $currentYear)
    //         ->sum('amount_paid');

    //     // Hitung pertumbuhan (growth)
    //     if ($lastMonthRevenue > 0) {
    //         $growthPercentage = round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2);
    //     } else {
    //         $growthPercentage = null; // belum ada data untuk dibandingkan
    //     }

    //     return view('dashboard.index', compact(
    //         'totalProjects',
    //         'activeProjects',
    //         'totalnominalproject',
    //         'invoiceStatus',
    //         'totalPayments',
    //         'totalInvoiceAmount',
    //         'outstanding',
    //         'revenueByMonth',
    //         'projects',
    //         'invoiceDataByYear',
    //         'growthPercentage'
    //     ));
    // }


    public function index()
    {
        $currentYear = Carbon::now()->year;

        $yearsFromProjects = DB::table('projects')
            ->selectRaw('YEAR(start) as year')
            ->whereNotNull('start')
            ->groupBy('year')
            ->pluck('year')
            ->toArray();

        $yearsFromInvoices = DB::table('invoices')
            ->selectRaw('YEAR(date) as year')
            ->whereNotNull('date')
            ->groupBy('year')
            ->pluck('year')
            ->toArray();

        // gabungkan, unique, sort ascending
        $availableYears = array_values(array_unique(array_merge($yearsFromProjects, $yearsFromInvoices)));
        sort($availableYears);

        // pastikan tahun sekarang ada di list (agar user bisa memilih tahun sekarang walau belum ada data)
        if (!in_array($currentYear, $availableYears)) {
            $availableYears[] = $currentYear;
            sort($availableYears);
        }

         // Projects list (active)
        // NOTE: asumsi tabel clients ada, kalau beda sesuaikan joinnya
        $projects = DB::table('projects')
            ->join('users', 'projects.client_id', '=', 'users.id')
            ->select('projects.*', 'users.name as client_name')
            ->where('projects.end', '>=', now())
            ->get();

        // kirim ke view
        return view('dashboard.index', [
            'availableYears' => $availableYears,
            'currentYear' => $currentYear,
            'projects' => $projects,
        ]);
    }

   public function getData(Request $request)
    {
        $year = (int) $request->query('year', Carbon::now()->year);

        // ----------------- SUMMARY (projects berdasarkan start) -----------------
        // Total Project (filter by start year)
        $totalProjects = (int) DB::table('projects')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->count();

        // Total nilai project (sum) berdasarkan start year
        $totalNominalProject = (float) DB::table('projects')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->sum('total_biaya_project');

        // ----------------- PAYMENTS / INVOICES (tetap pakai invoices.date & payment_date) -----------------
        // Total pendapatan = sum invoice_payments.amount_paid pada tahun (payment_date)
        $totalPayments = (float) DB::table('invoice_payments')
            ->whereYear('payment_date', $year)
            ->sum('amount_paid');

        // Total invoice amount di tahun (dipakai untuk outstanding) berdasarkan invoices.date
        $totalInvoiceAmount = (float) DB::table('invoices')
            ->whereYear('date', $year)
            ->sum('net_total');

        $outstanding = $totalInvoiceAmount - $totalPayments;

        // ---------- BAR CHART: paid vs unpaid per month ----------
        // invoicedByMonth: sum net_total grouped by MONTH(date) (invoices.date)
        $invoicedRows = DB::table('invoices')
            ->selectRaw('MONTH(date) as month, COALESCE(SUM(net_total),0) as total')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // paidByMonth: sum amount_paid grouped by MONTH(payment_date)
        $paidRows = DB::table('invoice_payments')
            ->selectRaw('MONTH(payment_date) as month, COALESCE(SUM(amount_paid),0) as total')
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ---------- PROJECT NOMINAL PER MONTH (berdasarkan start) ----------
        $projectNominalRows = DB::table('projects')
            ->selectRaw('MONTH(start) as month, COALESCE(SUM(total_biaya_project),0) as total')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $invoicedByMonth = [];
        $paidByMonth = [];
        $unpaidByMonth = [];
        $projectNominalByMonth = [];

        for ($m = 1; $m <= 12; $m++) {
            $inv = isset($invoicedRows[$m]) ? (float)$invoicedRows[$m] : 0.0;
            $paid = isset($paidRows[$m]) ? (float)$paidRows[$m] : 0.0;
            $projNom = isset($projectNominalRows[$m]) ? (float)$projectNominalRows[$m] : 0.0;

            // cap paid to invoiced to keep stacked bar total == invoiced
            $paidCapped = ($paid > $inv) ? $inv : $paid;
            $unpaid = $inv - $paidCapped;
            if ($unpaid < 0) $unpaid = 0.0;

            $invoicedByMonth[] = $inv;
            $paidByMonth[] = $paidCapped;
            $unpaidByMonth[] = $unpaid;
            $projectNominalByMonth[] = $projNom;
        }

        // ---------- DONUT CHART: PROJECT PROGRESS (aggregated) based on projects with start in year ----------
        $projects = DB::table('projects')
            ->select('id', 'kerjaan_id')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->get();

        $projectsTotal = $projects->count();
        $projectsFinished = 0;
        $projectsInProgress = 0;
        $sumPercentAll = 0;

        foreach ($projects as $project) {
            // ambil list proses untuk pekerjaan ini
            $listProses = DB::table('kerjaan_list_proses')
                ->where('kerjaan_id', $project->kerjaan_id)
                ->select('list_proses_id', 'urutan')
                ->get();

            $totalProses = $listProses->count();

            if ($totalProses === 0) {
                $persen = 0;
            } else {
                $prosesSelesaiQuery = DB::table('project_details')
                    ->where('project_id', $project->id)
                    ->where('status', 'done');

                $prosesSelesaiQuery->where(function ($q) use ($listProses) {
                    foreach ($listProses as $proses) {
                        $q->orWhere(function ($sub) use ($proses) {
                            $sub->where('kerjaan_list_proses_id', $proses->list_proses_id)
                                ->where('urutan_id', $proses->urutan);
                        });
                    }
                });

                $prosesSelesai = (int) $prosesSelesaiQuery->count();
                $persen = $totalProses > 0 ? round(($prosesSelesai / $totalProses) * 100) : 0;
            }

            $sumPercentAll += $persen;
            if ($persen >= 100) {
                $projectsFinished++;
            } else {
                $projectsInProgress++;
            }
        }

        $avgProgressPercent = $projectsTotal > 0 ? round($sumPercentAll / $projectsTotal, 2) : 0;

        // ---------- Years available dari projects berdasarkan start ----------
        $yearsRaw = DB::table('projects')
            ->selectRaw('YEAR(start) as year')
            ->whereNotNull('start')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->pluck('year')
            ->toArray();

        if (!in_array($year, $yearsRaw)) {
            $yearsRaw[] = $year;
            sort($yearsRaw);
        }

        // ---------- Return JSON ----------
        return response()->json([
            'year' => $year,
            'availableYears' => $yearsRaw,
            'summary' => [
                'totalProjects' => $totalProjects,
                'totalNominalProject' => $totalNominalProject,
                'totalPayments' => $totalPayments,
                'totalInvoiceAmount' => $totalInvoiceAmount,
                'outstanding' => $outstanding,
            ],
            'charts' => [
                'invoicedByMonth' => $invoicedByMonth,
                'paidByMonth' => $paidByMonth,
                'unpaidByMonth' => $unpaidByMonth,
                'projectProgress' => [
                    'total' => $projectsTotal,
                    'finished' => $projectsFinished,
                    'in_progress' => $projectsInProgress,
                    'avg_percent' => $avgProgressPercent,
                ],
                'projectNominalByMonth' => $projectNominalByMonth,
            ],
        ]);
    }
}
