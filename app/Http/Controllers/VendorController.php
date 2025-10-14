<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{

    public function index()
    {
        return view('vendor.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Vendor::select(['id', 'nama_vendor', 'nama_perusahaan', 'alamat', 'nomor_telepon', 'email']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    $btn = '
                        <button class="btn btn-sm btn-secondary editVendor" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteVendor" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor tidak ditemukan'], 404);
        }

        return response()->json($vendor);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_vendor' => 'required|string|max:100',
            'nama_perusahaan' => 'nullable|string|max:150',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100|unique:vendor,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vendor = Vendor::create($validator->validated());

        return response()->json(['message' => 'Vendor berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_vendor' => 'required|string|max:100',
            'nama_perusahaan' => 'nullable|string|max:150',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100|unique:vendor,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vendor->update($validator->validated());

        return response()->json(['message' => 'Vendor berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor tidak ditemukan'], 404);
        }

        $vendor->delete();

        return response()->json(['message' => 'Vendor berhasil dihapus']);
    }
}
