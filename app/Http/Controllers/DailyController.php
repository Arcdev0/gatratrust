<?php

namespace App\Http\Controllers;

use App\Models\Daily;
use App\Models\DailyComment;
use App\Models\DailyItem;
use App\Models\ListProses;
use App\Models\ProjectTbl;
use App\Models\TimelineTahunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DailyController extends Controller
{
    /**
     * Display the index page.
     */
    public function index()
    {
        return view('daily.index');
    }


    public function getProjectData()
    {
        $userId = auth()->id();

        // 1) Project user + prosesnya
        $projects = ProjectTbl::with(['kerjaan.prosesList'])
            ->join('project_user', 'projects.id', '=', 'project_user.project_id')
            ->where('project_user.user_id', $userId)
            ->select('projects.*')
            ->get();

        // project_id => list proses
        $projectProcesses = $projects->mapWithKeys(function ($p) {
            if (!$p->kerjaan) {
                return [$p->id => []];
            }

            $list = $p->kerjaan->prosesList->map(function ($proses) {
                return [
                    'id'     => $proses->id,
                    'nama'   => $proses->nama_proses,
                    'urutan' => $proses->pivot->urutan,
                    'hari'   => $proses->pivot->hari,
                ];
            });

            return [$p->id => $list->values()];
        });

        // 2) Cari proses ongoing & selesai (untuk disable / hide di form)
        $ongoing = DailyItem::where('status', false)
            ->whereNotNull('project_id')
            ->whereNotNull('proses_id')
            ->get()
            ->groupBy('project_id');

        $allWorked = DailyItem::whereNotNull('project_id')
            ->whereNotNull('proses_id')
            ->get()
            ->groupBy('project_id');

        $doneProcessesByProject = [];
        $completedProjects      = [];

        foreach ($allWorked as $projectId => $items) {
            $allProsesIds = $items->pluck('proses_id')->unique()->values()->all();

            $ongoingForProject = $ongoing->get($projectId) ?? collect();
            $ongoingIds = $ongoingForProject->pluck('proses_id')->unique()->values()->all();

            // proses selesai = pernah dikerjakan - yang masih ongoing
            $doneIds = array_values(array_diff($allProsesIds, $ongoingIds));
            if (!empty($doneIds)) {
                $doneProcessesByProject[$projectId] = $doneIds;
            }
        }

        // project yang semua prosesnya selesai
        foreach ($projects as $p) {
            $prosesList = $projectProcesses[$p->id] ?? collect();
            $allProsesIds = collect($prosesList)->pluck('id')->all();

            if (empty($allProsesIds)) continue;

            $doneIdsForProject = $doneProcessesByProject[$p->id] ?? [];

            if (!array_diff($allProsesIds, $doneIdsForProject)) {
                $completedProjects[] = $p->id;
            }
        }

        // Carry over dari daily terakhir user ini
        $lastDaily = Daily::with(['items' => function ($q) {
            $q->where('status', false);
        }])
            ->where('user_id', $userId)
            ->orderBy('tanggal', 'desc')
            ->first();

        $carryOverItems = [];

        if ($lastDaily) {
            $carryOverItems = $lastDaily->items->map(function ($item) {
                return [
                    'jenis'          => $item->jenis,
                    'project_id'     => $item->project_id,
                    'proses_id'      => $item->proses_id,
                    'pekerjaan_umum' => $item->pekerjaan_umum,
                    'keterangan'     => $item->keterangan,
                    'status'         => $item->status ? 'ok' : 'belum',
                ];
            })->values()->toArray();
        }

        return response()->json([
            'projects'               => $projects,
            'projectProcesses'       => $projectProcesses,
            'doneProcessesByProject' => $doneProcessesByProject,
            'completedProjects'      => $completedProjects,
            'carryOverItems'         => $carryOverItems,
        ]);
    }


    public function getList(Request $request)
    {
        $tanggal = $request->get('tanggal');

        $query = Daily::with(['user'])
            ->withCount('comments')
            ->orderBy('tanggal', 'desc');

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        } else {
            $query->whereDate('tanggal', now());
        }

        // 🔹 Map global (untuk tampilan list semua user)
        $projectMap = ProjectTbl::pluck('no_project', 'id');        // [id => no_project]
        $prosesMap  = ListProses::pluck('nama_proses', 'id');       // [id => nama_proses]

        // ======================================================
        // 1) Hitung kombinasi project+proses yang sudah pernah OK
        // ======================================================
        $okItems = DailyItem::where('status', true) // true = OK
            ->whereNotNull('project_id')
            ->whereNotNull('proses_id')
            ->get();

        $hasOk = []; // key: "project_id|proses_id" => true
        foreach ($okItems as $it) {
            $key = $it->project_id . '|' . $it->proses_id;
            $hasOk[$key] = true;
        }

        // ======================================================
        // 2) Ambil pending (status = false), filter & deduplicate
        // ======================================================
        $pendingQuery = DailyItem::where('status', false)
            ->with([
                'daily',
                'project.pics',
            ]);

        if ($tanggal) {
            $pendingQuery->whereHas('daily', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', '<=', $tanggal);
            });
        }

        // supaya "yang pertama dibuat" yang dipakai
        $pendingRaw = $pendingQuery
            ->orderBy('created_at', 'asc')
            ->get();

        $seenKeys    = [];          // untuk deduplicate pending per (project+proses)
        $pendingTasks = collect();

        foreach ($pendingRaw as $item) {

            // KEY hanya untuk yang punya project & proses
            if ($item->project_id && $item->proses_id) {
                $key = $item->project_id . '|' . $item->proses_id;

                // 🔴 Kalau kombinasi ini pernah OK → SKIP semua pending-nya
                if (!empty($hasOk[$key])) {
                    continue;
                }

                // 🟡 Kalau pending untuk key ini sudah pernah dimasukkan → SKIP duplikat
                if (!empty($seenKeys[$key])) {
                    continue;
                }

                // tandai sudah dipakai
                $seenKeys[$key] = true;
            }

            // project_no
            $projectNo = $item->project_id
                ? ($projectMap[$item->project_id] ?? null)
                : null;

            // PIC
            $picNames = [];
            if ($item->jenis === 'project') {
                if ($item->project && $item->project->pics) {
                    $picNames = $item->project->pics->pluck('name')->values()->all();
                }
            } else {
                if ($item->daily && $item->daily->user) {
                    $picNames = [$item->daily->user->name];
                }
            }

            $pendingTasks->push([
                'id'             => $item->id,
                'tanggal'        => optional($item->daily)->tanggal,
                'jenis'          => $item->jenis,
                'project_id'     => $item->project_id,
                'project_no'     => $projectNo,
                'proses_id'      => $item->proses_id,
                'proses'         => $item->proses_id ? ($prosesMap[$item->proses_id] ?? null) : null,
                'pekerjaan_umum' => $item->pekerjaan_umum,
                'keterangan'     => $item->keterangan,
                'pic'            => $picNames,
                'status'         => $item->status,
            ]);
        }

        return response()->json([
            'data'          => $query->get(),
            'auth_user_id'  => auth()->id(),
            'project_map'   => $projectMap,
            'proses_map'    => $prosesMap,
            'pending_tasks' => $pendingTasks->values(),
        ]);
    }



    public function dataDailyComments(Daily $daily)
    {
        return $daily->comments()->with('user')->latest()->get();
    }

    public function storeDailyComments(Request $request, $daily)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        DailyComment::create([
            'daily_id' => $daily,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyDailyComments(DailyComment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }


    /**
     * Store a newly created daily record.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'user_id'        => 'required|exists:users,id',
            'tanggal'        => 'required|date',
            'achievements'   => 'required|array|min:1',
            'achievements.*.jenis'        => 'required|in:project,umum',
            'achievements.*.project_id'   => 'nullable|exists:projects,id',
            'achievements.*.proses_id'    => 'nullable|exists:list_proses,id',
            'achievements.*.pekerjaan_umum' => 'nullable|string',
            'achievements.*.status'       => 'required|in:ok,belum',

            'tomorrows'      => 'nullable|array',
            'tomorrows.*.jenis'      => 'required_with:tomorrows|in:project,umum',
            'tomorrows.*.project_id' => 'nullable|exists:projects,id',
            'tomorrows.*.proses_id'  => 'nullable|exists:list_proses,id',

            'upload_file'    => 'nullable|file|max:2048',
        ]);

        // ----- 1. Siapkan header Daily -----
        $data = [
            'user_id'       => $request->user_id,
            'tanggal'       => $request->tanggal,
            // simpan JSON untuk kompatibilitas dengan struktur lama
            'plan_today'    => json_encode($request->input('achievements', [])),
            'plan_tomorrow' => json_encode($request->input('tomorrows', [])),
            'problem'       => null, // sementara tidak dipakai
        ];

        if ($request->hasFile('upload_file')) {
            $data['upload_file'] = $request->file('upload_file')->store('uploads/daily', 'public');
        }

        $daily = Daily::create($data);

        // ----- 2. Simpan detail ke daily_items -----
        $achievements = $request->input('achievements', []);

        foreach ($achievements as $item) {
            $projectId  = $item['project_id'] ?? null;
            $prosesId   = $item['proses_id'] ?? null;
            $jenis      = $item['jenis'];
            $statusText = $item['status'] ?? 'ok';

            // Ambil kerjaan_id dari ProjectTbl
            $kerjaanId = null;
            if ($projectId) {
                $project   = ProjectTbl::find($projectId);
                $kerjaanId = $project ? $project->kerjaan_id : null;
            }

            DailyItem::create([
                'daily_id'       => $daily->id,
                'jenis'          => $jenis,                  // 'project' / 'umum'
                'project_id'     => $projectId,
                'kerjaan_id'     => $kerjaanId,              // diambil dari ProjectTbl
                'proses_id'      => $prosesId,               // dari ListProses
                'pekerjaan_umum' => $item['pekerjaan_umum'] ?? null,
                'keterangan'     => $item['keterangan'] ?? null,
                'status'         => $statusText === 'ok',    // boolean: true=ok, false=belum
            ]);
        }

        // (Opsional) Kalau mau simpan "tomorrows" ke tabel lain atau ke daily_items juga,
        // bisa ditambah loop kedua di sini.

        return response()->json([
            'message' => 'Daily created successfully',
            'data'    => $daily,
        ], 201);
    }

    /**
     * Show the form for editing the specified daily record.
     */
    public function edit($id)
    {
        $daily = Daily::findOrFail($id);
        return response()->json($daily);
    }

    /**
     * Update the specified daily record.
     */
    public function update(Request $request, $id)
    {
        $daily = Daily::findOrFail($id);

        $request->validate([
            'tanggal'        => 'required|date',

            // SAMA seperti di store()
            'achievements'   => 'required|array|min:1',
            'achievements.*.jenis'          => 'required|in:project,umum',
            'achievements.*.project_id'     => 'nullable|exists:projects,id',
            'achievements.*.proses_id'      => 'nullable|exists:list_proses,id',
            'achievements.*.pekerjaan_umum' => 'nullable|string',
            'achievements.*.status'         => 'required|in:ok,belum',

            'tomorrows'      => 'nullable|array',
            'tomorrows.*.jenis'      => 'required_with:tomorrows|in:project,umum',
            'tomorrows.*.project_id' => 'nullable|exists:projects,id',
            'tomorrows.*.proses_id'  => 'nullable|exists:list_proses,id',

            'upload_file'    => 'nullable|file|max:2048',
        ]);

        // ----- 1. Update header Daily (master) -----
        $data = [
            'tanggal'       => $request->tanggal,
            // simpan JSON supaya kompatibel dengan format lama
            'plan_today'    => json_encode($request->input('achievements', [])),
            'plan_tomorrow' => json_encode($request->input('tomorrows', [])),
            'problem'       => null, // sekarang memang tidak dipakai lagi
        ];

        // handle file
        if ($request->hasFile('upload_file')) {
            // hapus file lama kalau ada
            if ($daily->upload_file && Storage::disk('public')->exists($daily->upload_file)) {
                Storage::disk('public')->delete($daily->upload_file);
            }

            $data['upload_file'] = $request->file('upload_file')->store('uploads/daily', 'public');
        }

        $daily->update($data);

        // ----- 2. Reset & simpan ulang detail (daily_items) -----
        // untuk simpel: hapus semua item lama, lalu insert ulang dari achievements baru
        $daily->items()->delete();

        $achievements = $request->input('achievements', []);

        foreach ($achievements as $item) {
            $projectId  = $item['project_id'] ?? null;
            $prosesId   = $item['proses_id'] ?? null;
            $jenis      = $item['jenis'];
            $statusText = $item['status'] ?? 'ok';

            // Ambil kerjaan_id dari ProjectTbl (kalau ada project)
            $kerjaanId = null;
            if ($projectId) {
                $project   = ProjectTbl::find($projectId);
                $kerjaanId = $project ? $project->kerjaan_id : null;
            }

            DailyItem::create([
                'daily_id'       => $daily->id,
                'jenis'          => $jenis,                       // 'project' / 'umum'
                'project_id'     => $projectId,
                'kerjaan_id'     => $kerjaanId,                   // diambil dari ProjectTbl
                'proses_id'      => $prosesId,                    // dari ListProses
                'pekerjaan_umum' => $item['pekerjaan_umum'] ?? null,
                'keterangan'     => $item['keterangan'] ?? null,
                'status'         => $statusText === 'ok',         // boolean: true=ok, false=belum
            ]);
        }

        // (opsional) kalau nanti Tuan mau juga simpan "tomorrows" ke tabel lain,
        // bisa ditambah loop di bawah ini.

        return response()->json([
            'message' => 'Daily updated successfully',
            'data'    => $daily,
        ]);
    }

    /**
     * Delete the specified daily record.
     */
    public function destroy($id)
    {
        $daily = Daily::with('items')->findOrFail($id);


        if ($daily->upload_file && Storage::disk('public')->exists($daily->upload_file)) {
            Storage::disk('public')->delete($daily->upload_file);
        }

        $daily->items()->delete();

        $daily->delete();

        return response()->json(['message' => 'Daily deleted successfully']);
    }

    /**
     * Ambil semua data timeline (opsional filter tahun)
     */
    public function getDataTimeline(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $timeline = TimelineTahunan::where('tahun', $tahun)
            ->orderBy('start_date', 'asc')
            ->get();

        return response()->json($timeline);
    }

    /**
     * Tambah data timeline baru
     */
    public function tambahListTimeline(Request $request)
    {
        $validated = $request->validate([
            'tahun'       => 'required|integer',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'required|string',
            'is_action'   => 'nullable|boolean'
        ]);

        $timeline = TimelineTahunan::create($validated);

        return response()->json([
            'message' => 'Data timeline berhasil ditambahkan',
            'data'    => $timeline
        ]);
    }

    /**
     * Update data timeline berdasarkan ID
     */
    public function updateListTimeline(Request $request, $id)
    {
        $timeline = TimelineTahunan::findOrFail($id);

        $validated = $request->validate([
            'tahun'       => 'required|integer',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'required|string',
            'is_action'   => 'nullable|boolean'
        ]);

        $timeline->update($validated);

        return response()->json([
            'message' => 'Data timeline berhasil diperbarui',
            'data'    => $timeline
        ]);
    }

    /**
     * Hapus data timeline berdasarkan ID
     */
    public function deleteListTimeline($id)
    {
        $timeline = TimelineTahunan::findOrFail($id);
        $timeline->delete();

        return response()->json([
            'message' => 'Data timeline berhasil dihapus'
        ]);
    }
}
