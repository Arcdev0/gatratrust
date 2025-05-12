<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
      public function index()
    {


        return view('role.indexrole');
    }

    public function list()
    {
        $roles = Role::select('id', 'name')->get();

        return response()->json(['data' => $roles]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Role berhasil ditambahkan.',
            'data' => $role
        ]);
    }

    // Update role

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Role berhasil diperbarui.',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui role.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return response()->json([
                'status' => true,
                'message' => 'Role berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus role.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
