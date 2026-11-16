<?php
namespace App\Http\Controllers\Pengguna;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    // Form buat pesanan
    public function create(Request $request)
    {
        $layanan = Service::aktif()->orderBy('price')->get();
        $zona = DeliveryZone::aktif()->orderBy('fee')->get();
        $alamat = $request->user()->addresses()->get();

        // Layanan yang dipilih dari tombol "Pesan" di beranda
        $dipilih = $layanan->contains('id', (int) $request->query('layanan'))
            ? (int) $request->query('layanan')
            : null;

        return view('pengguna.pesanan.buat', compact('layanan', 'zona', 'dipilih', 'alamat'));
    }

    // Simpan pesanan
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:10'],
            'items.*.service_id' => ['required', Rule::exists('services', 'id')->where('is_active', true)],
            'items.*.shoe_name' => ['required', 'string', 'max:100'],
            'items.*.shoe_color' => ['nullable', 'string', 'max:50'],
            'items.*.shoe_size' => ['nullable', 'string', 'max:10'],
            'items.*.condition_notes' => ['nullable', 'string', 'max:500'],
            'items.*.before_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'jemput' => ['nullable', 'boolean'],
            'jemput_address' => ['required_if:jemput,1', 'nullable', 'string', 'max:500'],
            'jemput_maps_url' => ['nullable', 'url:http,https', 'max:500'],
            'jemput_zone' => ['required_if:jemput,1', 'nullable', Rule::exists('delivery_zones', 'id')->where('is_active', true)],
            'jemput_at' => ['nullable', 'date', 'after:now'],

            'antar' => ['nullable', 'boolean'],
            'antar_address' => ['required_if:antar,1', 'nullable', 'string', 'max:500'],
            'antar_maps_url' => ['nullable', 'url:http,https', 'max:500'],
            'antar_zone' => ['required_if:antar,1', 'nullable', Rule::exists('delivery_zones', 'id')->where('is_active', true)],
            'antar_at' => ['nullable', 'date', 'after:now'],

            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'items.required' => 'Tambahkan minimal satu sepatu.',
            'items.*.service_id.required' => 'Pilih layanan untuk setiap sepatu.',
            'items.*.service_id.exists' => 'Layanan yang dipilih tidak tersedia.',
            'items.*.shoe_name.required' => 'Nama sepatu wajib diisi.',
            'items.*.before_photo.image' => 'Foto harus berupa gambar.',
            'items.*.before_photo.max' => 'Ukuran foto maksimal 2 MB.',

            'jemput_address.required_if' => 'Alamat jemput wajib diisi.',
            'jemput_maps_url.url' => 'Tautan Google Maps jemput harus diawali http:// atau https://.',
            'antar_maps_url.url' => 'Tautan Google Maps antar harus diawali http:// atau https://.',
            'jemput_zone.required_if' => 'Pilih zona untuk penjemputan.',
            'antar_address.required_if' => 'Alamat antar wajib diisi.',
            'antar_zone.required_if' => 'Pilih zona untuk pengantaran.',
            'jemput_at.after' => 'Jadwal jemput harus di waktu yang akan datang.',
            'antar_at.after' => 'Jadwal antar harus di waktu yang akan datang.',
        ]);

        $order = DB::transaction(function () use ($request, $data) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => OrderStatus::MenungguKonfirmasi,
                'notes' => $data['notes'] ?? null,
            ]);

            // Sepatu
            $hariMaks = 1;

            foreach ($data['items'] as $i => $row) {
                $service = Service::findOrFail($row['service_id']);
                $foto = $request->file("items.$i.before_photo")?->store('sepatu/sebelum', 'public');

                $order->items()->create([
                    'service_id' => $service->id,
                    'shoe_name' => $row['shoe_name'],
                    'shoe_color' => $row['shoe_color'] ?? null,
                    'shoe_size' => $row['shoe_size'] ?? null,
                    'condition_notes' => $row['condition_notes'] ?? null,
                    'before_photo' => $foto,
                    'price' => $service->price,
                ]);

                $hariMaks = max($hariMaks, $service->estimated_days);
            }

            // Jemput / antar
            // Biaya selalu dihitung ulang dari database
            foreach (['jemput', 'antar'] as $tipe) {
                if (! $request->boolean($tipe)) {
                    continue;
                }

                $zona = DeliveryZone::findOrFail($data[$tipe . '_zone']);

                $order->deliveries()->create([
                    'type' => $tipe,
                    'address' => $data[$tipe . '_address'],
                    'maps_url' => $data[$tipe . '_maps_url'] ?? null,
                    'delivery_zone_id' => $zona->id,
                    'fee' => $zona->fee,
                    'scheduled_at' => $data[$tipe . '_at'] ?? null,
                ]);
            }

            $order->update([
                'estimated_done_at' => now()->addDays($hariMaks)
            ]);

            $order->hitungUlang();

            $order->logs()->create([
                'status' => OrderStatus::MenungguKonfirmasi->value,
                'note' => 'Pesanan dibuat',
                'user_id' => $request->user()->id,
            ]);

            return $order;
        });

        return redirect()->route('pesanan.show', $order)
            ->with('success', "Pesanan {$order->code} berhasil dibuat. Tunggu konfirmasi dari petugas.");
    }

    // Pesanan Saya
    public function index(Request $request)
    {
        // Kelompok status untuk filter kartu pesanan
        $grupStatus = [
            'menunggu' => ['menunggu_konfirmasi', 'menunggu_jemput'],
            'diproses' => ['diterima', 'dicuci'],
            'siap' => ['siap', 'dalam_pengantaran'],
            'selesai' => ['selesai'],
        ];

        $grupQuery = $request->query('grup');

        $grup = is_string($grupQuery) && isset($grupStatus[$grupQuery])
            ? $grupQuery
            : null;

        $tampil = $grup === 'selesai'
            ? 'riwayat'
            : ($grup ? 'berjalan' : $request->query('tampil', 'berjalan'));

        $orders = $request->user()->orders()
            ->withCount('items')
            ->when($grup, fn ($q) => $q->whereIn('status', $grupStatus[$grup]))
            ->when($tampil === 'berjalan', fn ($q) => $q->whereNotIn('status', ['selesai', 'dibatalkan']))
            ->when($tampil === 'riwayat', fn ($q) => $q->whereIn('status', ['selesai', 'dibatalkan']))
            ->when($request->query('q'), fn ($q, $kata) => $q->where('code', 'like', '%' . $kata . '%'))
            ->latest()
            ->simplePaginate(10)
            ->withQueryString();

        return view('pengguna.pesanan.index', compact('orders', 'tampil', 'grup'));
    }

    // Detail pesanan
    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load('items.service', 'deliveries.zone', 'logs');

        return view('pengguna.pesanan.show', compact('order'));
    }

    // Batalkan pesanan
    public function batal(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if (! $order->status->bisaDibatalkanPengguna()) {
            return back()->withErrors([
                'batal' => 'Pesanan ini sudah diproses sehingga tidak bisa dibatalkan.',
            ]);
        }

        $order->ubahStatus(
            OrderStatus::Dibatalkan,
            'Dibatalkan oleh pengguna',
            $request->user()->id
        );

        return back()->with('success', 'Pesanan dibatalkan.');
    }
}
