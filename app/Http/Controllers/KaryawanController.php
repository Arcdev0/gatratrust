<?php

namespace App\Http\Controllers;

use App\Models\KaryawanData;
use App\Models\SertifikatInhouse;
use App\Models\SertifikatExternal;
use App\Models\Jabatan;
use App\Models\SyaratJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KaryawanController extends Controller
{
    /**
     * Menampilkan halaman index (DataTables)
     */
    public function index()
    {
        return view('karyawan.index');
    }

    /**
     * Mengambil data karyawan untuk DataTables
     */
    public function getData()
    {
        $karyawan = KaryawanData::with('jabatan')->select('karyawan_data.*');

        return DataTables::of($karyawan)
            ->addColumn('jabatan', function ($row) {
                return $row->jabatan ? $row->jabatan->nama_jabatan : '-';
            })
            ->addColumn('status', function ($row) {
                return $row->status
                    ? '<span class="badge badge-success">Aktif</span>'
                    : '<span class="badge badge-secondary">Tidak Aktif</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('karyawan.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                    <form action="' . route('karyawan.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus?\')">Hapus</button>
                    </form>
                ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Form tambah karyawan
     */
    public function create()
    {
        $jabatan = Jabatan::all();
        return view('karyawan.create', compact('jabatan'));
    }


    public function getSyaratJabatan($jabatanId)
    {
        $syarat = SyaratJabatan::where('jabatan_id', $jabatanId)->pluck('nama_syarat');

        return response()->json($syarat);
    }

    /**
     * Simpan data karyawan + sertifikat
     */
    public function store(Request $request)
    {

    //    dd($request->file('foto')->getMimeType());
        $request->validate([
            'no_karyawan'      => 'required|string|unique:karyawan_data,no_karyawan',
            'nama_lengkap'     => 'required|string|max:255',
            'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir'     => 'required|string|max:255',
            'tanggal_lahir'    => 'required|date',
            'alamat_lengkap'   => 'required|string',
            'jabatan_id'       => 'required|exists:jabatan,id',
            'status'           => 'required|boolean',
            'nomor_telepon'    => 'nullable|string|max:20',
            'email'            => 'nullable|email|unique:karyawan_data,email',
            'nomor_identitas'  => 'nullable|string|unique:karyawan_data,nomor_identitas',
            'status_perkawinan' => 'nullable|string',
            'kewarganegaraan'  => 'nullable|string',
            'agama'            => 'nullable|string',
            'doh'              => 'nullable|date',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png',

            'sertifikat_inhouse.*.nama_sertifikat' => 'nullable|string|max:255',
            'sertifikat_inhouse.*.file_sertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'sertifikat_external.*.nama_sertifikat' => 'nullable|string|max:255',
            'sertifikat_external.*.file_sertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ]);

        DB::beginTransaction();

        try {
            // Upload foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto_karyawan', 'public');
            }

            // Insert data karyawan
            $karyawan = KaryawanData::create([
                'no_karyawan'       => $request->no_karyawan,
                'nama_lengkap'      => $request->nama_lengkap,
                'jenis_kelamin'     => $request->jenis_kelamin,
                'tempat_lahir'      => $request->tempat_lahir,
                'tanggal_lahir'     => $request->tanggal_lahir,
                'alamat_lengkap'    => $request->alamat_lengkap,
                'jabatan_id'        => $request->jabatan_id,
                'status'            => $request->status,
                'nomor_telepon'     => $request->nomor_telepon,
                'email'             => $request->email,
                'nomor_identitas'   => $request->nomor_identitas,
                'status_perkawinan' => $request->status_perkawinan,
                'kewarganegaraan'   => $request->kewarganegaraan,
                'agama'             => $request->agama,
                'doh'               => $request->doh,
                'foto'              => $fotoPath
            ]);

            // Sertifikat Inhouse
            if ($request->has('sertifikat_inhouse')) {
                foreach ($request->sertifikat_inhouse as $sertifikat) {
                    if (!empty($sertifikat['nama_sertifikat'])) {
                        $filePath = null;
                        if (isset($sertifikat['file_sertifikat']) && $sertifikat['file_sertifikat'] instanceof \Illuminate\Http\UploadedFile) {
                            $filePath = $sertifikat['file_sertifikat']->store('sertifikat_inhouse', 'public');
                        }
                        SertifikatInhouse::create([
                            'karyawan_id' => $karyawan->id,
                            'nama_sertifikat' => $sertifikat['nama_sertifikat'],
                            'file_sertifikat' => $filePath,
                        ]);
                    }
                }
            }

            // Sertifikat External
            if ($request->has('sertifikat_external')) {
                foreach ($request->sertifikat_external as $sertifikat) {
                    if (!empty($sertifikat['nama_sertifikat'])) {
                        $filePath = null;
                        if (isset($sertifikat['file_sertifikat']) && $sertifikat['file_sertifikat'] instanceof \Illuminate\Http\UploadedFile) {
                            $filePath = $sertifikat['file_sertifikat']->store('sertifikat_external', 'public');
                        }
                        SertifikatExternal::create([
                            'karyawan_id' => $karyawan->id,
                            'nama_sertifikat' => $sertifikat['nama_sertifikat'],
                            'file_sertifikat' => $filePath,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data karyawan berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Form edit karyawan
     */
    public function edit($id)
    {
        $karyawan = KaryawanData::with(['sertifikatInhouse', 'sertifikatExternal'])->findOrFail($id);
        $jabatan = Jabatan::all();
        return view('karyawan.edit', compact('karyawan', 'jabatan'));
    }

    /**
     * Update data karyawan
     */
    public function update(Request $request, $id)
    {
        $karyawan = KaryawanData::findOrFail($id);

        $request->validate([
            'no_karyawan'      => 'required|string|unique:karyawan_data,no_karyawan,' . $id,
            'nama_lengkap'     => 'required|string|max:255',
            'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir'     => 'required|string|max:255',
            'tanggal_lahir'    => 'required|date',
            'alamat_lengkap'   => 'required|string',
            'jabatan_id'       => 'required|exists:jabatan,id',
            'status'           => 'required|boolean',
            'nomor_telepon'    => 'nullable|string|max:20',
            'email'            => 'nullable|email|unique:karyawan_data,email,' . $id,
            'nomor_identitas'  => 'nullable|string|unique:karyawan_data,nomor_identitas,' . $id,
            'status_perkawinan' => 'nullable|string',
            'kewarganegaraan'  => 'nullable|string',
            'agama'            => 'nullable|string',
            'pekerjaan'        => 'nullable|string',
            'doh'              => 'nullable|date',
            'foto'             => 'nullable|string',
        ]);

        $karyawan->update($request->only([
            'nama_lengkap',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat_lengkap',
            'jabatan_id',
            'status',
            'nomor_telepon',
            'email',
            'nomor_identitas',
            'status_perkawinan',
            'kewarganegaraan',
            'agama',
            'pekerjaan',
            'doh',
            'foto'
        ]));

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui');
    }

    /**
     * Hapus data karyawan
     */
    public function destroy($id)
    {
        $karyawan = KaryawanData::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus');
    }
}
