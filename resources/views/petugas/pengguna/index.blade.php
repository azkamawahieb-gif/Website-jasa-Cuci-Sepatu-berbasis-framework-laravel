@extends('layouts.petugas')
@section('title', 'Pengguna')

@section('content')
<h1 class="h4 mb-3">Pengguna</h1>

<form method="GET" action="{{ route('petugas.pengguna.index') }}" class="mb-3" style="max-width: 360px;">
  <input type="text" name="q" value="{{ request('q') }}" class="form-control"
         placeholder="Cari nama, email, atau nomor HP">
</form>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Kontak</th>
          <th class="text-center">Pesanan</th>
          <th class="text-end">Total belanja (lunas)</th>
          <th>Terdaftar</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $u)
          <tr>
            <td class="fw-semibold">{{ $u->name }}</td>
            <td>
              {{ $u->phone ?: '-' }}
              <div class="small text-body-secondary">{{ $u->email }}</div>
            </td>
            <td class="text-center">{{ $u->orders_count }}</td>
            <td class="text-end">Rp {{ number_format($u->total_belanja ?? 0, 0, ',', '.') }}</td>
            <td class="small">{{ $u->created_at->format('d M Y') }}</td>
            <td class="text-end">
              <a href="{{ route('petugas.pengguna.show', $u) }}" class="btn btn-sm btn-outline-primary">Detail</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-body-secondary py-4">Belum ada pengguna.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $users->links() }}</div>
@endsection