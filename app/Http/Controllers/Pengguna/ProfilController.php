<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    // Halaman profil gaya Shopee (ringkasan + jumlah pesanan per kelompok status)
    public function edit(Request $request)
    {
        $perStatus = DB::table('orders')
            ->where('user_id', $request->user()->id)
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $hitung = [
            'menunggu' => $perStatus->only(['menunggu_konfirmasi', 'menunggu_jemput'])->sum(),
            'diproses' => $perStatus->only(['diterima', 'dicuci'])->sum(),
            'siap' => $perStatus->only(['siap', 'dalam_pengantaran'])->sum(),
            'selesai' => $perStatus->only(['selesai'])->sum(),
        ];

        // Menghitung pesan chat yang belum dibaca
        $chatBaru = \App\Models\ChatMessage::where('user_id', $request->user()->id)
            ->whereColumn('sender_id', '<>', 'user_id')
            ->whereNull('read_at')
            ->count();

        return view('pengguna.profil', compact('hitung', 'chatBaru'));
    }

    // Halaman "Akun Saya": ubah data diri, foto, bio, dan password
    public function akun()
    {
        return view('pengguna.profil-akun');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'address' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:160'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_avatar' => ['nullable', 'boolean'],
        ], [
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'phone.regex' => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'phone.unique' => 'Nomor HP ini sudah dipakai akun lain.',
            'bio.max' => 'Bio maksimal 160 karakter.',
            'avatar.image' => 'Foto profil harus berupa gambar.',
            'avatar.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatar', 'public');

        } elseif ($request->boolean('hapus_avatar') && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = null;
        }

        unset($data['hapus_avatar']);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.current_password' => 'Password lama salah.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
