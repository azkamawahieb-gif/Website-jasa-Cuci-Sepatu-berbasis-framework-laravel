@extends('layouts.petugas')
@section('title', 'Dashboard')

@push('css')
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('js/dashboard.js') }}" defer></script>
@endpush

@section('content')
@php
  $chip = array_values(array_filter([
    [$perluKonfirmasi, 'pesanan perlu dikonfirmasi', route('petugas.pesanan.index', ['status' => 'menunggu_konfirmasi']), '#F2B84B'],
    [$siap, 'pesanan siap diambil/diantar', route('petugas.pesanan.index', ['status' => 'siap']), '#2FA98C'],
    [$tugasAktif, 'tugas jemput/antar aktif', route('petugas.pengantaran.index'), '#2D5BB0'],
  ], fn ($c) => $c[0] > 0));
@endphp

{{-- Banner sapaan --}}
<section class="dh-hero dash-masuk" style="--i:0">
  <div class="dh-teks">
    <p class="dh-tanggal">{{ $tanggalHariIni }}</p>
    <h1 class="dh-judul">Halo, {{ $namaDepan }}</h1>
    <p class="dh-sub">
      @if (count($chip))
        Yang perlu diurus sekarang:
      @else
        Semua beres. Belum ada yang perlu ditindaklanjuti.
      @endif
    </p>

    @if (count($chip))
      <div class="dh-chip-baris">
        @foreach ($chip as [$n, $teks, $link, $warna])
          <a href="{{ $link }}" class="dh-chip">
            <span class="dh-titik" style="background:{{ $warna }}"></span>
            <span><b>{{ $n }}</b> {{ $teks }}</span>
          </a>
        @endforeach
      </div>
    @endif

    <div class="dh-aksi">
      <a href="{{ route('petugas.pesanan.index') }}" class="dh-btn dh-btn-utama">Lihat pesanan</a>
      <a href="{{ route('petugas.pengantaran.index') }}" class="dh-btn dh-btn-putih">Jemput &amp; Antar</a>
    </div>
  </div>
</section>

