@extends('layouts.petugas')
@section('title', 'Pesanan')

@section('content')
<h1 class="h4 mb-3">Pesanan</h1>

<div class="d-flex flex-wrap gap-2 mb-3">
  <a href="{{ route('petugas.pesanan.index', ['q' => request('q')]) }}"
     class="btn btn-sm {{ $status ? 'btn-outline-secondary' : 'btn-primary' }}">
    Semua <span class="badge text-bg-light">{{ $hitung->sum() }}</span>
  </a>
  @foreach ($semuaStatus as $s)
    <a href="{{ route('petugas.pesanan.index', ['status' => $s->value, 'q' => request('q')]) }}"
       class="btn btn-sm {{ $status === $s->value ? 'btn-primary' : 'btn-outline-secondary' }}">
      {{ $s->label() }} <span class="badge text-bg-light">{{ $hitung[$s->value] ?? 0 }}</span>
    </a>
  @endforeach
</div>

<form method="GET" action="{{ route('petugas.pesanan.index') }}" class="mb-3" style="max-width: 360px;">
  @if ($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
  <input type="text" name="q" value="{{ request('q') }}" class="form-control"
         placeholder="Cari kode, nama, atau nomor HP">
</form>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Pengguna</th>
          <th>Sepatu</th>
          <th>Jemput/Antar</th>
          <th class="text-end">Total</th>
          <th>Status</th>
          <th>Bayar</th>
          <th>Tanggal</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($orders as $o)
          <tr>
            <td class="fw-semibold">{{ $o->code }}</td>
            <td>{{ $o->user->name }}<div class="small text-body-secondary">{{ $o->user->phone }}</div></td>
            <td>{{ $o->items_count }} pasang</td>
            <td>
              @forelse ($o->deliveries as $d)
                <span class="badge text-bg-light border">{{ $d->type === 'jemput' ? 'Jemput' : 'Antar' }}</span>
              @empty
                <span class="text-body-secondary small">-</span>
              @endforelse
            </td>
            <td class="text-end">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
            <td><span class="badge text-bg-{{ $o->status->color() }}">{{ $o->status->label() }}</span></td>
            <td>
              <span class="badge text-bg-{{ $o->payment_status === 'lunas' ? 'success' : 'warning' }}">
                {{ $o->payment_status === 'lunas' ? 'Lunas' : 'Belum' }}
              </span>
            </td>
            <td class="small">{{ $o->created_at->format('d M Y') }}</td>
            <td class="text-end">
              <a href="{{ route('petugas.pesanan.show', $o) }}" class="btn btn-sm btn-outline-primary">Detail</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center text-body-secondary py-4">Tidak ada pesanan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $orders->links() }}</div>
@endsection