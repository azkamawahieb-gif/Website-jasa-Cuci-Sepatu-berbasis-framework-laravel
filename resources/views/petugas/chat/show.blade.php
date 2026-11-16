@extends('layouts.petugas')
@section('title', 'Chat: ' . $user->name)

@section('content')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">

<div class="d-flex align-items-center flex-wrap gap-2 mb-3">
  <a href="{{ route('petugas.chat.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Chat</a>
  <h1 class="h4 mb-0">{{ $user->name }}</h1>
  <span class="text-body-secondary">{{ $user->phone }}</span>
  <a href="{{ route('petugas.pengguna.show', $user) }}" class="ms-auto small">Lihat pengguna</a>
</div>

<div class="card">
  <div class="card-body">
    @include('chat._panel', [
      'pesan' => $pesan,
      'milik' => fn ($m) => $m->sender_id !== $user->id,
      'urlKirim' => route('petugas.chat.kirim', $user),
      'urlSejak' => route('petugas.chat.sejak', $user),
      'kosongTeks' => 'Belum ada pesan dari pengguna ini.',
    ])
  </div>
</div>
@endsection