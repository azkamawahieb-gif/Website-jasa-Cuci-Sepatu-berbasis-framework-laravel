<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Halaman chat: tampilkan 100 pesan terakhir dan tandai balasan petugas sudah dibaca
    public function index(Request $request)
    {
        $uid = $request->user()->id;

        $this->tandaiDibaca($uid);

        $pesan = ChatMessage::where('user_id', $uid)
            ->orderByDesc('id')->limit(100)->get()->reverse()->values();

        return view('pengguna.chat.index', compact('pesan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            ['body.required' => 'Tulis pesan dulu.', 'body.max' => 'Pesan maksimal 1000 karakter.']
        );

        $uid = $request->user()->id;

        $pesan = ChatMessage::create([
            'user_id' => $uid,
            'sender_id' => $uid,
            'body' => $data['body'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['pesan' => $pesan->untukTampilan(true)], 201);
        }

        return redirect()->route('chat.index');
    }

    // Dipanggil JavaScript tiap beberapa detik: pesan baru setelah id tertentu
    public function sejak(Request $request)
    {
        $uid = $request->user()->id;

        $baru = ChatMessage::where('user_id', $uid)
            ->where('id', '>', (int) $request->query('setelah', 0))
            ->orderBy('id')->limit(50)->get();

        $this->tandaiDibaca($uid);

        return response()->json([
            'pesan' => $baru->map(fn ($m) => $m->untukTampilan($m->sender_id === $uid))->values(),
        ]);
    }

    // Balasan petugas = pesan yang pengirimnya bukan pengguna itu sendiri
    private function tandaiDibaca(int $uid): void
    {
        ChatMessage::where('user_id', $uid)
            ->whereColumn('sender_id', '<>', 'user_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}