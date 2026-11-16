@extends('layouts.pengguna')
@section('title', 'Pengaturan')
@push('css')
<link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
@endpush

@section('content')
@php
  $akun = [
    ['Akun Saya', 'Foto, bio, data diri, dan password', route('profil.akun'),
      '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>'],
    ['Alamat Saya', 'Ubah alamat atau buat alamat baru', route('alamat.index'),
      '<path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>'],
    ['Semua Pesanan', 'Pesanan berjalan dan riwayat', route('pesanan.index'),
      '<path d="M7 3h10v18l-2.5-1.5L12 21l-2.5-1.5L7 21z"/><path d="M10 8h4M10 12h4"/>'],
  ];
@endphp

<div class="profil-halaman">

  <div class="halaman-judul anim-naik">
    <a href="{{ route('profil') }}" class="kembali" aria-label="Kembali ke profil">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
    </a>
    <h1>Pengaturan</h1>
  </div>

  {{-- Akun, alamat, pesanan --}}
  <section class="profil-kartu profil-menu anim-naik" style="--i:1" aria-labelledby="judul-akun">
    <h2 class="menu-judul" id="judul-akun">Akun</h2>
    @foreach ($akun as [$judul, $sub, $url, $ikon])
      <a href="{{ $url }}" class="menu-baris">
        <span class="menu-ikon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $ikon !!}</svg></span>
        <span class="menu-teks"><strong>{{ $judul }}</strong><small>{{ $sub }}</small></span>
        <svg class="menu-panah" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
      </a>
    @endforeach
  </section>

  {{-- Notifikasi: tampil otomatis setelah Tahap J --}}
  @if (Route::has('notifikasi.pengaturan'))
    <section class="profil-kartu profil-menu anim-naik" style="--i:2" aria-labelledby="judul-notif">
      <h2 class="menu-judul" id="judul-notif">Notifikasi</h2>
      <a href="{{ route('notifikasi.pengaturan') }}" class="menu-baris">
        <span class="menu-ikon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 16v-5a6 6 0 1 0-12 0v5l-2 2h16z"/><path d="M10 21h4"/></svg></span>
        <span class="menu-teks"><strong>Pengaturan Notifikasi</strong><small>Notifikasi pesanan dan promo</small></span>
        <svg class="menu-panah" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
      </a>
    </section>
  @endif

  {{-- Ganti akun / keluar --}}
  <section class="profil-kartu profil-menu anim-naik" style="--i:3" aria-labelledby="judul-sesi">
    <h2 class="menu-judul" id="judul-sesi">Sesi</h2>

    <form method="POST" action="{{ route('pengaturan.ganti') }}">
      @csrf
      <button type="submit" class="menu-baris menu-tombol">
        <span class="menu-ikon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 3l4 4-4 4"/><path d="M20 7H9a4 4 0 0 0-4 4"/><path d="M8 21l-4-4 4-4"/><path d="M4 17h11a4 4 0 0 0 4-4"/></svg></span>
        <span class="menu-teks"><strong>Ganti Akun</strong><small>Keluar lalu masuk dengan akun lain</small></span>
        <svg class="menu-panah" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
      </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="menu-baris menu-tombol menu-bahaya">
        <span class="menu-ikon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg></span>
        <span class="menu-teks"><strong>Keluar</strong><small>Akhiri sesi di perangkat ini</small></span>
      </button>
    </form>
  </section>
</div>
@endsection