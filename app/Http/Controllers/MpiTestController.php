<?php

namespace App\Http\Controllers;

use App\Models\MpiTest;
use App\Models\MpiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\MpiTestExport;
use Maatwebsite\Excel\Facades\Excel;


class MpiTestController extends Controller
{
    public function index(Request $request)
    {
        return view('mpi.index');
    }

    public function testsData(Request $request)
    {
        $query = MpiTest::query()
            ->select([
                'mpi_tests.id',
                'mpi_tests.nama_pt',
                'mpi_tests.tanggal_running',
                'mpi_tests.tanggal_inspection',
                'mpi_tests.person',
                'mpi_tests.created_by',
                'mpi_tests.created_at',
            ])
            ->with('creator');

        return DataTables::of($query)
           ->addIndexColumn()
            ->editColumn('tanggal_running', function ($row) {
                return $row->tanggal_running ? Carbon::parse($row->tanggal_running)->format('d M Y') : '';
            })
            ->editColumn('tanggal_inspection', function ($row) {
                return $row->tanggal_inspection ? Carbon::parse($row->tanggal_inspection)->format('d M Y') : '';
            })
            ->editColumn('person', function ($row) {
                if (is_numeric($row->person) && floor($row->person) == $row->person) {
                    return number_format($row->person, 0, ',', '.');
                }
                return number_format($row->person, 2, ',', '.');
            })
            ->addColumn('creator_name', function ($row) {
                return $row->creator ? $row->creator->name : ($row->created_by ? 'ID: '.$row->created_by : '-');
            })
            ->addColumn('actions', function($row){
                $viewUrl = route('mpi.show', $row->id);
                $editBtn = '<button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>';
                $delBtn = '<button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'"> <i class="fas fa-trash"></i></button>';
                $exportBtn = '<a href="'.route('mpi.tests.export', $row->id).'" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i></a>';
                return '<a href="'.$viewUrl.'" class="btn btn-sm btn-info"> <i class="fas fa-eye"></i></a> '.$editBtn.' '.$delBtn.' '.$exportBtn;
            })
            ->rawColumns(['actions'])
            ->make(true);
        }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pt' => 'required|string|max:255',
            'tanggal_running' => 'nullable|date',
            'tanggal_inspection' => 'nullable|date',
            'person' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $test = MpiTest::create([
                'nama_pt' => $data['nama_pt'],
                'tanggal_running' => $data['tanggal_running'] ?? null,
                'tanggal_inspection' => $data['tanggal_inspection'] ?? null,
                'person' => $data['person'] ?? null,
                'created_by' => auth()->id() ?? null,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'id' => $test->id, 'message' => 'MPI test created']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create MPI test', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $test = MpiTest::findOrFail($id);
        return response()->json($test);
    }

