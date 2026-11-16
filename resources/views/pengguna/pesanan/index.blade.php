@extends('layouts.pengguna')
@section('title', 'Pesanan Saya')
@push('css')
<link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
@endpush

@section('content')
@php
  // Perkiraan kemajuan untuk bilah mini (hanya tampilan)
  $persen = [
    'menunggu_konfirmasi' => 10, 'menunggu_jemput' => 25, 'diterima' => 40, 'dicuci' => 60,
    'siap' => 80, 'dalam_pengantaran' => 90, 'selesai' => 100, 'dibatalkan' => 100,
  ];
  $kata = request('q');

  $namaGrup = [
    'menunggu' => 'Menunggu',
    'diproses' => 'Diproses',
    'siap' => 'Siap / Diantar',
    'selesai' => 'Selesai',
  ];
@endphp

<div class="area">
  <div class="halaman-judul anim-naik">
    <a href="{{ route('profil') }}" class="kembali" aria-label="Kembali ke profil">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
    </a>
    <h1>{{ $grup ? 'Pesanan ' . $namaGrup[$grup] : 'Pesanan Saya' }}</h1>
  </div>

  {{-- Filter dan pencarian hanya tampil bila tidak dibuka dari kartu status --}}
  @unless ($grup)
    <div class="bar-filter">
      <div class="filter" role="group" aria-label="Filter pesanan">
        <a href="{{ route('pesanan.index', ['tampil' => 'berjalan']) }}" class="{{ $tampil === 'berjalan' ? 'aktif' : '' }}" @if ($tampil === 'berjalan') aria-current="true" @endif>Berjalan</a>
        <a href="{{ route('pesanan.index', ['tampil' => 'riwayat']) }}" class="{{ $tampil === 'riwayat' ? 'aktif' : '' }}" @if ($tampil === 'riwayat') aria-current="true" @endif>Riwayat</a>
        <a href="{{ route('pesanan.index', ['tampil' => 'semua']) }}" class="{{ $tampil === 'semua' ? 'aktif' : '' }}" @if ($tampil === 'semua') aria-current="true" @endif>Semua</a>
      </div>

      <form method="GET" action="{{ route('pesanan.index') }}" class="cari-pesanan" role="search">
        <input type="hidden" name="tampil" value="{{ $tampil }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.500-3.500"/></svg>
        <label for="q" class="sembunyi-label">Cari kode pesanan</label>
        <input type="text" id="q" name="q" value="{{ $kata }}" class="form-control" placeholder="Cari kode pesanan">
      </form>
    </div>
  @endunless

  @if ($orders->isEmpty())
    @if ($grup)
      <p class="kosong-polos">Belum ada pesanan.</p>
    @else
      <div class="kosong anim-naik">
        <span class="kosong-ikon" aria-hidden="true">@include('auth._sepatu')</span>
        @if ($kata)
          <h2>Pesanan tidak ditemukan</h2>
          <p>Tidak ada pesanan dengan kode &ldquo;{{ $kata }}&rdquo;. Periksa lagi kodenya atau lihat semua pesanan.</p>
          <a href="{{ route('pesanan.index', ['tampil' => 'semua']) }}" class="btn btn-outline">Lihat semua pesanan</a>
        @elseif ($tampil === 'riwayat')
          <h2>Belum ada riwayat</h2>
          <p>Pesanan yang sudah selesai atau dibatalkan akan tampil di sini.</p>
        @elseif ($tampil === 'berjalan')
          <h2>Tidak ada pesanan berjalan</h2>
          <p>Sepatu Anda yang sedang diproses akan muncul di sini lengkap dengan statusnya.</p>
          <a href="{{ route('pesanan.buat') }}" class="btn">Buat pesanan</a>
        @else
          <h2>Belum ada pesanan</h2>
          <p>Yuk titipkan sepatu pertama Anda. Kami jemput, cuci, lalu antar kembali.</p>
          <a href="{{ route('pesanan.buat') }}" class="btn">Buat pesanan</a>
        @endif
      </div>
    @endif
  @else
    <ul class="pesanan-daftar">
      @foreach ($orders as $o)
        <li class="pesanan-kartu anim-naik" style="--i:{{ min($loop->index, 8) }}">
          <div>
            <a href="{{ route('pesanan.show', $o) }}" class="pk-kode">{{ $o->code }}</a>
            <span class="pk-meta">{{ $o->created_at->format('d M Y') }} &middot; {{ $o->items_count }} pasang</span>
          </div>

          <div class="pk-status">
            <span class="badge badge-{{ $o->status->color() }}">{{ $o->status->label() }}</span>
            <span class="progres-mini" aria-hidden="true">
              <span class="progres-isi {{ $o->status->value === 'dibatalkan' ? 'batal' : '' }}" style="--pct: {{ $persen[$o->status->value] ?? 0 }}%"></span>
            </span>
          </div>

          <div class="pk-aksi">
            <span class="pk-total">Rp {{ number_format($o->total, 0, ',', '.') }}</span>
            <a href="{{ route('pesanan.show', $o) }}" class="btn btn-outline btn-kecil" aria-label="Detail pesanan {{ $o->code }}">Detail</a>
          </div>
        </li>
      @endforeach
    </ul>

    <div class="halaman">
      <span>@if ($orders->previousPageUrl()) <a href="{{ $orders->previousPageUrl() }}" class="btn btn-outline btn-kecil">&laquo; Sebelumnya</a> @endif</span>
      <span>@if ($orders->nextPageUrl()) <a href="{{ $orders->nextPageUrl() }}" class="btn btn-outline btn-kecil">Berikutnya &raquo;</a> @endif</span>
    </div>
  @endif
</div>
@endsection