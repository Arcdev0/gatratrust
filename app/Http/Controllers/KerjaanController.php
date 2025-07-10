<?php

namespace App\Http\Controllers;

use App\Models\Kerjaan;
use App\Models\KerjaanListProses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KerjaanController extends Controller
{
    public function index()
    {

        return view('kerjaan.index');
    }

    public function getData()
    {
        $kerjaans = Kerjaan::query();

        return DataTables::of($kerjaans)
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y');
                // Hasil: 10 Juli 2025
            })
            ->addColumn('action', function ($row) {
                return '
                <a href="" class="btn btn-sm btn-primary">Edit</a>
                <button class="btn btn-sm btn-danger delete-kerjaan" data-id="' . $row->id . '">
                    <i class="fas fa-trash"></i>
                </button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function store(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'nama_pekerjaan' => 'required|string|max:255',
            'proses' => 'required|array',
            'proses.*.id' => 'required|integer',
            'proses.*.proses' => 'required|string',
            'proses.*.urutan' => 'required|integer',
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
}
