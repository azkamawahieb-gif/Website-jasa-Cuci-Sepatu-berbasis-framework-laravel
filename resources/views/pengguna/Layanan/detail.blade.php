@extends('layouts.pengguna')
@section('title', $layanan->name)

@push('css')
  <link rel="stylesheet" href="{{ asset('css/beranda.css') }}">
  <link rel="stylesheet" href="{{ asset('css/layanan.css') }}">
@endpush

@section('content')
@php
  $foto = $layanan->fotoUrl();
  $bagian = $detail['bagian'] ?? [];
  $kunci = array_column($bagian, 'kunci');
  $namaBagian = array_column($bagian, 'nama', 'kunci');
  $ada = fn ($k) => in_array($k, $kunci, true);
  $no = fn ($k) => array_search($k, $kunci, true) + 1;
  $atr = fn ($k) => $ada($k)
      ? 'data-zona="'.$k.'" tabindex="0" role="button" aria-label="'.e($namaBagian[$k]).'"'
      : 'aria-hidden="true"';
@endphp

{{-- ================= HERO DETAIL ================= --}}
<section class="ld-hero anim-naik">
  <nav class="ld-crumb" aria-label="Jejak halaman">
    <a href="{{ route('beranda') }}">Beranda</a>
    <span aria-hidden="true">/</span>
    <a href="{{ route('beranda') }}#layanan">Layanan</a>
    <span aria-hidden="true">/</span>
    <span>{{ $layanan->name }}</span>
  </nav>

  <div class="ld-hero-isi">
    <div class="ld-teks">
      <span class="hero-label">Layanan</span>
      <h1>{{ $layanan->name }}</h1>
      <p class="ld-ringkas">{{ $detail['ringkasan'] ?? $layanan->description }}</p>

      <div class="ld-meta">
        <span class="ld-chip">Rp {{ number_format($layanan->price, 0, ',', '.') }} / pasang</span>
        <span class="ld-chip">Estimasi {{ $layanan->estimated_days }} hari</span>
      </div>

      <div class="hero-aksi">
        <a href="{{ route('pesanan.buat', ['layanan' => $layanan->id]) }}" class="btn btn-lg">Pesan layanan ini</a>
        @if ($bagian)
          <a href="#bagian" class="btn btn-ghost btn-lg">Lihat bagian yang dibersihkan</a>
        @endif
      </div>
    </div>

    <div class="ld-foto">
      @if ($foto)
        <img src="{{ $foto }}" alt="Foto layanan {{ $layanan->name }}" width="800" height="600">
      @else
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3s6 6.2 6 10.5A6 6 0 0 1 6 13.5C6 9.2 12 3 12 3z"/></svg>
      @endif
    </div>
  </div>
</section>

