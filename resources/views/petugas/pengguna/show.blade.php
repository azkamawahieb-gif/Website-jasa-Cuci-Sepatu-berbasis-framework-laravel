@extends('layouts.petugas')
@section('title', $user->name)

@section('content')
@php
  $nomor = preg_replace('/\D/', '', $user->phone ?? '');
  if (str_starts_with($nomor, '0')) { $nomor = '62' . substr($nomor, 1); }
@endphp

<a href="{{ route('petugas.pengguna.index') }}" class="small">&laquo; Kembali ke daftar pengguna</a>
<h1 class="h4 my-3">{{ $user->name }}</h1>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">Data Pengguna</div>
      <div class="card-body">
        <div class="mb-2"><span class="text-body-secondary small d-block">Email</span>{{ $user->email }}</div>
        <div class="mb-2"><span class="text-body-secondary small d-block">Nomor HP</span>{{ $user->phone ?: '-' }}</div>
        <div class="mb-2"><span class="text-body-secondary small d-block">Alamat</span>{{ $user->address ?: '-' }}</div>
        <div class="mb-3"><span class="text-body-secondary small d-block">Terdaftar</span>{{ $user->created_at->format('d M Y') }}</div>

        @if ($nomor)
          <a href="https://wa.me/{{ $nomor }}" target="_blank" rel="noopener" class="btn btn-success btn-sm w-100">Hubungi via WhatsApp</a>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">Riwayat Pesanan ({{ $orders->count() }})</div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr><th>Kode</th><th>Tanggal</th><th>Sepatu</th><th class="text-end">Total</th><th>Status</th></tr>
          </thead>
          <tbody>
            @forelse ($orders as $o)
              <tr>
                <td><a href="{{ route('petugas.pesanan.show', $o) }}" class="fw-semibold">{{ $o->code }}</a></td>
                <td class="small">{{ $o->created_at->format('d M Y') }}</td>
                <td>{{ $o->items_count }} pasang</td>
                <td class="text-end">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                <td><span class="badge text-bg-{{ $o->status->color() }}">{{ $o->status->label() }}</span></td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-body-secondary py-4">Belum ada pesanan.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection