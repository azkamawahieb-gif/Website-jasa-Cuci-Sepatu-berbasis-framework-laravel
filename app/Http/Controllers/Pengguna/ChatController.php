<?php

namespace App\Http\Controllers\Pengguna;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Menu Chat: langsung buka chat pesanan yang sedang berjalan (atau pesanan terakhir)
    public function index(Request $request)
    {
        $pesanan = Order::where('user_id', $request->user()->id);

        $order = (clone $pesanan)
            ->whereNotIn('status', [OrderStatus::Selesai->value, OrderStatus::Dibatalkan->value])
            ->latest('id')->first()
            ?? $pesanan->latest('id')->first();

        if (! $order) {
            return view('pengguna.chat.index');
        }

        return redirect()->route('chat.order', $order);
    }

    // Chat satu pesanan: 100 pesan terakhir, balasan petugas ditandai sudah dibaca
    public function show(Request $request, Order $order)
    {
        $this->pastikanMilik($request, $order);
        $this->tandaiDibaca($order);

        $order->load('items.service');

        $pesan = $order->chatMessages()
            ->orderByDesc('id')->limit(100)->get()->reverse()->values();

        // Daftar pesanan pengguna untuk tab pilihan, lengkap dengan jumlah balasan belum dibaca
        $daftarPesanan = Order::where('user_id', $request->user()->id)
            ->with('deliveries')
            ->withCount(['chatMessages as belum_dibaca' => fn ($q) => $q
                ->whereColumn('sender_id', '<>', 'user_id')
                ->whereNull('read_at')])
            ->latest('id')->limit(10)->get();

        return view('pengguna.chat.show', compact('order', 'pesan', 'daftarPesanan'));
    }

    public function store(Request $request, Order $order)
    {
        $this->pastikanMilik($request, $order);

        $data = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            ['body.required' => 'Tulis pesan dulu.', 'body.max' => 'Pesan maksimal 1000 karakter.']
        );

        $uid = $request->user()->id;

        $pesan = $order->chatMessages()->create([
            'user_id' => $uid,
            'sender_id' => $uid,
            'body' => $data['body'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['pesan' => $pesan->untukTampilan(true)], 201);
        }

        return redirect()->route('chat.order', $order);
    }

    // Dipanggil JavaScript tiap beberapa detik: pesan baru setelah id tertentu
    public function sejak(Request $request, Order $order)
    {
        $this->pastikanMilik($request, $order);

        $uid = $request->user()->id;

        $baru = $order->chatMessages()
            ->where('id', '>', (int) $request->query('setelah', 0))
            ->orderBy('id')->limit(50)->get();

        $this->tandaiDibaca($order);

        return response()->json([
            'pesan' => $baru->map(fn ($m) => $m->untukTampilan($m->sender_id === $uid))->values(),
        ]);
    }

    // Pengguna hanya boleh membuka chat pesanannya sendiri
    private function pastikanMilik(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 404);
    }

    // Balasan petugas = pesan yang pengirimnya bukan pengguna itu sendiri
    private function tandaiDibaca(Order $order): void
    {
        $order->chatMessages()
            ->whereColumn('sender_id', '<>', 'user_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
