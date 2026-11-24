@extends('layouts.petugas')
@section('title', 'Rekening Bank')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">Rekening Bank</h1>
  <a href="{{ route('petugas.rekening.create') }}" class="btn btn-primary">+ Tambah Rekening</a>
</div>

<p class="text-body-secondary small">Rekening yang aktif tampil di halaman pembayaran pengguna (metode Transfer Bank).</p>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Bank</th>
          <th>Nomor Rekening</th>
          <th>Atas Nama</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($rekening as $r)
          <tr>
            <td class="fw-semibold">{{ $r->bank_name }}</td>
            <td>{{ $r->account_number }}</td>
            <td>{{ $r->account_name }}</td>
            <td>
              <span class="badge text-bg-{{ $r->is_active ? 'success' : 'secondary' }}">
                {{ $r->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="text-end text-nowrap">
              <a href="{{ route('petugas.rekening.edit', $r) }}" class="btn btn-sm btn-outline-primary">Ubah</a>

              <form method="POST" action="{{ route('petugas.rekening.toggle', $r) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-outline-secondary">
                  {{ $r->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </form>

              <form method="POST" action="{{ route('petugas.rekening.destroy', $r) }}" class="d-inline"
                    onsubmit="return confirm('Hapus rekening {{ $r->bank_name }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-body-secondary py-4">Belum ada rekening.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection