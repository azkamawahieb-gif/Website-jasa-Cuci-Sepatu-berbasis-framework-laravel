@extends('layouts.pengguna')
@section('title', 'Chat dengan Petugas')
@push('css')
<link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endpush

@section('content')
<div class="profil-halaman">

  <div class="halaman-judul anim-naik">
    <a href="{{ route('profil') }}" class="kembali" aria-label="Kembali ke profil">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
    </a>
    <h1>Chat dengan Petugas</h1>
  </div>

  <section class="profil-kartu anim-naik" style="--i:1" aria-label="Chat dengan petugas">
    <p class="chat-kosong">
      Chat terhubung ke pesanan Anda. Buat pesanan dulu, lalu chat dengan petugas
      tentang sepatu yang Anda titipkan akan muncul di sini.
    </p>
    <p style="text-align:center;margin:0">
      <a href="{{ route('pesanan.buat') }}" class="btn">Buat pesanan</a>
    </p>
  </section>
</div>
@endsection