<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
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
        $year     = (int) $request->query('year', Carbon::now()->year);
        $prevYear = $year - 1;
        $today    = Carbon::now();

        // ----------------- PROJECTS (berdasarkan start) -----------------
        $totalProjects = (int) DB::table('projects')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->count();

        // Project aktif: start di tahun ini & (end null OR end >= today)
        $activeProjects = (int) DB::table('projects')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->where(function ($q) use ($today) {
                $q->whereNull('end')
                    ->orWhere('end', '>=', $today);
            })
            ->count();

        // ----------------- TOTAL NILAI PROJECT -----------------
        // Tahun ini
        $totalProjectValue = (float) DB::table('projects')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->sum('total_biaya_project');

        // Tahun sebelumnya
        $totalProjectValuePrev = (float) DB::table('projects')
            ->whereNotNull('start')
            ->whereYear('start', $prevYear)
            ->sum('total_biaya_project');

        // Persentase perubahan total nilai project
        $totalProjectValueChangePct = null;
        if ($totalProjectValuePrev > 0) {
            $totalProjectValueChangePct = round(
                (($totalProjectValue - $totalProjectValuePrev) / $totalProjectValuePrev) * 100,
                2
            );
        }

        // ----------------- TOTAL PENGELUARAN (PAK & PAK ITEMS) -----------------
        // Ambil semua pak_id dari project di tahun ini yang terhubung PAK
        $pakIdsForYear = DB::table('projects')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->whereNotNull('pak_id')
            ->pluck('pak_id')
            ->filter()
            ->unique()
            ->toArray();

        $totalExpenses = 0.0;

        if (!empty($pakIdsForYear)) {
            // Total biaya dari pak_items.total_cost
            $totalItemCost = (float) DB::table('pak_items')
                ->whereIn('pak_id', $pakIdsForYear)
                ->sum('total_cost');

            // Total pajak dari paks.pph_23 + paks.ppn
            $taxSums = DB::table('paks')
                ->whereIn('id', $pakIdsForYear)
                ->selectRaw('
                COALESCE(SUM(pph_23), 0) as sum_pph_23,
                COALESCE(SUM(ppn), 0)    as sum_ppn
            ')
                ->first();

            $sumPph23 = (float) ($taxSums->sum_pph_23 ?? 0);
            $sumPpn   = (float) ($taxSums->sum_ppn ?? 0);

            // Total pengeluaran = total_cost + pph_23 + ppn
            $totalExpenses = $totalItemCost + $sumPph23 + $sumPpn;
        }

        // ----- TOTAL PENGELUARAN TAHUN SEBELUMNYA -----
        $pakIdsForPrevYear = DB::table('projects')
            ->whereNotNull('start')
            ->whereYear('start', $prevYear)
            ->whereNotNull('pak_id')
            ->pluck('pak_id')
            ->filter()
            ->unique()
            ->toArray();

        $totalExpensesPrev = 0.0;

        if (!empty($pakIdsForPrevYear)) {
            $totalItemCostPrev = (float) DB::table('pak_items')
                ->whereIn('pak_id', $pakIdsForPrevYear)
                ->sum('total_cost');

            $taxSumsPrev = DB::table('paks')
                ->whereIn('id', $pakIdsForPrevYear)
                ->selectRaw('
                COALESCE(SUM(pph_23), 0) as sum_pph_23,
                COALESCE(SUM(ppn), 0)    as sum_ppn
            ')
                ->first();

            $sumPph23Prev = (float) ($taxSumsPrev->sum_pph_23 ?? 0);
            $sumPpnPrev   = (float) ($taxSumsPrev->sum_ppn ?? 0);

            $totalExpensesPrev = $totalItemCostPrev + $sumPph23Prev + $sumPpnPrev;
        }

        // Persentase perubahan total pengeluaran
        $totalExpensesChangePct = null;
        if ($totalExpensesPrev > 0) {
            $totalExpensesChangePct = round(
                (($totalExpenses - $totalExpensesPrev) / $totalExpensesPrev) * 100,
                2
            );
        }

        // ----------------- PENDAPATAN BERSIH -----------------
        // Tahun ini
        $netIncome = $totalProjectValue - $totalExpenses;

        // Tahun sebelumnya
        $netIncomePrev = $totalProjectValuePrev - $totalExpensesPrev;

        // Persentase perubahan net income
        $netIncomeChangePct = null;
        if ($netIncomePrev > 0) {
            $netIncomeChangePct = round(
                (($netIncome - $netIncomePrev) / $netIncomePrev) * 100,
                2
            );
        }

        // ----------------- DATA UNTUK CHART: TOTAL NILAI PROJECT PER BULAN -----------------

        $projectNominalRows = DB::table('projects')
            ->selectRaw('MONTH(start) as month, COALESCE(SUM(total_biaya_project),0) as total')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $projectNominalByMonth = [];

        for ($m = 1; $m <= 12; $m++) {
            $projNom = isset($projectNominalRows[$m]) ? (float) $projectNominalRows[$m] : 0.0;
            $projectNominalByMonth[] = $projNom;
        }

        // ---------- DONUT CHART: PROJECT PROGRESS (semua project di tahun ini) ----------
        $projects = DB::table('projects')
            ->select('id', 'kerjaan_id')
            ->whereNotNull('start')
            ->whereYear('start', $year)
            ->get();

        $projectsTotal      = $projects->count();
        $projectsFinished   = 0;
        $projectsInProgress = 0;
        $sumPercentAll      = 0;

        foreach ($projects as $project) {
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
                    ->where('status', 'done')
                    ->where(function ($q) use ($listProses) {
                        foreach ($listProses as $proses) {
                            $q->orWhere(function ($sub) use ($proses) {
                                $sub->where('kerjaan_list_proses_id', $proses->list_proses_id)
                                    ->where('urutan_id', $proses->urutan);
                            });
                        }
                    });

                $prosesSelesai = (int) $prosesSelesaiQuery->count();
                $persen        = $totalProses > 0
                    ? round(($prosesSelesai / $totalProses) * 100)
                    : 0;
            }

            $sumPercentAll += $persen;

            if ($persen >= 100) {
                $projectsFinished++;
            } else {
                $projectsInProgress++;
            }
        }

        $avgProgressPercent = $projectsTotal > 0
            ? round($sumPercentAll / $projectsTotal, 2)
            : 0;

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
            'year'           => $year,
            'availableYears' => $yearsRaw,
            'summary'        => [
                'totalProjects'                 => $totalProjects,
                'activeProjects'                => $activeProjects,

                // Total nilai project + perbandingan
                'totalProjectValue'             => $totalProjectValue,
                'totalProjectValuePrev'         => $totalProjectValuePrev,
                'totalProjectValueChangePct'    => $totalProjectValueChangePct,

                // Total pengeluaran + perbandingan
                'totalExpenses'                 => $totalExpenses,
                'totalExpensesPrev'             => $totalExpensesPrev,
                'totalExpensesChangePct'        => $totalExpensesChangePct,

                // Pendapatan bersih + perbandingan
                'netIncome'                     => $netIncome,
                'netIncomePrev'                 => $netIncomePrev,
                'netIncomeChangePct'            => $netIncomeChangePct,
            ],
            'charts' => [
                'projectProgress'       => [
                    'total'       => $projectsTotal,
                    'finished'    => $projectsFinished,
                    'in_progress' => $projectsInProgress,
                    'avg_percent' => $avgProgressPercent,
                ],
                'projectNominalByMonth' => $projectNominalByMonth,
            ],
        ]);
    }




    public function projectsData(Request $request)
    {
        // ambil tahun dari query param (fallback current year)
        $year = (int) $request->query('year', Carbon::now()->year);

        // query dasar: projects yang start di tahun terpilih, join ke users untuk client name
        $query = DB::table('projects')
            ->join('users', 'projects.client_id', '=', 'users.id')
            ->select(
                'projects.id',
                'projects.no_project',
                'projects.nama_project',
                'users.name as client_name',
                'projects.start',
                'projects.end',
                'projects.total_biaya_project'
            )
            ->whereNotNull('projects.start')
            ->whereYear('projects.start', $year);

        // kalau mau filter hanya project aktif (seperti sebelumnya): uncomment
        // ->where(function($q){ $q->whereNull('projects.end')->orWhere('projects.end', '>=', now()); })

        return DataTables::of($query)
            ->editColumn('start', function ($row) {
                return $row->start ? Carbon::parse($row->start)->format('Y-m-d') : '-';
            })
            ->editColumn('end', function ($row) {
                return $row->end ? Carbon::parse($row->end)->format('Y-m-d') : '-';
            })
            ->editColumn('total_biaya_project', function ($row) {
                return 'Rp ' . number_format((float)$row->total_biaya_project, 0, ',', '.');
            })
            ->addColumn('status', function ($row) {
                $today = now();
                $end = $row->end ? Carbon::parse($row->end) : null;
                if (!$row->end || ($end && $end >= $today)) {
                    return '<span class="badge badge-success">Aktif</span>';
                }
                return '<span class="badge badge-danger">Selesai</span>';
            })
            // ->addColumn('action', function($row){
            //     // kalau mau row clickable, bisa return link atau data-href
            //     $url = route('projects.show', $row->id);
            //     return '<a href="'. $url .'" class="btn btn-sm btn-outline-primary">Lihat</a>';
            // })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}
