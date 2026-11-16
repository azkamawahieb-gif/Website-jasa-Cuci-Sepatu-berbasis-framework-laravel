<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    // Halaman Pengaturan (ikon gigi di profil)
    public function index()
    {
        return view('pengguna.pengaturan.index');
    }

    // Ganti akun: keluar lalu langsung ke halaman login
    public function gantiAkun(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}