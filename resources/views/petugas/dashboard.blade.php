@extends('layouts.petugas')
@section('title', 'Dashboard')

@section('content')
<h1 class="h4 mb-4">Dashboard Petugas</h1>

@if ($passwordBawaan)
  <div class="alert alert-warning">
    Akun Anda masih memakai password bawaan (<code>password</code>), yang sudah diketahui umum.
    <a href="{{ route('petugas.akun') }}" class="alert-link">Ganti sekarang</a>.
  </div>
@endif

<div class="row g-3 mb-4">
  @foreach ([
    ['Perlu Dikonfirmasi', $perluKonfirmasi, 'warning', route('petugas.pesanan.index', ['status' => 'menunggu_konfirmasi'])],
    ['Sedang Dikerjakan', $dikerjakan, 'primary', route('petugas.pesanan.index')],
    ['Siap Diambil/Diantar', $siap, 'success', route('petugas.pesanan.index', ['status' => 'siap'])],
    ['Tugas Jemput/Antar Aktif', $tugasAktif, 'info', route('petugas.pengantaran.index')],
  ] as [$label, $jumlah, $warna, $link])
    <div class="col-6 col-xl-3">
      <a href="{{ $link }}" class="text-decoration-none">
        <div class="card text-white bg-{{ $warna }}">
          <div class="card-body">
            <div class="fs-3 fw-semibold">{{ $jumlah }}</div>
            <div class="small">{{ $label }}</div>
          </div>
        </div>
      </a>
    </div>
  @endforeach
</div>

<div class="card mb-4">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <div class="text-body-secondary small">Pendapatan bulan ini (pesanan lunas)</div>
      <div class="fs-4 fw-semibold">Rp {{ number_format($omzet, 0, ',', '.') }}</div>
    </div>
  </div>
</div>

<div class="card">
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
            <td><span class="badge text-bg-{{ $o->status->color() }}">{{ $o->status->label() }}</span></td>
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