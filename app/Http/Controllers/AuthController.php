<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->arahkan($request->user());
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/', 'unique:users,phone'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone.regex' => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // Role TIDAK diambil dari form: akun daftar selalu "pengguna".
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => Role::Pengguna,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('beranda'))
            ->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }

    // Petugas selalu ke dashboard. Pengguna kembali ke halaman yang
    // tadi dituju (misalnya form pesanan), atau ke beranda.
    private function arahkan(User $user)
    {
        if ($user->isPetugas()) {
            return redirect()->route('petugas.dashboard');
        }

        return redirect()->intended(route('beranda'));
    }
}