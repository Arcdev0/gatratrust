<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login.login'); // Sesuaikan view jika perlu
    }

    public function login(Request $request)
    {
        // Validasi menggunakan 'name' dan 'password'
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required'],
        ]);

       if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('projects.tampilan'));
        }

        return back()->withErrors([
            'name' => 'Nama pengguna atau password salah.',
        ])->onlyInput('name');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
