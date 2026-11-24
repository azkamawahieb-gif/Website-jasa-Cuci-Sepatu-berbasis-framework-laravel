@extends('layouts.petugas')
@section('title', 'QRIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">QRIS</h1>
  <a href="{{ route('petugas.qris.create') }}" class="btn btn-primary">+ Tambah QRIS</a>
</div>

<p class="text-body-secondary small">Hanya satu QRIS yang aktif. QRIS aktif tampil di halaman pembayaran pengguna (metode QRIS).</p>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th style="width: 110px;">Gambar</th>
          <th>Nama</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($daftar as $q)
          <tr>
            <td>
              @if ($url = $q->gambarUrl())
                <img src="{{ $url }}" alt="{{ $q->name }}" class="rounded border"
                     style="width: 80px; height: 80px; object-fit: contain; background: #fff;">
              @else
                <span class="text-body-secondary small">Gambar hilang</span>
              @endif
            </td>
            <td class="fw-semibold">{{ $q->name }}</td>
            <td>
              <span class="badge text-bg-{{ $q->is_active ? 'success' : 'secondary' }}">
                {{ $q->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="text-end text-nowrap">
              <a href="{{ route('petugas.qris.edit', $q) }}" class="btn btn-sm btn-outline-primary">Ubah</a>

              <form method="POST" action="{{ route('petugas.qris.toggle', $q) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-outline-secondary">
                  {{ $q->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </form>

              <form method="POST" action="{{ route('petugas.qris.destroy', $q) }}" class="d-inline"
                    onsubmit="return confirm('Hapus QRIS {{ $q->name }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-body-secondary py-4">Belum ada QRIS.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection