<?php

namespace App\Http\Controllers;

use App\Models\Kerjaan;
use App\Models\KerjaanListProses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KerjaanController extends Controller
{
    public function index()
    {

        return view('kerjaan.index');
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
}