{{-- ================= BAGIAN YANG DIBERSIHKAN (INTERAKTIF) ================= --}}
<section id="bagian" class="bagian" @if ($bagian) data-bagian-akar @endif>
  <div class="bagian-kepala reveal">
    <span class="eyebrow">Cakupan</span>
    <h2>Bagian yang dibersihkan</h2>
    @if ($bagian)
      <p>Layanan {{ $layanan->name }} meliputi bagian berikut. Sentuh bagian pada gambar atau pilih dari daftar.</p>
    @endif
  </div>

  @if ($bagian)
    <div class="ld-interaktif">
      <div class="ld-diagram reveal">
        <svg viewBox="0 0 520 300" role="group" aria-label="Gambar sepatu, pilih bagian untuk melihat penjelasan">
          <ellipse cx="268" cy="284" rx="230" ry="9" fill="#0B2545" opacity=".1"/>

          {{-- Outsole --}}
          <g class="zona z-outsole {{ $ada('outsole') ? '' : 'nonaktif' }}" {!! $atr('outsole') !!}>
            <path class="bentuk" style="--f:#6394D3" d="M40 244 H484 Q496 244 496 256 Q496 268 484 268 H52 Q40 268 40 256 Z"/>
            @if ($ada('outsole'))
              <g transform="translate(420 256)"><circle class="pin-ring" r="11"/><circle class="pin" r="11"/><text class="pin-no" y="4.5">{{ $no('outsole') }}</text></g>
            @endif
          </g>

          {{-- Midsole --}}
          <g class="zona z-midsole {{ $ada('midsole') ? '' : 'nonaktif' }}" {!! $atr('midsole') !!}>
            <path class="bentuk" style="--f:#E8FFF7" d="M42 216 H482 Q494 216 494 228 V244 H40 V218 Z"/>
            @if ($ada('midsole'))
              <g transform="translate(150 230)"><circle class="pin-ring" r="11"/><circle class="pin" r="11"/><text class="pin-no" y="4.5">{{ $no('midsole') }}</text></g>
            @endif
          </g>

          {{-- Upper --}}
          <g class="zona z-upper {{ $ada('upper') ? '' : 'nonaktif' }}" {!! $atr('upper') !!}>
            <path class="bentuk" style="--f:#FFFFFF" d="M44 216 L44 124 C44 106 56 96 72 94 L132 88 C150 86 162 94 172 106 C194 134 232 148 276 156 C344 168 444 174 480 198 C490 204 490 216 480 216 Z"/>
            @if ($ada('upper'))
              <g transform="translate(370 196)"><circle class="pin-ring" r="11"/><circle class="pin" r="11"/><text class="pin-no" y="4.5">{{ $no('upper') }}</text></g>
            @endif
          </g>

          {{-- Bagian dalam --}}
          <g class="zona z-dalam {{ $ada('dalam') ? '' : 'nonaktif' }}" {!! $atr('dalam') !!}>
            <ellipse class="bentuk" style="--f:#BDEFD6" cx="92" cy="96" rx="42" ry="10" transform="rotate(-4 92 96)"/>
            @if ($ada('dalam'))
              <g transform="translate(92 72)"><circle class="pin-ring" r="11"/><circle class="pin" r="11"/><text class="pin-no" y="4.5">{{ $no('dalam') }}</text></g>
            @endif
          </g>

          {{-- Tali sepatu --}}
          <g class="zona z-tali {{ $ada('tali') ? '' : 'nonaktif' }}" {!! $atr('tali') !!}>
            <path class="bentuk" style="--f:transparent" d="M160 100 L262 148 L240 168 L146 126 Z"/>
            <path class="tali-garis" d="M186 105 L170 127 M204 114 L188 136 M222 122 L206 144 M240 131 L224 153"/>
            @if ($ada('tali'))
              <g transform="translate(205 90)"><circle class="pin-ring" r="11"/><circle class="pin" r="11"/><text class="pin-no" y="4.5">{{ $no('tali') }}</text></g>
            @endif
          </g>
        </svg>
      </div>

      <div class="ld-daftar">
        <ul class="ld-list">
          @foreach ($bagian as $b)
            <li>
              <button type="button" class="ld-bagian {{ $loop->first ? 'aktif' : '' }}"
                      data-bagian="{{ $b['kunci'] }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                <span class="no">{{ $loop->iteration }}</span>
                <span>{{ $b['nama'] }}</span>
              </button>
            </li>
          @endforeach
        </ul>

        <div aria-live="polite">
          @foreach ($bagian as $b)
            <div class="ld-panel" data-panel="{{ $b['kunci'] }}" @if (! $loop->first) hidden @endif>
              <h3>{{ $b['nama'] }}</h3>
              <p>{{ $b['teks'] }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @else
    <p class="kosong">Rincian layanan ini belum tersedia.</p>
  @endif
</section>

{{-- ================= COCOK UNTUK + CATATAN ================= --}}
@if (! empty($detail['cocok']) || ! empty($detail['catatan']))
  <section class="bagian">
    <div class="ld-info">
      @if (! empty($detail['cocok']))
        <div class="ld-kotak reveal" style="--i: 0">
          <h3>Cocok untuk</h3>
          <ul>@foreach ($detail['cocok'] as $c)<li>{{ $c }}</li>@endforeach</ul>
        </div>
      @endif
      @if (! empty($detail['catatan']))
        <div class="ld-kotak reveal" style="--i: 1">
          <h3>Catatan</h3>
          <ul>@foreach ($detail['catatan'] as $c)<li>{{ $c }}</li>@endforeach</ul>
        </div>
      @endif
    </div>
  </section>
@endif

{{-- ================= LAYANAN LAINNYA ================= --}}
@if ($lainnya->isNotEmpty())
  <section class="bagian">
    <div class="bagian-kepala reveal">
      <span class="eyebrow">Lainnya</span>
      <h2>Layanan lainnya</h2>
    </div>
    <div class="ld-lain">
      @foreach ($lainnya as $l)
        <a href="{{ route('layanan.detail', $l->slug()) }}" class="reveal" style="--i: {{ $loop->index }}">
          @if ($lf = $l->fotoUrl())
            <img src="{{ $lf }}" alt="" width="64" height="48" loading="lazy" decoding="async">
          @else
            <span class="ikon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3s6 6.2 6 10.5A6 6 0 0 1 6 13.5C6 9.2 12 3 12 3z"/></svg></span>
          @endif
          <span>
            <strong>{{ $l->name }}</strong>
            <small>Rp {{ number_format($l->price, 0, ',', '.') }}</small>
          </span>
        </a>
      @endforeach
    </div>
  </section>
@endif

<section class="cta reveal">
  <h2>Tertarik dengan {{ $layanan->name }}?</h2>
  <p>Pesan sekarang dan biarkan kami yang menjemput, mencuci, dan mengantarnya.</p>
  <a href="{{ route('pesanan.buat', ['layanan' => $layanan->id]) }}" class="btn btn-lg">Pesan layanan ini</a>
</section>

<script src="{{ asset('js/layanan.js') }}" defer></script>
@endsection