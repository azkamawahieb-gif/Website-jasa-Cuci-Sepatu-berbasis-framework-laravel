<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AkunController extends Controller
{
    public function edit()
    {
        return view('petugas.akun');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/', Rule::unique('users', 'phone')->ignore($user->id)],
        ], [
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'phone.regex' => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'phone.unique' => 'Nomor HP ini sudah dipakai akun lain.',
        ]);

        $user->update($data);

        return back()->with('success', 'Akun berhasil diperbarui.');
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

        $request->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}