    public function update(Request $request, $id)
    {
        $test = MpiTest::findOrFail($id);

        $data = $request->validate([
            'nama_pt' => 'required|string|max:255',
            'tanggal_running' => 'nullable|date',
            'tanggal_inspection' => 'nullable|date',
            'person' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $test->update([
                'nama_pt' => $data['nama_pt'],
                'tanggal_running' => $data['tanggal_running'] ?? null,
                'tanggal_inspection' => $data['tanggal_inspection'] ?? null,
                'person' => $data['person'] ?? null,
            ]);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'MPI test updated']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $test = MpiTest::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($test->items as $item) {
                $this->deleteItemFiles($item);
            }

            $test->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'MPI test deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $test = MpiTest::with(['items.materials', 'items.posisis'])->findOrFail($id);
        return view('mpi.show', compact('test'));
    }

    public function storeItem(Request $request, $testId)
    {
        $test = MpiTest::findOrFail($testId);

        $rules = [
            'nama_jurulas' => 'nullable|string|max:255',
            'proses_las' => ['nullable', Rule::in(['SMAW','FCAW','SMAW & FCAW'])],
            'posisi_uji' => 'nullable|array',
            'posisi_uji.*' => ['nullable', Rule::in(['1G','2G','3G','4G','5G','6G','1F','2F','3F','4F'])],
            'materials' => 'nullable|array',
            'materials.*.nama_material' => 'required_with:materials|string|max:255',
            'materials.*.qty' => 'nullable|string|max:100',
            'materials.*.note' => 'nullable|string|max:500',
            'foto_jurulas' => 'nullable|file|image|max:5120',
            'foto_ktp' => 'nullable|file|image|max:5120',
            'foto_sebelum' => 'nullable|file|image|max:5120',
            'foto_during' => 'nullable|file|image|max:5120',
            'foto_hasil' => 'nullable|file|image|max:5120',
            'foto_sebelum_mpi' => 'nullable|file|image|max:5120',
            'foto_setelah_mpi' => 'nullable|file|image|max:5120',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // create item
            $item = $test->items()->create([
                'nama_jurulas' => $validated['nama_jurulas'] ?? null,
                'proses_las' => $validated['proses_las'] ?? null,
            ]);

            // handle files
            $fileKeys = ['foto_jurulas','foto_ktp','foto_sebelum','foto_during','foto_hasil','foto_sebelum_mpi','foto_setelah_mpi'];
            foreach ($fileKeys as $k) {
                if ($request->hasFile($k)) {
                    $path = $this->storeItemFile($test->id, $item->id, $request->file($k));
                    $item->{$k} = $path;
                }
            }
            $item->save();

            // materials
            if (!empty($validated['materials'])) {
                foreach ($validated['materials'] as $m) {
                    $item->materials()->create([
                        'nama_material' => $m['nama_material'] ?? null,
                        'qty' => $m['qty'] ?? null,
                        'note' => $m['note'] ?? null,
                    ]);
                }
            }

            // posisi
            if (!empty($validated['posisi_uji'])) {
                foreach ($validated['posisi_uji'] as $code) {
                    $item->posisis()->create(['nama_posisi' => $code]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item added', 'item_id' => $item->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>'Failed','error'=>$e->getMessage()],500);
        }
    }

    /**
     * Return JSON for one item (for item edit modal)
     */
    public function editItem($id)
    {
        $item = MpiItem::with(['materials','posisis'])->findOrFail($id);
        return response()->json($item);
    }

    /**
     * Update item (modal via AJAX)
     */
    public function updateItem(Request $request, $id)
    {
        $item = MpiItem::with(['materials','posisis'])->findOrFail($id);

        $rules = [
            'nama_jurulas' => 'nullable|string|max:255',
            'proses_las' => ['nullable', Rule::in(['SMAW','FCAW','SMAW & FCAW'])],
            'posisi_uji' => 'nullable|array',
            'posisi_uji.*' => ['nullable', Rule::in(['1G','2G','3G','4G','5G','6G','1F','2F','3F','4F'])],
            'materials' => 'nullable|array',
            'materials.*.nama_material' => 'required_with:materials|string|max:255',
            'foto_jurulas' => 'nullable|file|image|max:5120',
            'foto_ktp' => 'nullable|file|image|max:5120',
            'foto_sebelum' => 'nullable|file|image|max:5120',
            'foto_during' => 'nullable|file|image|max:5120',
            'foto_hasil' => 'nullable|file|image|max:5120',
            'foto_sebelum_mpi' => 'nullable|file|image|max:5120',
            'foto_setelah_mpi' => 'nullable|file|image|max:5120',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $item->update([
                'nama_jurulas' => $validated['nama_jurulas'] ?? $item->nama_jurulas,
                'proses_las' => $validated['proses_las'] ?? $item->proses_las,
            ]);

            // files: if provided replace and delete old
            $fileKeys = ['foto_jurulas','foto_ktp','foto_sebelum','foto_during','foto_hasil','foto_sebelum_mpi','foto_setelah_mpi'];
            foreach ($fileKeys as $k) {
                if ($request->hasFile($k)) {
                    // delete old
                    if ($item->{$k}) {
                        Storage::disk('public')->delete($item->{$k});
                    }
                    $path = $this->storeItemFile($item->test->id ?? $item->mpi_test_id, $item->id, $request->file($k));
                    $item->{$k} = $path;
                }
            }
            $item->save();

            // replace materials & posisi (simple approach: delete and recreate)
            $item->materials()->delete();
            if (!empty($validated['materials'])) {
                foreach ($validated['materials'] as $m) {
                    $item->materials()->create([
                        'nama_material' => $m['nama_material'] ?? null,
                        'qty' => $m['qty'] ?? null,
                        'note' => $m['note'] ?? null,
                    ]);
                }
            }

            $item->posisis()->delete();
            if (!empty($validated['posisi_uji'])) {
                foreach ($validated['posisi_uji'] as $code) {
                    $item->posisis()->create(['nama_posisi' => $code]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item updated']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>'Failed to update item','error'=>$e->getMessage()],500);
        }
    }

    /**
     * Delete item (modal confirm -> AJAX)
     */
    public function destroyItem($id)
    {
        $item = MpiItem::findOrFail($id);
        DB::beginTransaction();
        try {
            $this->deleteItemFiles($item);
            $item->materials()->delete();
            $item->posisis()->delete();
            $item->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>'Failed to delete item','error'=>$e->getMessage()],500);
        }
    }


    protected function storeItemFile($testId, $itemId, $file)
    {
        $folder = "mpi/{$testId}/item_{$itemId}";
        $name = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        return $file->storeAs($folder, $name, 'public');
    }

    protected function deleteItemFiles(MpiItem $item)
    {
        $keys = ['foto_jurulas','foto_ktp','foto_sebelum','foto_during','foto_hasil','foto_sebelum_mpi','foto_setelah_mpi'];
        foreach ($keys as $k) {
            if ($item->{$k}) {
                try {
                    Storage::disk('public')->delete($item->{$k});
                } catch (\Throwable $e) {
                    // ignore deletion error
                }
            }
        }
    }

    public function exportExcel($id)
    {
        $fileName = 'mpi_test_' . $id . '_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new MpiTestExport($id), $fileName);
    }
}
