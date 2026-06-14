<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //

    public function prosesRegister(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:12',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // dd($validatedData);

        // Simpan data pengguna ke database
        $user = \App\Models\User::create([
            'nama' => $validatedData['nama'],
            'no_hp' => $validatedData['no_hp'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        // Redirect atau kembalikan respons sesuai kebutuhan
        return redirect('/login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    public function prosesLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $redirectTo = $user && $user->role === 'admin' ? '/admin/dashboard' : '/reservasi';

            return redirect($redirectTo)->with(
                'success',
                'Login berhasil. Selamat datang! ' . $user->nama
            );
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }
}
