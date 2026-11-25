@extends('layouts.petugas')
@section('title', 'Chat: ' . $order->code)

@section('content')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">

<div class="d-flex align-items-center flex-wrap gap-2 mb-3">
  <a href="{{ route('petugas.chat.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Chat</a>
  <h1 class="h4 mb-0">{{ $order->user->name }}</h1>
  <span class="text-body-secondary">{{ $order->user->phone }}</span>
  <a href="{{ route('petugas.pengguna.show', $order->user) }}" class="ms-auto small">Lihat pengguna</a>
</div>

<div class="card">
  <div class="card-body">
    @include('chat._pesanan', ['order' => $order, 'urlDetail' => route('petugas.pesanan.show', $order)])

    @include('chat._panel', [
      'pesan' => $pesan,
      'milik' => fn ($m) => $m->sender_id !== $order->user_id,
      'urlKirim' => route('petugas.chat.kirim', $order),
      'urlSejak' => route('petugas.chat.sejak', $order),
      'kosongTeks' => 'Belum ada pesan untuk pesanan ini.',
    ])
  </div>
</div>
@endsection