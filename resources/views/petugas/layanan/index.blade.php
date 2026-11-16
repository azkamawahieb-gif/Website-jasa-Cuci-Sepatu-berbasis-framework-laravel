@extends('layouts.petugas')
@section('title', 'Layanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">Layanan</h1>
  <a href="{{ route('petugas.layanan.create') }}" class="btn btn-primary">+ Tambah Layanan</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Deskripsi</th>
          <th class="text-end">Harga</th>
          <th class="text-center">Estimasi</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($layanan as $s)
          <tr>
            <td class="fw-semibold">{{ $s->name }}</td>
            <td class="text-body-secondary small">{{ $s->description ?: '-' }}</td>
            <td class="text-end">Rp {{ number_format($s->price, 0, ',', '.') }}</td>
            <td class="text-center">{{ $s->estimated_days }} hari</td>
            <td>
              <span class="badge text-bg-{{ $s->is_active ? 'success' : 'secondary' }}">
                {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="text-end text-nowrap">
              <a href="{{ route('petugas.layanan.edit', $s) }}" class="btn btn-sm btn-outline-primary">Ubah</a>

              <form method="POST" action="{{ route('petugas.layanan.toggle', $s) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-outline-secondary">
                  {{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </form>

              <form method="POST" action="{{ route('petugas.layanan.destroy', $s) }}" class="d-inline"
                    onsubmit="return confirm('Hapus layanan {{ $s->name }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-body-secondary py-4">Belum ada layanan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
