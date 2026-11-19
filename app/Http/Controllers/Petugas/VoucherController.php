<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function index()
    {
        return view('petugas.voucher.index', [
            'voucher' => Voucher::orderByDesc('is_active')->orderByDesc('id')->get(),
        ]);
    }

    public function create()
    {
        return view('petugas.voucher.form', ['voucher' => new Voucher()]);
    }

    public function store(Request $request)
    {
        Voucher::create($this->validasi($request));

        return redirect()->route('petugas.voucher.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher)
    {
        return view('petugas.voucher.form', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $voucher->update($this->validasi($request, $voucher));

        return redirect()->route('petugas.voucher.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function toggle(Voucher $voucher)
    {
        $voucher->update(['is_active' => ! $voucher->is_active]);

        return back()->with('success', 'Status voucher diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        // Voucher yang sudah pernah dipakai disimpan sebagai riwayat, cukup dinonaktifkan
        if ($voucher->used_count > 0) {
            return back()->withErrors([
                'hapus' => 'Voucher ini sudah pernah dipakai, jadi tidak bisa dihapus. Nonaktifkan saja.',
            ]);
        }

        $voucher->delete();

        return back()->with('success', 'Voucher dihapus.');
    }

    private function validasi(Request $request, ?Voucher $voucher = null): array
    {
        // Kode selalu huruf besar supaya cocok dengan pencarian di sisi pengguna
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('vouchers', 'code')->ignore($voucher?->id)],
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['nominal', 'persen'])],
            'value' => ['required', 'integer', 'min:1',
                $request->input('type') === 'persen' ? 'max:100' : 'max:10000000'],
            'min_order' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'max_discount' => ['nullable', 'integer', 'min:1', 'max:10000000'],
            'quota' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ], [
            'code.required' => 'Kode voucher wajib diisi.',
            'code.regex' => 'Kode hanya boleh huruf, angka, strip (-), dan garis bawah (_), tanpa spasi.',
            'code.unique' => 'Kode ini sudah dipakai voucher lain.',
            'description.required' => 'Nama voucher wajib diisi.',
            'value.required' => 'Besar potongan wajib diisi.',
            'value.max' => $request->input('type') === 'persen'
                ? 'Potongan persen maksimal 100.' : 'Potongan terlalu besar.',
            'ends_at.after' => 'Tanggal berakhir harus setelah tanggal mulai.',
        ]);

        $data['min_order'] = $data['min_order'] ?? 0;
        // Batas potongan maksimal hanya berlaku untuk tipe persen
        if ($data['type'] !== 'persen') {
            $data['max_discount'] = null;
        }
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}