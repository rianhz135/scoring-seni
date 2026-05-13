<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function authenticate(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Coba login
        if (Auth::attempt($credentials)) {

            // regenerate session
            $request->session()->regenerate();

            $user = Auth::user();

            // =========================
            // LOGIN ADMIN
            // =========================
            if ($user->role == 'admin') {

                return redirect()->route('admin.dashboard');
            }

            // =========================
            // LOGIN JURI
            // =========================
            if ($user->role == 'juri') {

                // sementara arahkan ke solo
                return redirect()->route('juri.dashboard');
            }
        }

        // jika gagal login
        return back()->with('error', 'Username atau password salah');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}