@extends('layouts.pengguna')
@section('title', 'Profil')
@push('css')
<link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
@endpush

@section('content')
@php
  $user = auth()->user();

  $grupPesanan = [
    'menunggu' => ['Menunggu', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
    'diproses' => ['Diproses', '<path d="M12 3s6 6.2 6 10.5A6 6 0 0 1 6 13.5C6 9.2 12 3 12 3z"/>'],
    'siap'     => ['Siap / Diantar', '<path d="M3 6h11v10H3z"/><path d="M14 9h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>'],
    'selesai'  => ['Selesai', '<circle cx="12" cy="12" r="9"/><path d="m8 12.5 2.6 2.6L16 9.5"/>'],
  ];
@endphp

<div class="profil-halaman">

  {{-- Kepala profil --}}
  <header class="profil-hero anim-naik">
    <div class="profil-hero-isi">
      <span class="profil-avatar">
        @if ($user->avatarUrl())
          <img src="{{ $user->avatarUrl() }}" alt="Foto profil {{ $user->name }}">
        @else
          <span aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
        @endif
      </span>

      <div class="profil-identitas">
        <h1>{{ $user->name }}</h1>
        <p>{{ $user->bio ?: $user->email }}</p>
      </div>

      <div class="profil-ikon">
        @if (Route::has('chat.index'))
          <a href="{{ route('chat.index') }}" class="ikon-tombol" aria-label="Chat dengan petugas">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.4A8 8 0 1 1 21 12z"/></svg>
          </a>
        @endif
        @if (Route::has('notifikasi.index'))
          <a href="{{ route('notifikasi.index') }}" class="ikon-tombol" aria-label="Notifikasi">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 16v-5a6 6 0 1 0-12 0v5l-2 2h16z"/><path d="M10 21h4"/></svg>
          </a>
        @endif
        <a href="{{ route('pengaturan.index') }}" class="ikon-tombol" aria-label="Pengaturan">
          <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
        </a>
      </div>
    </div>
  </header>

  {{-- Pesanan Saya --}}
  <section class="profil-kartu anim-naik" style="--i:1" aria-labelledby="judul-pesanan">
    <div class="profil-kartu-kepala">
      <h2 id="judul-pesanan">Pesanan Saya</h2>
      <a href="{{ route('pesanan.index', ['tampil' => 'riwayat']) }}">Lihat Riwayat Pesanan &rsaquo;</a>
    </div>

    <div class="status-grid">
      @foreach ($grupPesanan as $kunci => [$label, $ikon])
        <a href="{{ route('pesanan.index', ['grup' => $kunci]) }}" class="status-item">
          <span class="status-ikon">
            <svg viewBox="0 0 24 24" aria-hidden="true">{!! $ikon !!}</svg>
            @if (($hitung[$kunci] ?? 0) > 0)
              <span class="status-badge" aria-label="{{ $hitung[$kunci] }} pesanan">{{ $hitung[$kunci] }}</span>
            @endif
          </span>
          <span class="status-label">{{ $label }}</span>
        </a>
      @endforeach
    </div>

    <a href="{{ route('pesanan.buat') }}" class="btn btn-block">Buat Pesanan Baru</a>
  </section>
</div>
@endsection