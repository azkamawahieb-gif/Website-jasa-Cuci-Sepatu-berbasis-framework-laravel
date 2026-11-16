<?php

namespace App\Http\Controllers\Petugas;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $tampil = $request->query('tampil', 'aktif');
        $tipe = $request->query('tipe');

        $tugas = Delivery::with('order.user', 'zone')
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
            ->when($tampil === 'aktif', fn ($q) => $q->whereIn('status', ['dijadwalkan', 'dalam_perjalanan']))
            ->when($tampil === 'selesai', fn ($q) => $q->where('status', 'selesai'))
            ->when(in_array($tipe, ['jemput', 'antar'], true), fn ($q) => $q->where('type', $tipe))
            ->orderByRaw('scheduled_at is null')
            ->orderBy('scheduled_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('petugas.pengantaran.index', compact('tugas', 'tampil', 'tipe'));
    }

    // Atur jadwal dan catatan
    public function update(Request $request, Delivery $delivery)
    {
        $data = $request->validate([
            'scheduled_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $delivery->update($data);

        return back()->with('success', 'Jadwal diperbarui.');
    }

    public function mulai(Request $request, Delivery $delivery)
    {
        $order = $delivery->order;
        $uid = $request->user()->id;

        if ($delivery->status !== 'dijadwalkan') {
            return back()->withErrors(['aksi' => 'Tugas ini sudah dimulai atau selesai.']);
        }

        if ($delivery->type === 'jemput') {
            if ($order->status !== OrderStatus::MenungguJemput) {
                return back()->withErrors(['aksi' => 'Penjemputan hanya bisa dimulai saat pesanan berstatus Menunggu Jemput.']);
            }
            $delivery->update(['status' => 'dalam_perjalanan', 'handled_by' => $uid]);
            $order->logs()->create([
                'status' => $order->status->value,
                'note' => 'Petugas berangkat menjemput sepatu',
                'user_id' => $uid,
            ]);
        } else {
            if ($order->status !== OrderStatus::Siap) {
                return back()->withErrors(['aksi' => 'Pengantaran hanya bisa dimulai saat pesanan berstatus Siap.']);
            }
            $delivery->update(['status' => 'dalam_perjalanan', 'handled_by' => $uid]);
            $order->ubahStatus(OrderStatus::DalamPengantaran, 'Sepatu dalam perjalanan diantar', $uid);
        }

        return back()->with('success', 'Tugas dimulai.');
    }

    public function selesai(Request $request, Delivery $delivery)
    {
        $request->validate([
            'proof_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $order = $delivery->order;
        $uid = $request->user()->id;

        if ($delivery->type === 'jemput' && $order->status !== OrderStatus::MenungguJemput) {
            return back()->withErrors(['aksi' => 'Penjemputan hanya bisa diselesaikan saat pesanan berstatus Menunggu Jemput.']);
        }

        if ($delivery->type === 'antar') {
            if ($order->status !== OrderStatus::DalamPengantaran) {
                return back()->withErrors(['aksi' => 'Mulai antar terlebih dahulu.']);
            }
            if ($order->payment_status !== 'lunas') {
                return back()->withErrors(['aksi' => 'Catat pembayaran di halaman pesanan sebelum menyerahkan sepatu.']);
            }
        }

        DB::transaction(function () use ($request, $delivery, $order, $uid) {
            $delivery->update([
                'status' => 'selesai',
                'completed_at' => now(),
                'handled_by' => $uid,
                'proof_photo' => $request->file('proof_photo')?->store('bukti', 'public') ?? $delivery->proof_photo,
                'note' => $request->input('note') ?: $delivery->note,
            ]);

            if ($delivery->type === 'jemput') {
                $order->ubahStatus(OrderStatus::Diterima, 'Sepatu dijemput dan diterima', $uid);
            } else {
                $order->ubahStatus(OrderStatus::Selesai, 'Sepatu diantar dan diserahkan', $uid);
            }
        });

        return back()->with('success', 'Tugas diselesaikan.');
    }
}