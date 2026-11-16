<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // Daftar percakapan, yang terbaru di atas
    public function index()
    {
        $ringkas = ChatMessage::query()
            ->select(
                'user_id',
                DB::raw('max(id) as terakhir_id'),
                DB::raw('sum(case when sender_id = user_id and read_at is null then 1 else 0 end) as belum')
            )
            ->groupBy('user_id')
            ->orderByDesc('terakhir_id')
            ->get();

        $users = User::whereIn('id', $ringkas->pluck('user_id'))->get()->keyBy('id');
        $terakhir = ChatMessage::whereIn('id', $ringkas->pluck('terakhir_id'))->get()->keyBy('id');

        return view('petugas.chat.index', compact('ringkas', 'users', 'terakhir'));
    }

    // Satu percakapan
    public function show(User $user)
    {
        abort_unless($user->isPengguna(), 404);

        $this->tandaiDibaca($user);

        $pesan = ChatMessage::where('user_id', $user->id)
            ->orderByDesc('id')->limit(100)->get()->reverse()->values();

        return view('petugas.chat.show', compact('user', 'pesan'));
    }

    public function store(Request $request, User $user)
    {
        abort_unless($user->isPengguna(), 404);

        $data = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            ['body.required' => 'Tulis pesan dulu.', 'body.max' => 'Pesan maksimal 1000 karakter.']
        );

        $pesan = ChatMessage::create([
            'user_id' => $user->id,
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['pesan' => $pesan->untukTampilan(true)], 201);
        }

        return redirect()->route('petugas.chat.show', $user);
    }

    public function sejak(Request $request, User $user)
    {
        abort_unless($user->isPengguna(), 404);

        $baru = ChatMessage::where('user_id', $user->id)
            ->where('id', '>', (int) $request->query('setelah', 0))
            ->orderBy('id')->limit(50)->get();

        $this->tandaiDibaca($user);

        return response()->json([
            // Bagi petugas, pesan "milik sendiri" = pesan yang pengirimnya bukan si pengguna
            'pesan' => $baru->map(fn ($m) => $m->untukTampilan($m->sender_id !== $user->id))->values(),
        ]);
    }

    // Pesan dari pengguna = pengirimnya pengguna itu sendiri
    private function tandaiDibaca(User $user): void
    {
        ChatMessage::where('user_id', $user->id)
            ->whereColumn('sender_id', 'user_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}