<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    // Cek kode voucher dari form pesanan (balasan JSON).
    // Potongan final tetap dihitung ulang di server saat pesanan disimpan.
    public function cek(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:30'],
            'subtotal' => ['nullable', 'integer', 'min:0'],
        ], [
            'kode.required' => 'Masukkan kode voucher.',
        ]);

        $voucher = Voucher::cari($data['kode']);

        if (! $voucher) {
            return response()->json(['ok' => false, 'pesan' => 'Kode voucher tidak ditemukan.'], 422);
        }

        // Syarat minimal belanja dicek di browser seiring isi pesanan berubah,
        // jadi di sini hanya status aktif, masa berlaku, dan kuota.
        if ($pesan = $voucher->galat(max((int) ($data['subtotal'] ?? 0), $voucher->min_order))) {
            return response()->json(['ok' => false, 'pesan' => $pesan], 422);
        }

        return response()->json([
            'ok' => true,
            'kode' => $voucher->code,
            'ringkasan' => $voucher->ringkasan(),
            'keterangan' => $voucher->description,
            'tipe' => $voucher->type,
            'nilai' => $voucher->value,
            'min_order' => $voucher->min_order,
            'maks' => $voucher->max_discount,
        ]);
    }
}