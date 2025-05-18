<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'company' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Update profile
        $user->company = $request->company;
        $user->email = $request->email;

        // Jika ada permintaan ganti password
        if ($request->filled('new_password')) {
            if (!\Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama salah.']);
            }
            $user->password = \Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }
}
