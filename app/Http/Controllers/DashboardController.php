<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
//    public function index()
//    {
//         $totalProjects = \DB::table('projects')->count();

//         $activeProjects = \DB::table('projects')
//         ->where('end', '>=', now())
//         ->count();

//         // Total invoice
//         $totalInvoices = \DB::table('invoices')->count();

//         // Invoice status open/close
//         $invoiceStatus = \DB::table('invoices')
//         ->select('status', \DB::raw('COUNT(*) as total'))
//         ->groupBy('status')
//         ->pluck('total', 'status');

//         $totalPayments = \DB::table('invoice_payments')->sum('amount_paid');

//         // Total tagihan (dari invoice)
//         $totalInvoiceAmount = \DB::table('invoices')->sum('net_total');

//         // Outstanding payment (belum dibayar)
//         $outstanding = $totalInvoiceAmount - $totalPayments;

//         // Revenue bulanan (sum net_total per bulan berdasarkan invoice status = close)
//         $revenueByMonth = \DB::table('invoices')
//             ->selectRaw('MONTH(date) as bulan, SUM(net_total) as total')
//             ->whereYear('date', now()->year)
//             ->where('status', 'close')
//             ->groupBy('bulan')
//             ->pluck('total', 'bulan');

//         // Project list (ambil yang masih aktif)
//         $projects = \DB::table('projects')
//             ->join('users', 'projects.client_id', '=', 'users.id')
//             ->select('projects.*', 'users.name as client_name')
//             ->where('projects.end', '>=', now())
//             ->get();

//         return view('dashboard.index', compact(
//         'totalProjects',
//         'activeProjects',
//         'totalInvoices',
//         'invoiceStatus',
//         'totalPayments',
//         'outstanding',
//         'revenueByMonth',
//         'projects'
//     ));
//    }

    public function index()
    {
        // Summary counts
        $totalProjects = DB::table('projects')->count();
        $activeProjects = DB::table('projects')->where('end', '>=', now())->count();

        $totalInvoices = DB::table('invoices')->count();
        $invoiceStatus = DB::table('invoices')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalPayments = (float) DB::table('invoice_payments')->sum('amount_paid');
        $totalInvoiceAmount = (float) DB::table('invoices')->sum('net_total');
        $outstanding = $totalInvoiceAmount - $totalPayments;

        // Revenue per month (current year) for line chart
        $revenueByMonth = DB::table('invoices')
            ->selectRaw('MONTH(date) as bulan, SUM(net_total) as total')
            ->whereYear('date', now()->year)
            ->where('status', 'close')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // Projects list (active)
        // NOTE: asumsi tabel clients ada, kalau beda sesuaikan joinnya
        $projects = DB::table('projects')
            ->join('users', 'projects.client_id', '=', 'users.id')
            ->select('projects.*', 'users.name as client_name')
            ->where('projects.end', '>=', now())
            ->get();

        // Invoice counts grouped by YEAR(date) and status -> untuk dropdown tahun
        $invoiceYearStatus = DB::table('invoices')
            ->selectRaw('YEAR(date) as year, status, COUNT(*) as total')
            ->groupBy('year', 'status')
            ->orderBy('year')
            ->get();

        $invoiceDataByYear = [];
        foreach ($invoiceYearStatus as $row) {
            $y = (int)$row->year;
            if (!isset($invoiceDataByYear[$y])) {
                $invoiceDataByYear[$y] = ['open' => 0, 'close' => 0];
            }
            $invoiceDataByYear[$y][$row->status] = (int)$row->total;
        }

        // Pastikan tahun sekarang ada di data (fallback 0 jika tidak ada invoice tahun ini)
        $currentYear = now()->year;
        if (!isset($invoiceDataByYear[$currentYear])) {
            $invoiceDataByYear[$currentYear] = [
                'open'  => $invoiceStatus['open'] ?? 0,
                'close' => $invoiceStatus['close'] ?? 0,
            ];
        }

        $currentMonth = now()->month;
        $lastMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $lastMonthYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

        // Total pendapatan bulan lalu
        $lastMonthRevenue = DB::table('invoice_payments')
            ->whereMonth('payment_date', $lastMonth)
            ->whereYear('payment_date', $lastMonthYear)
            ->sum('amount_paid');

        // Total pendapatan bulan ini
        $currentMonthRevenue = DB::table('invoice_payments')
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('amount_paid');

        // Hitung pertumbuhan (growth)
        if ($lastMonthRevenue > 0) {
            $growthPercentage = round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2);
        } else {
            $growthPercentage = null; // belum ada data untuk dibandingkan
        }

        // Progress percentages (hindari division by zero)
        $totalProjectsPct = $totalProjects ? round($activeProjects / $totalProjects * 100) : 0;
        $invoiceClosed = $invoiceStatus['close'] ?? 0;
        $invoicesPct = $totalInvoices ? round($invoiceClosed / $totalInvoices * 100) : 0;
        $paymentsPct = $totalInvoiceAmount ? round($totalPayments / $totalInvoiceAmount * 100) : 0;
        $outstandingPct = $totalInvoiceAmount ? round($outstanding / $totalInvoiceAmount * 100) : 0;

        return view('dashboard.index', compact(
            'totalProjects',
            'activeProjects',
            'totalInvoices',
            'invoiceStatus',
            'totalPayments',
            'totalInvoiceAmount',
            'outstanding',
            'revenueByMonth',
            'projects',
            'invoiceDataByYear',
            'totalProjectsPct',
            'invoicesPct',
            'paymentsPct',
            'outstandingPct',
            'growthPercentage'
        ));
    }
}
