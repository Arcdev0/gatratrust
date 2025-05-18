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
        // Validate and update the user's profile
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            // Add other validation rules as needed
        ]);

        $user = auth()->user();
        $user->update($request->only('name', 'email'));

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }
    public function changePassword(Request $request)
    {
        // Validate and change the user's password
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!password_verify($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => bcrypt($request->new_password)]);

        return redirect()->route('profile.index')->with('success', 'Password changed successfully.');
    }
}
