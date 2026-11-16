<?php

namespace App\Http\Controllers\Petugas;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = OrderStatus::tryFrom((string) $request->query('status'))?->value;

        $orders = Order::with(['user', 'deliveries'])
            ->withCount('items')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($request->query('q'), function ($q, $kata) {
                $q->where(function ($w) use ($kata) {
                    $w->where('code', 'like', "%{$kata}%")
                        ->orWhereHas('user', fn ($u) => $u
                            ->where('name', 'like', "%{$kata}%")
                            ->orWhere('phone', 'like', "%{$kata}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $hitung = DB::table('orders')
            ->select('status', DB::raw('count(*) as n'))
            ->groupBy('status')
            ->pluck('n', 'status');

        return view('petugas.pesanan.index', [
            'orders' => $orders,
            'status' => $status,
            'hitung' => $hitung,
            'semuaStatus' => OrderStatus::cases(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.service', 'deliveries.zone', 'logs.user');

        return view('petugas.pesanan.show', [
            'order' => $order,
            'langkah' => $this->labelLangkah($order),
            'bisaBatal' => $this->bisaBatal($order),
        ]);
    }

    // Langkah berikutnya sesuai status saat ini
    public function lanjut(Request $request, Order $order)
    {
        $catatan = $request->validate(['note' => ['nullable', 'string', 'max:255']])['note'] ?? null;
        $uid = $request->user()->id;
        $order->load('deliveries');
        $status = $order->status;

        if ($status === OrderStatus::MenungguKonfirmasi) {
            if ($order->jemput()) {
                $order->ubahStatus(OrderStatus::MenungguJemput, $catatan ?: 'Pesanan dikonfirmasi, menunggu penjemputan', $uid);
            } else {
                $order->ubahStatus(OrderStatus::Diterima, $catatan ?: 'Pesanan dikonfirmasi dan sepatu diterima', $uid);
            }
        } elseif ($status === OrderStatus::Diterima) {
            $order->ubahStatus(OrderStatus::Dicuci, $catatan ?: 'Sepatu mulai dicuci', $uid);
        } elseif ($status === OrderStatus::Dicuci) {
            $order->ubahStatus(OrderStatus::Siap, $catatan ?: 'Sepatu selesai dicuci', $uid);
        } elseif ($status === OrderStatus::Siap) {
            if ($antar = $order->antar()) {
                $antar->update(['status' => 'dalam_perjalanan', 'handled_by' => $uid]);
                $order->ubahStatus(OrderStatus::DalamPengantaran, $catatan ?: 'Sepatu dalam perjalanan diantar', $uid);
            } else {
                if ($order->payment_status !== 'lunas') {
                    return back()->withErrors(['aksi' => 'Catat pembayaran terlebih dahulu sebelum menyerahkan sepatu.']);
                }
                $order->ubahStatus(OrderStatus::Selesai, $catatan ?: 'Sepatu diambil pelanggan', $uid);
            }
        } else {
            return back()->withErrors(['aksi' => 'Tidak ada langkah berikutnya dari halaman ini untuk status tersebut.']);
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function batal(Request $request, Order $order)
    {
        $data = $request->validate(
            ['alasan' => ['required', 'string', 'max:255']],
            ['alasan.required' => 'Alasan pembatalan wajib diisi.']
        );

        if (! $this->bisaBatal($order)) {
            return back()->withErrors(['aksi' => 'Pesanan yang sudah dicuci tidak bisa dibatalkan.']);
        }

        DB::transaction(function () use ($order, $data, $request) {
            $order->deliveries()
                ->whereIn('status', ['dijadwalkan', 'dalam_perjalanan'])
                ->update(['status' => 'gagal']);

            $order->ubahStatus(OrderStatus::Dibatalkan, 'Dibatalkan petugas: ' . $data['alasan'], $request->user()->id);
        });

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function bayar(Request $request, Order $order)
    {
        $data = $request->validate(
            ['payment_method' => ['required', 'in:tunai,transfer,qris']],
            ['payment_method.required' => 'Pilih metode pembayaran.']
        );

        if ($order->status === OrderStatus::Dibatalkan) {
            return back()->withErrors(['aksi' => 'Pesanan yang dibatalkan tidak bisa dibayar.']);
        }
        if ($order->payment_status === 'lunas') {
            return back()->withErrors(['aksi' => 'Pesanan ini sudah lunas.']);
        }

        $order->update(['payment_status' => 'lunas', 'payment_method' => $data['payment_method']]);

        $order->logs()->create([
            'status' => $order->status->value,
            'note' => 'Pembayaran lunas via ' . $data['payment_method'] . ' (Rp ' . number_format($order->total, 0, ',', '.') . ')',
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Pembayaran dicatat lunas.');
    }

    // Ubah kondisi dan foto sepatu (sebelum/sesudah)
    public function item(Request $request, OrderItem $item)
    {
        $data = $request->validate([
            'condition_notes' => ['nullable', 'string', 'max:500'],
            'before_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'after_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'before_photo.image' => 'Foto sebelum harus berupa gambar.',
            'after_photo.image' => 'Foto sesudah harus berupa gambar.',
            'before_photo.max' => 'Ukuran foto maksimal 2 MB.',
            'after_photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $item->condition_notes = $data['condition_notes'] ?? null;

        foreach (['before_photo' => 'sepatu/sebelum', 'after_photo' => 'sepatu/sesudah'] as $kolom => $folder) {
            if ($request->hasFile($kolom)) {
                if ($item->{$kolom}) {
                    Storage::disk('public')->delete($item->{$kolom});
                }
                $item->{$kolom} = $request->file($kolom)->store($folder, 'public');
            }
        }

        $item->save();

        return back()->with('success', 'Data sepatu diperbarui.');
    }

    private function bisaBatal(Order $order): bool
    {
        return in_array($order->status, [
            OrderStatus::MenungguKonfirmasi,
            OrderStatus::MenungguJemput,
            OrderStatus::Diterima,
        ], true);
    }

    private function labelLangkah(Order $order): ?string
    {
        return match ($order->status) {
            OrderStatus::MenungguKonfirmasi => 'Konfirmasi Pesanan',
            OrderStatus::Diterima => 'Mulai Cuci',
            OrderStatus::Dicuci => 'Cuci Selesai',
            OrderStatus::Siap => $order->antar() ? 'Mulai Antar' : 'Sepatu Diambil Pelanggan',
            default => null,
        };
    }
}