<?php

namespace App\Http\Controllers;

use App\Models\Daily;
use App\Models\DailyComment;
use App\Models\DailyItem;
use App\Models\KerjaanListProses;
use App\Models\ListProses;
use App\Models\NewDaily;
use App\Models\ProjectTbl;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\TimelineTahunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DailyController extends Controller
{
    /**
     * Display the index page.
     */
    public function index()
    {
        return view('daily.index');
    }


    public function pendingTasks(Request $request)
    {
        $user = Auth::user();

        // Optional filter tanggal (misal untuk tampilkan pending sampai tanggal tertentu)
        $tanggal = $request->input('tanggal'); // YYYY-MM-DD

        $query = Task::query()
            ->with([
                'user:id,name',
                'project:id,no_project,nama_project', // sesuaikan kolom project kamu
                'latestLog' => function ($q) use ($tanggal) {
                    if ($tanggal) $q->whereDate('tanggal', '<=', $tanggal);
                }
            ])
            ->open();

        // kalau kamu ingin pending table hanya task milik user login:
        // $query->forUser($user->id);

        // kalau ingin pending task semua user (seperti di view ada kolom PIC):
        // biarkan tanpa filter user

        // Kalau tanggal filter ada, pastikan task punya log sebelum/di tanggal itu
        if ($tanggal) {
            $query->whereHas('logs', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', '<=', $tanggal);
            });
        }

        $tasks = $query
            ->orderByDesc('updated_at')
            ->get();

        // Bentuk data untuk DataTable (simple)
        $data = $tasks->map(function ($task, $idx) {
            $last = $task->latestLog;

            return [
                'no'        => $idx + 1,
                'tanggal'   => optional($last?->tanggal)->format('Y-m-d') ?? optional($task->started_at)->format('Y-m-d'),
                'project'   => $task->jenis === 'project'
                    ? (optional($task->project)->no_project ?? ('#' . $task->project_id))
                    : '-',
                'pic'       => optional($task->user)->name ?? '-',
                'jenis'     => $task->jenis,
                'pekerjaan' => $task->jenis === 'umum'
                    ? ($task->judul_umum ?? '-')
                    : 'Project Task',
                'keterangan' => $last?->keterangan ?? ($task->deskripsi ?? '-'),
                'status'    => $task->status, // open
                'status_hari_ini' => $last?->status_hari_ini ?? 'lanjut',
                'task_id'   => $task->id,
            ];
        });

        return response()->json([
            'count' => $data->count(),
            'data'  => $data,
        ]);
    }


    public function myOpenTasks(Request $request)
    {
        $userId = Auth::id();

        $tasks = Task::query()
            ->forUser($userId)
            ->open()
            ->with(['project:id,no_project,nama_project', 'latestLog'])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'data' => $tasks
        ]);
    }

    public function projectData()
    {
        $userId = Auth::id();

        $projects = ProjectTbl::query()
            ->select('id', 'no_project', 'kerjaan_id')
            ->whereHas('pics', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->orderBy('no_project')
            ->get();

        $kerjaanIds = $projects->pluck('kerjaan_id')->filter()->unique()->values();

        $processRows = KerjaanListProses::query()
            ->with(['listProses:id,nama_proses', 'kerjaan:id,nama_kerjaan'])
            ->whereIn('kerjaan_id', $kerjaanIds)
            ->orderBy('urutan')
            ->get();

        $processByKerjaan = $processRows->groupBy('kerjaan_id')->map(function ($rows) {
            return $rows->map(function ($r) {
                return [
                    'id' => $r->id,
                    'urutan' => $r->urutan,
                    'nama_proses' => $r->listProses?->nama_proses,
                    'nama_kerjaan' => $r->kerjaan?->nama_kerjaan,
                ];
            })->values();
        });

        $projectProcesses = [];
        foreach ($projects as $p) {
            $projectProcesses[$p->id] = $processByKerjaan[$p->kerjaan_id] ?? [];
        }

        return response()->json([
            'projects' => $projects,
            'projectProcesses' => $projectProcesses,
        ]);
    }


    public function listCards(Request $request)
    {
        $tanggal = $request->input('tanggal');
        if (!$tanggal) $tanggal = now()->toDateString();

        $dailies = NewDaily::query()
            ->with([
                'user:id,name',
                'taskLogs' => function ($q) {
                    $q->with([
                        'task.project',
                        'task.user',
                        'task.prosesRel.kerjaan',
                        'task.prosesRel.listProses',
                    ])->orderBy('id', 'asc');
                }
            ])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();


        // tambahkan field tampilan supaya tidak ISO
        $dailies->each(function ($d) {
            $d->tanggal_display = optional($d->tanggal)->format('d-m-Y');
        });

        return response()->json([
            'auth_user_id' => Auth::id(),
            'tanggal'      => $tanggal,
            'data'         => $dailies,
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
        $userId = Auth::id();


        if ($request->filled('items') && is_string($request->items)) {
            $decoded = json_decode($request->items, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['items' => $decoded]);
            }
        }

        $validated = $request->validate([
            'tanggal' => ['required'],
            'problem' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'upload_file' => ['nullable', 'file', 'max:5120'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'items.*.jenis' => ['required', Rule::in(['project', 'umum'])],
            'items.*.project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'items.*.kerjaan_id' => ['nullable', 'integer'],
            'items.*.proses_id' => ['nullable', 'integer'],
            'items.*.judul_umum' => ['nullable', 'string', 'max:255'],
            'items.*.deskripsi' => ['nullable', 'string'],
            'items.*.keterangan' => ['nullable', 'string'],
            'items.*.status_hari_ini' => ['required', Rule::in(['lanjut', 'done'])],
        ]);

        $tanggal = date('Y-m-d', strtotime($validated['tanggal']));

        return DB::transaction(function () use ($request, $validated, $tanggal, $userId) {

            // upload file (header daily)
            $path = null;
            if ($request->hasFile('upload_file')) {
                $path = $request->file('upload_file')->store('daily_uploads', 'public');
            }



            // upsert daily header (1 user 1 tanggal)
            $daily = NewDaily::updateOrCreate(
                ['user_id' => $userId, 'tanggal' => $tanggal],
                [
                    'problem' => $validated['problem'] ?? null,
                    'summary' => $validated['summary'] ?? null,
                    'upload_file' => $path ?? (NewDaily::where('user_id', $userId)->where('tanggal', $tanggal)->value('upload_file')),
                ]
            );

            $createdTaskIds = [];

            foreach ($validated['items'] as $row) {
                $taskId = $row['task_id'] ?? null;

                // kalau task_id tidak ada -> create task baru
                if (!$taskId) {
                    $task = Task::create([
                        'user_id'     => $userId,
                        'created_by'  => $userId,
                        'jenis'       => $row['jenis'],
                        'project_id'  => $row['jenis'] === 'project' ? ($row['project_id'] ?? null) : null,
                        'kerjaan_id'  => $row['kerjaan_id'] ?? null,
                        'proses_id'   => $row['proses_id'] ?? null,
                        'judul_umum'  => $row['jenis'] === 'umum' ? ($row['judul_umum'] ?? '-') : null,
                        'deskripsi'   => $row['deskripsi'] ?? null,
                        'status'      => 'open',
                        'started_at'  => $tanggal,
                        'finished_at' => null,
                    ]);

                    $taskId = $task->id;
                    $createdTaskIds[] = $taskId;
                } else {
                    // validasi: task yang di-log harus milik user ini (biar tidak bisa ngerubah task orang)
                    $task = Task::where('id', $taskId)->where('user_id', $userId)->first();
                    if (!$task) {
                        abort(403, 'Task tidak ditemukan atau bukan milik Anda.');
                    }
                }

                // upsert log hari ini untuk task tsb (unique task_id+tanggal)
                $log = TaskLog::updateOrCreate(
                    ['task_id' => $taskId, 'tanggal' => $tanggal],
                    [
                        'daily_id' => $daily->id,
                        'user_id'  => $userId,
                        'keterangan' => $row['keterangan'] ?? null,
                        'status_hari_ini' => $row['status_hari_ini'],
                        // upload_file per row belum aku handle, bisa ditambah kalau kamu butuh per item
                    ]
                );

                // kalau done -> tutup task master
                if (($row['status_hari_ini'] ?? 'lanjut') === 'done') {
                    Task::where('id', $taskId)->update([
                        'status' => 'done',
                        'finished_at' => $tanggal,
                    ]);
                } else {
                    // pastikan masih open
                    Task::where('id', $taskId)->update([
                        'status' => 'open',
                        'finished_at' => null,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Daily berhasil disimpan.',
                'daily_id' => $daily->id,
                'created_task_ids' => $createdTaskIds,
            ]);
        });
    }


    /**
     * Show the form for editing the specified daily record.
     */
    public function edit($id)
    {
        $daily = NewDaily::with([
            'user:id,name',
            'taskLogs' => function ($q) {
                $q->with([
                    'task.project:id,no_project',
                    'task.prosesRel.listProses:id,nama_proses',
                    'task.prosesRel.kerjaan:id,nama_kerjaan',
                ])->orderBy('id', 'asc');
            }
        ])->findOrFail($id);

        // hanya owner yang boleh edit
        abort_if(auth()->id() !== (int)$daily->user_id, 403);

        return response()->json([
            'data' => $daily,
            'auth_user_id' => auth()->id(),
        ]);
    }




    public function update(Request $request, $id)
    {
        $daily = NewDaily::findOrFail($id);
        abort_if(Auth::id() !== (int) $daily->user_id, 403);

        $request->validate([
            'tanggal'     => ['required'],
            'items'       => ['required'], // JSON string
            'upload_file' => ['nullable', 'file', 'max:5120'],
        ]);

        $items = json_decode($request->items, true);
        if (!is_array($items)) {
            return response()->json([
                'message' => 'The items field must be an array.',
                'errors'  => ['items' => ['The items field must be an array.']]
            ], 422);
        }

        $tanggalOnly = substr((string) $request->tanggal, 0, 10);

        // helper mapping
        $normalizeTaskStatus = function ($v) {
            // tasks.status: open/done
            return ($v === 'done') ? 'done' : 'open';
        };

        $normalizeLogStatus = function ($v) {
            // task_logs.status_hari_ini: lanjut/done
            return ($v === 'done') ? 'done' : 'lanjut';
        };

        DB::transaction(function () use (
            $request,
            $daily,
            $items,
            $tanggalOnly,
            $normalizeTaskStatus,
            $normalizeLogStatus
        ) {

            // 1) header daily
            $daily->tanggal = $tanggalOnly;

            if ($request->hasFile('upload_file')) {
                if ($daily->upload_file) {
                    Storage::disk('public')->delete($daily->upload_file);
                }
                $daily->upload_file = $request->file('upload_file')->store('daily_uploads', 'public');
            }

            $daily->save();

            // 2) items
            foreach ($items as $it) {
                $jenis = $it['jenis'] ?? null;
                $keterangan = $it['keterangan'] ?? null;

                // input status dari JS (bebas), kita normalkan:
                $rawStatus = $it['status_hari_ini'] ?? 'open';
                $taskStatus = $normalizeTaskStatus($rawStatus); // open/done
                $logStatus  = $normalizeLogStatus($rawStatus);  // lanjut/done

                $logId  = $it['log_id'] ?? null;
                $taskId = $it['task_id'] ?? null;

                // A) update log lama
                if (!empty($logId)) {
                    $log = TaskLog::where('id', $logId)
                        ->where('daily_id', $daily->id)
                        ->firstOrFail();

                    $log->tanggal = $tanggalOnly;
                    $log->keterangan = $keterangan;
                    $log->status_hari_ini = $logStatus; // lanjut/done
                    $log->save();

                    $task = Task::find($log->task_id);
                    if ($task) {
                        $task->status = $taskStatus; // open/done
                        if (!$task->started_at) $task->started_at = $tanggalOnly;
                        $task->finished_at = ($taskStatus === 'done') ? $tanggalOnly : null;
                        $task->save();
                    }

                    continue;
                }

                // B) pastikan task ada
                if (empty($taskId)) {
                    $task = new Task();
                    $task->user_id = Auth::id();
                    $task->created_by = Auth::id();
                    $task->jenis = $jenis;

                    if ($jenis === 'project') {
                        $task->project_id = $it['project_id'] ?? null;
                        $task->proses_id  = $it['proses_id'] ?? null;
                        $task->judul_umum = null;
                    } else {
                        $task->project_id = null;
                        $task->proses_id  = null;
                        $task->judul_umum = $it['judul_umum'] ?? null;
                    }

                    $task->status = $taskStatus; // open/done
                    $task->started_at = $tanggalOnly;
                    $task->finished_at = ($taskStatus === 'done') ? $tanggalOnly : null;
                    $task->save();

                    $taskId = $task->id;
                } else {
                    $task = Task::find($taskId);
                    if ($task) {
                        $task->status = $taskStatus;
                        if (!$task->started_at) $task->started_at = $tanggalOnly;
                        $task->finished_at = ($taskStatus === 'done') ? $tanggalOnly : null;
                        $task->save();
                    }
                }

                // C) buat log baru
                TaskLog::create([
                    'daily_id'        => $daily->id,
                    'task_id'         => $taskId,
                    'user_id'         => Auth::id(),
                    'tanggal'         => $tanggalOnly,
                    'keterangan'      => $keterangan,
                    'status_hari_ini' => $logStatus, // lanjut/done
                    'upload_file'     => null,
                ]);
            }
        });

        return response()->json(['message' => 'Daily berhasil diperbarui']);
    }

    /**
     * Delete the specified daily record.
     */
    public function destroy($id)
    {
        $daily = NewDaily::findOrFail($id);

        if ($daily->user_id !== Auth::id()) {
            abort(403, 'Anda tidak punya akses hapus daily ini.');
        }

        // hapus file
        if ($daily->upload_file && Storage::disk('public')->exists($daily->upload_file)) {
            Storage::disk('public')->delete($daily->upload_file);
        }

        $daily->delete();

        return response()->json([
            'message' => 'Daily berhasil dihapus.',
        ]);
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
