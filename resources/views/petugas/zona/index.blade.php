@extends('layouts.petugas')
@section('title', 'Zona Antar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">Zona Antar</h1>
  <a href="{{ route('petugas.zona.create') }}" class="btn btn-primary">+ Tambah Zona</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Area</th>
          <th class="text-end">Biaya</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($zona as $z)
          <tr>
            <td class="fw-semibold">{{ $z->name }}</td>
            <td class="text-body-secondary small">{{ $z->description ?: '-' }}</td>
            <td class="text-end">Rp {{ number_format($z->fee, 0, ',', '.') }}</td>
            <td>
              <span class="badge text-bg-{{ $z->is_active ? 'success' : 'secondary' }}">
                {{ $z->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="text-end text-nowrap">
              <a href="{{ route('petugas.zona.edit', $z) }}" class="btn btn-sm btn-outline-primary">Ubah</a>

              <form method="POST" action="{{ route('petugas.zona.toggle', $z) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-outline-secondary">
                  {{ $z->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </form>

              <form method="POST" action="{{ route('petugas.zona.destroy', $z) }}" class="d-inline"
                    onsubmit="return confirm('Hapus {{ $z->name }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-body-secondary py-4">Belum ada zona.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection