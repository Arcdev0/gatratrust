<?php

namespace App\Http\Controllers;

use App\Models\Kerjaan;
use App\Models\KerjaanListProses;
use App\Models\ListProses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KerjaanController extends Controller
{
    public function index()
    {
        // Ambil semua data list proses
        $listProses = ListProses::orderBy('nama_proses')->get();

        return view('kerjaan.index', compact('listProses'));
    }

    public function getData()
    {
        $kerjaans = Kerjaan::query();

        return DataTables::of($kerjaans)
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y');
            })
            ->addColumn('action', function ($row) {
                return '
                <button class="btn btn-sm btn-primary edit-kerjaan" data-id="' . $row->id . '">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-kerjaan" data-id="' . $row->id . '">
                    <i class="fas fa-trash"></i>
                </button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($id)
    {
        $kerjaan = Kerjaan::with(['prosesList'])->findOrFail($id);

        return response()->json([
            'id' => $kerjaan->id,
            'nama_kerjaan' => $kerjaan->nama_kerjaan,
            'proses' => $kerjaan->prosesList->map(function ($item) {
                return [
                    'list_proses_id' => $item->pivot->list_proses_id,
                    'proses' => $item->nama_proses,
                    'urutan' => $item->pivot->urutan,
                    'hari' => $item->pivot->hari
                ];
            })
        ]);
    }


    public function store(Request $request)
    {

        // dd($request->all());
        // Validasi
        $validated = $request->validate([
            'nama_pekerjaan' => 'required|string|max:255',
            'proses' => 'required|array',
            'proses.*.id' => 'required|integer',
            'proses.*.proses' => 'required|string',
            'proses.*.urutan' => 'required|integer',
            'proses.*.hari' => 'required|integer|min:0'
        ]);

        DB::beginTransaction();

        try {
            // Simpan ke tabel kerjaans
            $kerjaan = Kerjaan::create([
                'nama_kerjaan' => $validated['nama_pekerjaan'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Simpan ke tabel kerjaan_list_proses
            foreach ($validated['proses'] as $proses) {
                KerjaanListProses::create([
                    'kerjaan_id' => $kerjaan->id, // ambil id kerjaan
                    'list_proses_id' => $proses['id'],
                    'urutan' => $proses['urutan'],
                    'hari' => $proses['hari'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Hapus semua list proses terkait
            KerjaanListProses::where('kerjaan_id', $id)->delete();

            // Hapus kerjaan utama
            $kerjaan = Kerjaan::findOrFail($id);
            $kerjaan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'nama_pekerjaan' => 'required|string|max:255',
            'proses' => 'required|array',
            'proses.*.id' => 'required|integer',
            'proses.*.proses' => 'required|string',
            'proses.*.urutan' => 'required|integer',
            'proses.*.hari' => 'required|integer|min:0'
        ]);

        // dd($validated);

        DB::beginTransaction();
        try {
            $kerjaan = Kerjaan::findOrFail($id);
            $kerjaan->update([
                'nama_kerjaan' => $validated['nama_pekerjaan'],
                'updated_at' => now()
            ]);

            // Hapus proses lama
            KerjaanListProses::where('kerjaan_id', $id)->delete();

            // Simpan proses baru
            foreach ($validated['proses'] as $proses) {
                KerjaanListProses::create([
                    'kerjaan_id' => $kerjaan->id,
                    'list_proses_id' => $proses['id'],
                    'urutan' => $proses['urutan'],
                    'hari' => $proses['hari'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
