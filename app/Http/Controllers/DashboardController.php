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
            ->selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
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

        // kirim ke view
        return view('dashboard.index', [
            'availableYears' => $availableYears,
            'currentYear' => $currentYear,
        ]);
    }

    public function getData(Request $request)
    {
        $year = (int) $request->query('year', Carbon::now()->year);

        // Total Project (filter by created_at year; ubah field jika perlu)
        $totalProjects = (int) DB::table('projects')
            ->whereYear('created_at', $year)
            ->count();

        // Total nilai project (sum)
        $totalNominalProject = (float) DB::table('projects')
            ->whereYear('created_at', $year)
            ->sum('total_biaya_project');

        // Total pendapatan = sum invoice_payments.amount_paid pada tahun
        $totalPayments = (float) DB::table('invoice_payments')
            ->whereYear('payment_date', $year)
            ->sum('amount_paid');

        // Total invoice amount di tahun (dipakai untuk outstanding)
        $totalInvoiceAmount = (float) DB::table('invoices')
            ->whereYear('date', $year)
            ->sum('net_total');

        $outstanding = $totalInvoiceAmount - $totalPayments;

        // ---------- BAR CHART: paid vs unpaid per month ----------
        // invoicedByMonth: sum net_total grouped by MONTH(date)
        $invoicedRows = DB::table('invoices')
            ->selectRaw('MONTH(date) as month, SUM(net_total) as total')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // paidByMonth: sum amount_paid grouped by MONTH(payment_date)
        $paidRows = DB::table('invoice_payments')
            ->selectRaw('MONTH(payment_date) as month, SUM(amount_paid) as total')
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();


        $projectNominalRows = DB::table('projects')
            ->selectRaw('MONTH(created_at) as month, COALESCE(SUM(total_biaya_project),0) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();


        $projectNominalByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $projectNominalByMonth[] = isset($projectNominalRows[$m]) ? (float)$projectNominalRows[$m] : 0.0;
        }

        $invoicedByMonth = [];
        $paidByMonth = [];
        $unpaidByMonth = [];

        for ($m = 1; $m <= 12; $m++) {
            $inv = isset($invoicedRows[$m]) ? (float)$invoicedRows[$m] : 0.0;
            $paid = isset($paidRows[$m]) ? (float)$paidRows[$m] : 0.0;

            // Jika pembayaran melebihi invoiced di bulan itu, cap paid ke invoiced.
            // Ini memastikan stacked bar total == invoiced. Jika mau tampilkan overflow, ubah logika.
            if ($paid > $inv) {
                $paidCapped = $inv;
            } else {
                $paidCapped = $paid;
            }

            $unpaid = $inv - $paidCapped;
            if ($unpaid < 0) $unpaid = 0.0;

            $invoicedByMonth[] = $inv;
            $paidByMonth[] = $paidCapped;
            $unpaidByMonth[] = $unpaid;
        }

        // ---------- DONUT CHART: PROJECT PROGRESS (aggregated) ----------
        // Kita ambil semua project di tahun yang dipilih
        $projects = DB::table('projects')
            ->select('id', 'kerjaan_id')
            ->whereYear('created_at', $year)
            ->get();

        $projectsTotal = $projects->count();
        $projectsFinished = 0;
        $projectsInProgress = 0;
        $sumPercentAll = 0;

        // NOTE: loop per project. Jika dataset besar, nanti kita bisa optimalkan.
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
                // hitung berapa proses yang sudah "done" untuk project ini
                $prosesSelesaiQuery = DB::table('project_details')
                    ->where('project_id', $project->id)
                    ->where('status', 'done');

                // build where clause untuk setiap list proses (OR conditions)
                // Note: ini mirip kode yang kamu kirim — kalau struktur DB berbeda, ubah sesuai
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

        // ---------- Years available (opsional) ----------
        $yearsRaw = DB::table('projects')
            ->selectRaw('YEAR(created_at) as year')
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
                // bar chart stacked: paid (hijau) dan unpaid (merah)
                'invoicedByMonth' => $invoicedByMonth, // total invoiced per month (optional)
                'paidByMonth' => $paidByMonth,         // hijau
                'unpaidByMonth' => $unpaidByMonth,     // merah
                // donut chart: projects progress (green = finished, red = in progress)
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
