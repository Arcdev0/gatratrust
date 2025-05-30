<?php
namespace App\Http\Controllers;

use App\Models\ProjectTbl;
use App\Models\User;
use App\Models\Kerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;

class TblProjectController extends Controller
{
    public function index()
    {
        $projects = ProjectTbl::with(['client', 'kerjaan', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
        $listclient = User::where('role_id', 2)->get();
        $listkerjaan = Kerjaan::select('id', 'nama_kerjaan')->get();
        return view('projects.index', compact('projects', 'listclient', 'listkerjaan'));
    }

    public function getListProject(Request $request)
    {
        if ($request->ajax()) {
            $query = ProjectTbl::with(['client', 'kerjaan'])->select('projects.*');

            // Jika user adalah client, filter hanya project miliknya
            if (auth()->user()->role_id == 2) {
                $query->where('client_id', auth()->id());
            }

            return DataTables::of($query)
                ->addColumn('client', function ($project) {
                    return $project->client->name ?? '-';
                })
                ->addColumn('kerjaan', function ($project) {
                    return $project->kerjaan->nama_kerjaan ?? '-';
                })
                ->addColumn('company', function ($project) {
                    return $project->client->company ?? '-';
                })
               ->addColumn('selesai', function ($project) {
                    $listProses = DB::table('kerjaan_list_proses')
                        ->where('kerjaan_id', $project->kerjaan_id)
                        ->select('list_proses_id', 'urutan')
                        ->get();
                    $totalProses = $listProses->count();
                    $prosesSelesai = DB::table('project_details')
                        ->where('project_id', $project->id)
                        ->where(function($query) use ($listProses) {
                            foreach ($listProses as $proses) {
                                $query->orWhere(function($q) use ($proses) {
                                    $q->where('kerjaan_list_proses_id', $proses->list_proses_id)
                                    ->where('urutan_id', $proses->urutan);
                                });
                            }
                        })
                        ->where('status', 'done')
                        ->count();

                    $persen = $totalProses > 0 ? round(($prosesSelesai / $totalProses) * 100) : 0;

                    $icon = $persen == 100 ? '<i class="fas fa-check text-success ml-1"></i>' : '';

                    return "$persen% $icon";
                })
                ->addColumn('periode', function ($project) {
                    if ($project->start && $project->end) {
                        return '
                            <small class="d-block">Mulai: ' . $project->start->format('d M Y') . '</small>
                            <small class="d-block">Selesai: ' . $project->end->format('d M Y') . '</small>
                        ';
                    }
                    return '<span class="text-muted">Belum ditentukan</span>';
                })
                ->addColumn('aksi', function ($project) {
                    $viewBtn = '
                        <a class="btn btn-sm btn-info" href="' . route('projects.show', $project->id) . '">
                            <i class="fas fa-eye"></i>
                        </a>';

                    if (auth()->user()->role_id == 1) {
                        $editBtn = '
                            <button type="button" class="btn btn-sm btn-secondary btn-edit-project"
                                data-toggle="modal" data-target="#EditProjectModal"
                                data-id="' . $project->id . '"
                                data-no="' . $project->no_project . '"
                                data-nama="' . $project->nama_project . '"
                                data-client="' . $project->client_id . '"
                                data-kerjaan="' . $project->kerjaan_id . '"
                                data-deskripsi="' . $project->deskripsi . '"
                                data-start="' . optional($project->start)->format('Y-m-d') . '"
                                data-end="' . optional($project->end)->format('Y-m-d') . '">
                                <i class="fas fa-edit"></i>
                            </button>';

                        $deleteBtn = '
                                <button modal type="button" class="btn btn-sm btn-danger btnDeletProject"
                                    data-id="' . $project->id . '">
                                    <i class="fas fa-trash"></i>
                                </button>';

                        return $viewBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                    }

                    return $viewBtn;
                })
                ->rawColumns(['periode', 'aksi', 'selesai'])
                ->make(true);
        }
    }


    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_project' => 'required|string|max:100',
        'no_project' => 'required|string|unique:projects',
        'client_id' => 'required|exists:users,id',
        'kerjaan_id' => 'required|exists:kerjaans,id',
        'deskripsi' => 'nullable|string',
        'start' => 'nullable|date',
        'end' => 'nullable|date|after_or_equal:start'
    ]);

    $validated['created_by'] = Auth::id();

    ProjectTbl::create($validated);

    if ($request->ajax()) {
        return response()->json(['success' => true]);
    }