{{-- Kartu ringkasan --}}
<div class="row g-3 mb-4">
  @foreach ([
    ['Perlu Dikonfirmasi', $perluKonfirmasi, '#FFF3D6', '#9A6700', 'Pesanan baru yang menunggu Anda',
      route('petugas.pesanan.index', ['status' => 'menunggu_konfirmasi']),
      '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
    ['Sedang Dikerjakan', $dikerjakan, '#E3ECFA', '#2D5BB0', 'Sudah diterima atau sedang dicuci',
      route('petugas.pesanan.index'),
      '<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>'],
    ['Siap Diambil/Diantar', $siap, '#DDF5EE', '#1B7F66', 'Sepatu bersih, tinggal diserahkan',
      route('petugas.pesanan.index', ['status' => 'siap']),
      '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
    ['Tugas Jemput/Antar', $tugasAktif, '#DDF1F6', '#2F7F95', 'Jemput dan antar yang masih berjalan',
      route('petugas.pengantaran.index'),
      '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>'],
  ] as $n => [$label, $jumlah, $tint, $ikonWarna, $hint, $link, $ikon])
    <div class="col-6 col-xl-3">
      <a href="{{ $link }}" class="dash-kartu dash-masuk" style="--i:{{ $n + 1 }}; --tint:{{ $tint }}; --ikon:{{ $ikonWarna }}">
        <span class="dash-ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ikon !!}</svg>
        </span>
        <span class="dash-kartu-isi">
          <span class="dash-label">{{ $label }}</span>
          <span class="dash-angka" data-count="{{ $jumlah }}">{{ $jumlah }}</span>
          <span class="dash-hint">{{ $hint }}</span>
        </span>
      </a>
    </div>
  @endforeach
</div>

<div class="row g-3 mb-4">
  {{-- Grafik 7 hari --}}
  <div class="col-lg-7">
    <div class="card h-100 dash-masuk dash-hover" style="--i:5">
      <div class="card-header">Pesanan 7 Hari Terakhir</div>
      <div class="card-body">
        <p class="dash-ket">
          Jumlah pesanan masuk per hari
          @if ($totalHarian > 0)
            (total <strong>{{ $totalHarian }}</strong>, tidak termasuk yang dibatalkan).
          @else
            (belum ada pesanan dalam 7 hari terakhir).
          @endif
        </p>

        <div class="dg-chart" role="group" aria-label="Grafik pesanan per hari selama 7 hari terakhir">
          <div class="dg-garis" aria-hidden="true"></div>
          @foreach ($harian as $i => $h)
            @php $t = round($h['jumlah'] / $maksHarian * 85, 1); @endphp
            <div class="dg-kol {{ $h['hariIni'] ? 'dg-ini' : '' }} {{ $h['jumlah'] === 0 ? 'dg-kosong' : '' }}"
                 style="--i:{{ $i }}; --t:{{ $t }}%" tabindex="0"
                 aria-label="{{ $h['panjang'] }}: {{ $h['jumlah'] }} pesanan">
              <div class="dg-area">
                <span class="dg-nilai">{{ $h['jumlah'] }}</span>
                <span class="dg-batang"></span>
              </div>
              <span class="dg-hari">{{ $h['label'] }}</span>
              <span class="dg-tip">{{ $h['panjang'] }}<br>{{ $h['jumlah'] }} pesanan &middot; Rp {{ number_format($h['omzet'], 0, ',', '.') }} lunas</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- Tugas hari ini --}}
  <div class="col-lg-5">
    <div class="card h-100 dash-masuk dash-hover" style="--i:6">
      <div class="card-header d-flex justify-content-between">
        <span>Tugas Hari Ini</span>
        <a href="{{ route('petugas.pengantaran.index') }}" class="small">Lihat semua</a>
      </div>
      <div class="card-body">
        @forelse ($tugasHariIni as $d)
          <div class="dash-tugas">
            <span class="dash-jam">{{ $d->scheduled_at->format('H:i') }}</span>
            <div class="dash-tugas-isi">
              <div>
                <a href="{{ route('petugas.pesanan.show', $d->order) }}" class="fw-semibold">{{ $d->order->code }}</a>
                <span class="badge text-bg-{{ $d->type === 'jemput' ? 'info' : 'primary' }} ms-1">{{ $d->type === 'jemput' ? 'Jemput' : 'Antar' }}</span>
              </div>
              <div class="small text-body-secondary">
                {{ $d->order->user->name }} &middot; {{ \Illuminate\Support\Str::limit($d->address, 48) }}
              </div>
            </div>
          </div>
        @empty
          <div class="dash-kosong">
            <span class="dash-kosong-ikon" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <strong>Tidak ada jadwal hari ini</strong>
            <span class="small text-body-secondary">Jemput dan antar yang dijadwalkan hari ini akan muncul di sini.</span>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  {{-- Omzet --}}
  <div class="col-lg-4">
    <div class="card h-100 dash-masuk dash-hover dash-omzet" style="--i:7">
      <div class="card-body d-flex flex-column justify-content-center">
        <div class="dash-ket mb-1">Pendapatan bulan ini</div>
        <div class="dash-omzet-nilai"><span data-count="{{ (int) $omzet }}" data-rupiah="1">Rp {{ number_format($omzet, 0, ',', '.') }}</span></div>
        <div class="small text-body-secondary mt-1">Dari pesanan yang sudah lunas.</div>
      </div>
    </div>
  </div>

  {{-- Alur status --}}
  <div class="col-lg-8">
    <div class="card h-100 dash-masuk dash-hover" style="--i:8">
      <div class="card-header">Alur Status Pesanan</div>
      <div class="card-body">
        <p class="dash-ket">Posisi semua pesanan saat ini. Makin lebar warnanya, makin banyak pesanan di status itu.</p>
        @if ($totalStatus > 0)
          <div class="dash-alur" role="img" aria-label="Bilah jumlah pesanan per status, total {{ $totalStatus }} pesanan">
            @foreach ($alurStatus as $i => $r)
              <span class="dash-seg" style="--i:{{ $i }}; width:{{ round($r['jumlah'] / $totalStatus * 100, 2) }}%; background:{{ $r['warna'] }}"
                    title="{{ $r['label'] }}: {{ $r['jumlah'] }}"></span>
            @endforeach
          </div>
          <ul class="dash-legenda">
            @foreach ($alurStatus as $r)
              <li><span class="dash-titik" style="background:{{ $r['warna'] }}"></span>{{ $r['label'] }} <strong>{{ $r['jumlah'] }}</strong></li>
            @endforeach
          </ul>
        @else
          <div class="dash-alur" aria-hidden="true"></div>
          <p class="text-center text-body-secondary small mt-3 mb-0">Belum ada pesanan.</p>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Pesanan terbaru --}}
<div class="card dash-masuk dash-hover" style="--i:9">
  <div class="card-header d-flex justify-content-between">
    <span>Pesanan Terbaru</span>
    <a href="{{ route('petugas.pesanan.index') }}" class="small">Lihat semua</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr><th>Kode</th><th>Pengguna</th><th>Status</th><th class="text-end">Total</th></tr>
      </thead>
      <tbody>
        @forelse ($terbaru as $o)
          <tr>
            <td><a href="{{ route('petugas.pesanan.show', $o) }}" class="fw-semibold">{{ $o->code }}</a></td>
            <td>{{ $o->user->name }}</td>
            <td><span class="badge text-bg-{{ $o->status->color() }}">{{ $o->statusLabel() }}</span></td>
            <td class="text-end">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-body-secondary py-4">Belum ada pesanan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection