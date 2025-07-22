<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        return view('user.tampilan', compact('roles'));
    }


    public function getListTable()
    {
        $users = User::with('role')->select('users.*');

        return DataTables::of($users)
            ->addIndexColumn() // nomor urut otomatis
            ->editColumn('status', function ($user) {
                return $user->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Non Active</span>';
            })
            ->addColumn('role', function ($user) {
                return $user->role ? $user->role->name : '-';
            })
            ->addColumn('actions', function ($user) {
                return '
                <button type="button" class="btn btn-sm btn-secondary btnEditUser"
                    data-id="' . $user->id . '"
                    data-name="' . $user->name . '"
                    data-email="' . $user->email . '"
                    data-role="' . $user->role_id . '"
                    data-company="' . ($user->company ?? '') . '"
                    data-status="' . $user->is_active . '">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger btnDeleteUser"
                    data-id="' . $user->id . '">
                    <i class="fas fa-trash"></i>
                </button>
            ';
            })
            ->rawColumns(['status', 'actions']) // biar HTML badge dan button tidak di-escape
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'company' => 'nullable|string',
            'is_active' => 'boolean'

        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'company' => $request->company,
            'is_active' => $request->is_active
        ]);

        return redirect()->route('user.tampilan')->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|unique:users,name,' . $user->id,
            'email' => 'required|email',
            'password' => 'nullable|min:6',
            'role_id' => 'required|exists:roles,id',
            'company' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'company' => $request->company,
            'is_active' => $request->is_active
        ];

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('user.tampilan')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus user.'
            ], 500);
        }
    }
}
