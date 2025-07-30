<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\SyaratJabatan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    public function index()
    {
        return view('jabatan.index');
    }

    public function getData(Request $request)
    {
        $jabatan = Jabatan::with('syaratJabatan')->latest();

        return DataTables::of($jabatan)
            ->addIndexColumn()
            ->addColumn('syarat_list', function ($row) {
                if ($row->syaratJabatan->count() > 0) {
                    $list = '<ul class="mb-0">';
                    foreach ($row->syaratJabatan as $syarat) {
                        $list .= '<li>' . e($syarat->nama_syarat) . '</li>';
                    }
                    $list .= '</ul>';
                    return $list;
                }
                return '<em class="text-muted">Tidak ada syarat</em>';
            })
            ->addColumn('action', function ($row) {
                return '
                <button class="btn btn-sm btn-primary edit" data-id="' . $row->id . '">Edit</button>
                <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</button>
            ';
            })
            ->rawColumns(['syarat_list', 'action']) // supaya HTML list tampil
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'nama_syarat.*' => 'nullable|string|max:255'
        ]);

        $jabatan = Jabatan::create([
            'nama_jabatan' => $request->nama_jabatan
        ]);

        if ($request->has('nama_syarat')) {
            foreach ($request->nama_syarat as $syarat) {
                if ($syarat) {
                    SyaratJabatan::create([
                        'jabatan_id' => $jabatan->id,
                        'nama_syarat' => $syarat
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }


    public function edit($id)
    {
        $jabatan = Jabatan::with('syaratJabatan')->findOrFail($id);

        return response()->json($jabatan);
    }

    /**
     * Update jabatan dan syaratnya
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'nama_syarat.*' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($request, $id) {
            $jabatan = Jabatan::findOrFail($id);
            $jabatan->update([
                'nama_jabatan' => $request->nama_jabatan
            ]);

            // Hapus semua syarat lama
            SyaratJabatan::where('jabatan_id', $id)->delete();

            // Insert syarat baru
            if ($request->has('nama_syarat')) {
                foreach ($request->nama_syarat as $syarat) {
                    if ($syarat) {
                        SyaratJabatan::create([
                            'jabatan_id' => $id,
                            'nama_syarat' => $syarat
                        ]);
                    }
                }
            }
        });

        return response()->json(['success' => true]);
    }
}
