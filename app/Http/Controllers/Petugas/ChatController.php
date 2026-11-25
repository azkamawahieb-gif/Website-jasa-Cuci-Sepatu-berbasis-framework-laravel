<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // Daftar percakapan per pesanan, yang terbaru di atas
    public function index()
    {
        $ringkas = ChatMessage::query()
            ->whereNotNull('order_id')
            ->select(
                'order_id',
                DB::raw('max(id) as terakhir_id'),
                DB::raw('sum(case when sender_id = user_id and read_at is null then 1 else 0 end) as belum')
            )
            ->groupBy('order_id')
            ->orderByDesc('terakhir_id')
            ->get();

        $orders = Order::with(['user', 'items.service'])
            ->whereIn('id', $ringkas->pluck('order_id'))
            ->get()->keyBy('id');

        $terakhir = ChatMessage::whereIn('id', $ringkas->pluck('terakhir_id'))->get()->keyBy('id');

        return view('petugas.chat.index', compact('ringkas', 'orders', 'terakhir'));
    }

    // Satu percakapan = satu pesanan
    public function show(Order $order)
    {
        $this->tandaiDibaca($order);

        $order->load(['user', 'items.service']);

        $pesan = $order->chatMessages()
            ->orderByDesc('id')->limit(100)->get()->reverse()->values();

        return view('petugas.chat.show', compact('order', 'pesan'));
    }

    public function store(Request $request, Order $order)
    {
        $data = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            ['body.required' => 'Tulis pesan dulu.', 'body.max' => 'Pesan maksimal 1000 karakter.']
        );

        $pesan = $order->chatMessages()->create([
            'user_id' => $order->user_id,
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['pesan' => $pesan->untukTampilan(true)], 201);
        }

        return redirect()->route('petugas.chat.show', $order);
    }

    public function sejak(Request $request, Order $order)
    {
        $baru = $order->chatMessages()
            ->where('id', '>', (int) $request->query('setelah', 0))
            ->orderBy('id')->limit(50)->get();

        $this->tandaiDibaca($order);

        return response()->json([
            'pesan' => $baru->map(fn ($m) => $m->untukTampilan($m->sender_id !== $order->user_id))->values(),
        ]);
    }

    // Pesan dari pengguna = pengirimnya pengguna pemilik pesanan
    private function tandaiDibaca(Order $order): void
    {
        $order->chatMessages()
            ->whereColumn('sender_id', 'user_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}