    return redirect()->route('projects.tampilan')
        ->with('success', 'Project berhasil ditambahkan');
}

    public function show(ProjectTbl $project)
        {
            $kerjaanId = $project->kerjaan_id;
            $projectId = $project->id;

            // dd( $kerjaanId,  $projectId);

         $q = "SELECT
                    a.urutan,
                    a.list_proses_id,
                    b.nama_proses,
                    c.status
                FROM kerjaan_list_proses a
                JOIN list_proses b ON a.list_proses_id = b.id
                LEFT JOIN project_details c
                    ON b.id = c.kerjaan_list_proses_id
                    AND c.project_id = '$projectId'
                    AND c.urutan_id = a.urutan
                WHERE a.kerjaan_id = '$kerjaanId'
                ORDER BY a.urutan ASC";

           $processes = DB::select($q);

            $steps = [];
            $stepStatuses = [];
            $stepProcessIds = [];
            $stepUrutan = [];

            foreach ($processes as $process) {
                $key = $process->list_proses_id . '-' . $process->urutan;
                $steps[$key] = $process->nama_proses;
                $stepStatuses[$key] = $process->status ?? 'pending';
                $stepProcessIds[$key] = $process->list_proses_id;
                $stepUrutan[$key] = $process->urutan;
            }

            return view('projects.show', compact('project', 'steps', 'stepStatuses', 'stepProcessIds', 'stepUrutan'));
        }

    public function update(Request $request, ProjectTbl $project)
    {
        $validated = $request->validate([
            'nama_project' => 'required|string|max:100',
            'no_project' => 'required|string|unique:projects,no_project,' . $project->id,
            'client_id' => 'required|exists:users,id',
            'kerjaan_id' => 'required|exists:kerjaans,id',
            'deskripsi' => 'nullable|string',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start'
        ]);

        $project->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('projects.tampilan')
            ->with('success', 'Project berhasil diperbarui');
    }

    public function destroy(ProjectTbl $project, Request $request)
    {
        try {
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus project.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadFiles(Request $request)
    {

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'list_proses_id' => 'required|exists:list_proses,id',
            'fileLabel' => 'required|array',
            'fileLabel.*' => 'required|string',
            'fileInput' => 'required|array',
            'fileInput.*' => 'required|file|mimes:pdf,jpg,png,doc,docx,xls,xlsx|max:102400',
        ]);

        $listProsesId = $request->input('list_proses_id');
        $urutanId = $request->input('urutan_id');

        DB::beginTransaction();

        try {
            // 1. Ambil atau buat project_detail
            $projectDetail = DB::table('project_details')
                ->where('project_id', $request->project_id)
                ->where('kerjaan_list_proses_id', $listProsesId)
                ->where('urutan_id', $urutanId)
                ->first();

            if (!$projectDetail) {
                $projectDetailId = DB::table('project_details')->insertGetId([
                    'project_id' => $request->project_id,
                    'kerjaan_list_proses_id' => $listProsesId,
                    'urutan_id' => $urutanId,
                    'status' => 'in_progress',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $projectDetailId = $projectDetail->id;
            }

            // 2. Proses file yang diunggah
            foreach ($request->file('fileInput') as $index => $file) {
                $namaFile = $request->fileLabel[$index] ?? 'Unnamed File';

                // 2a. Ambil atau buat list_proses_file
                $listProsesFile = DB::table('list_proses_files')
                    ->where('list_proses_id', $listProsesId)
                    ->where('nama_file', $namaFile)
                    ->first();

                if (!$listProsesFile) {
                    $listProsesFileId = DB::table('list_proses_files')->insertGetId([
                        'list_proses_id' => $listProsesId,
                        'nama_file' => $namaFile,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $listProsesFileId = $listProsesFile->id;
                }

                // 2b. Simpan file ke storage
                $directory = 'uploads/projects/' . $request->project_id;
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($directory), $fileName);
                $publicPath = $directory . '/' . $fileName;

                // 2c. Simpan data ke project_progress_files
                DB::table('project_progress_files')->insert([
                    'project_detail_id' => $projectDetailId,
                    'list_proses_file_id' => $listProsesFileId,
                    'file_path' => $publicPath,
                    'keterangan' => $namaFile,
                    'uploaded_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'File berhasil diunggah.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 public function getUploadedFiles($projectId, Request $request)
    {
        $listProsesId = $request->input('list_proses_id');
        $urutanId = $request->input('urutan_id'); // Ambil dari request

        $query = DB::table('project_progress_files')
            ->join('project_details', 'project_progress_files.project_detail_id', '=', 'project_details.id')
            ->join('list_proses_files', 'project_progress_files.list_proses_file_id', '=', 'list_proses_files.id')
            ->where('project_details.project_id', $projectId);

        // Filter berdasarkan list_proses_id jika tersedia
        if ($listProsesId) {
            $query->where('project_details.kerjaan_list_proses_id', $listProsesId);
        }

        // Filter berdasarkan urutan_id jika tersedia
        if ($urutanId) {
            $query->where('project_details.urutan_id', $urutanId);
        }

        $files = $query->select([
                'project_progress_files.id',
                'list_proses_files.nama_file as name',
                'project_progress_files.file_path',
                'project_progress_files.keterangan',
                'project_progress_files.created_at',
                'project_progress_files.uploaded_by'
            ])
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->name,
                    'url' => asset($file->file_path),
                    'description' => $file->keterangan,
                    'uploaded_at' => $file->created_at,
                    'uploaded_by' => $file->uploaded_by
                ];
            });

        return response()->json($files);
    }


    public function deleteFile($id)
    {
        // Ambil data file
        $file = DB::table('project_progress_files')->where('id', $id)->first();

        if (!$file) {
            return response()->json(['message' => 'File tidak ditemukan.'], 404);
        }

        // Hapus file dari sistem file jika ada
        if (File::exists(public_path($file->file_path))) {
            File::delete(public_path($file->file_path));
        }

        // Hapus data dari database
        DB::table('project_progress_files')->where('id', $id)->delete();

        return response()->json(['message' => 'File berhasil dihapus.']);
    }



  public function markStepDone($id, Request $request)
    {
        $project_id = $id;
        $validated = $request->validate([
            'list_proses_id' => 'required',
            'urutan_id' => 'required'
        ]);

        try {

            DB::table('project_details')
                ->where('project_id', $project_id)
                ->where('kerjaan_list_proses_id', $validated['list_proses_id'])
                ->where('urutan_id', $validated['urutan_id'])
                ->update([
                    'status' => 'done',
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah menjadi selesai'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }


    public function unmarkStepDone($id, Request $request)
    {
        $project_id = $id;

        // Validasi input kerjaan_list_proses_id
        $validated = $request->validate([
            'kerjaan_list_proses_id' => 'required|integer',
            'urutan_id' => 'required'
        ]);

        try {
            // Update status jadi in_progress
            DB::table('project_details')
                ->where('project_id', $project_id)
                ->where('kerjaan_list_proses_id', $validated['kerjaan_list_proses_id'])
                ->where('urutan_id', $validated['urutan_id'])
                ->update([
                    'status' => 'in_progress',
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil dibatalkan dari selesai'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan status: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getListKomentar(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'list_proses_id' => 'required|integer',
            'urutan_id' => 'required|integer',
        ]);

        // 1. Cari project_detail_id
        $projectDetail = DB::table('project_details')
            ->where('project_id', $request->project_id)
            ->where('kerjaan_list_proses_id', $request->list_proses_id)
            ->where('urutan_id', $request->urutan_id)
            ->first();

        // dd($projectDetail);

        if (!$projectDetail) {
            return response()->json([]); // Tidak ada komentar karena belum ada project_detail
        }

        // 2. Ambil komentar berdasarkan project_detail_id
        $komentar = DB::table('project_detail_comments as c')
            ->leftJoin('users as u', 'c.user_id', '=', 'u.id')
            ->leftJoin('roles as r', 'u.role_id', '=', 'r.id')
            ->select(
                'c.id',
                'c.comment',
                'c.created_at',
                'u.name as user_name',
                'r.name as role_name'
            )
            ->where('c.project_detail_id', $projectDetail->id)
            ->orderBy('c.id', 'desc')
            ->get();

        //   dd($komentar);

        return response()->json($komentar);
    }


    public function storeKomentar(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'list_proses_id' => 'required|integer',
            'urutan_id' => 'required|integer',
            'comment' => 'required|string|max:1000',
        ]);

        $projectId = $request->project_id;
        $listProsesId = $request->list_proses_id;
        $urutanId = $request->urutan_id;

        try {
            DB::beginTransaction();

            // 1. Cek apakah sudah ada project_detail
            $projectDetail = DB::table('project_details')
                ->where('project_id', $projectId)
                ->where('kerjaan_list_proses_id', $listProsesId)
                ->where('urutan_id', $urutanId)
                ->first();

            // 2. Kalau belum ada, insert dulu
            if (!$projectDetail) {
                $projectDetailId = DB::table('project_details')->insertGetId([
                    'project_id' => $projectId,
                    'kerjaan_list_proses_id' => $listProsesId,
                    'urutan_id' => $urutanId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $projectDetailId = $projectDetail->id;
            }

            // 3. Simpan komentar ke project_detail_comments
            $inserted = DB::table('project_detail_comments')->insert([
                'project_detail_id' => $projectDetailId,
                'user_id' => Auth::id(),
                'comment' => $request->comment,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!$inserted) {
                throw new \Exception('Gagal menambahkan komentar.');
            }

            DB::commit();
            return response()->json(['message' => 'Komentar berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menambahkan komentar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteKomentar($id)
    {
        try {
            DB::table('project_detail_comments')->where('id', $id)->delete();

            return response()->json(['message' => 'Komentar berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus komentar'], 500);
        }
    }
}
