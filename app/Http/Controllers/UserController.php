<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
        public function index()
    {
        // Ambil semua user beserta relasi role
        $users = User::with('role')->get();
        // Ambil semua role untuk dropdown
        $roles = Role::all();
        return view('user.indexuser', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // Validasi konfirmasi password
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Buat user baru menggunakan model
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $request->role_id,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan user!'], 500);
        }
    }

    public function edit($id)
    {
        // Ambil user berdasarkan ID
        $user = User::findOrFail($id);
        return view('user.edituser', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed', // Validasi konfirmasi password
            'role_id' => 'required|exists:roles,id',
        ]);
    
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'role_id' => $request->role_id,
        ]);
    
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        try {
            // Cari user berdasarkan ID dan hapus
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'User berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus user!'], 500);
        }
    }
}
