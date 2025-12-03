<?php

namespace App\Http\Controllers;

use App\Models\Daily;
use App\Models\DailyComment;
use App\Models\DailyItem;
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
        $userId = auth()->user()->id;

        $projects = ProjectTbl::with(['kerjaan.prosesList'])
            ->join('project_user', 'projects.id', '=', 'project_user.project_id')
            ->where('project_user.user_id', $userId)
            ->select('projects.*')
            ->get();

        // Map: project_id => list proses (dari kerjaan yang terkait)
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

        // ====== ambil daily terakhir user + item yang statusnya BELUM ======
        $lastDaily = Daily::with(['items' => function ($q) {
            $q->where('status', false); // status = 0 / belum
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
                ];
            })->values()->toArray();
        }

        return view('daily.index', [
            'projects'         => $projects,
            'projectProcesses' => $projectProcesses,
            'carryOverItems'   => $carryOverItems,
        ]);
    }


    public function getList(Request $request)
    {
        $tanggal = $request->get('tanggal');

        $query = Daily::with(['user'])
            ->withCount('comments') // tambahkan ini
            ->orderBy('tanggal', 'desc');

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        } else {
            $query->whereDate('tanggal', now());
        }

        return response()->json([
            'data' => $query->get(),
            'auth_user_id' => auth()->id(),
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
            'tanggal' => 'required|date',
            'plan_today' => 'required',
            'plan_tomorrow' => 'nullable',
            'problem' => 'nullable',
            'upload_file' => 'nullable|file|max:2048',
        ]);

        $data = $request->only(['tanggal', 'plan_today', 'plan_tomorrow', 'problem']);

        if ($request->hasFile('upload_file')) {
            // Delete old file
            if ($daily->upload_file && Storage::disk('public')->exists($daily->upload_file)) {
                Storage::disk('public')->delete($daily->upload_file);
            }
            $data['upload_file'] = $request->file('upload_file')->store('uploads/daily', 'public');
        }

        $daily->update($data);

        return response()->json([
            'message' => 'Daily updated successfully',
            'data' => $daily
        ]);
    }

    /**
     * Delete the specified daily record.
     */
    public function destroy($id)
    {
        $daily = Daily::findOrFail($id);

        // Delete file if exists
        if ($daily->upload_file && Storage::disk('public')->exists($daily->upload_file)) {
            Storage::disk('public')->delete($daily->upload_file);
        }

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
