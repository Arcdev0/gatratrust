<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get()->map(function ($user) {
            $user->status = $user->is_active ? 'Active' : 'Non Active';
            return $user;
        });
        $roles = Role::all();
        $clients = User::whereHas('role', function ($query) {
            $query->where('name', 'client');
        })->get();

        return view('user.tampilan', compact('users', 'roles', 'clients'));
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
            return redirect()->route('user.tampilan')->with('success', 'User berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('user.tampilan')->with('error', 'User tidak dapat dihapus karena masih digunakan di data lain.');
        }
    }

}
