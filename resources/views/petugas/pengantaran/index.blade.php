
@extends('layouts.petugas')
@section('title', 'Jemput & Antar')

@section('content')
<h1 class="h4 mb-3">Jemput &amp; Antar</h1>

<div class="d-flex flex-wrap gap-2 mb-3">
  @foreach (['aktif' => 'Aktif', 'selesai' => 'Selesai', 'semua' => 'Semua'] as $k => $label)
    <a href="{{ route('petugas.pengantaran.index', ['tampil' => $k, 'tipe' => $tipe]) }}"
       class="btn btn-sm {{ $tampil === $k ? 'btn-primary' : 'btn-outline-secondary' }}">
       {{ $label }}
    </a>
  @endforeach

  <span class="mx-2 border-start"></span>

  @foreach ([null => 'Semua tipe', 'jemput' => 'Jemput', 'antar' => 'Antar'] as $k => $label)
    <a href="{{ route('petugas.pengantaran.index', ['tampil' => $tampil, 'tipe' => $k]) }}"
       class="btn btn-sm {{ $tipe === $k ? 'btn-dark' : 'btn-outline-secondary' }}">
       {{ $label }}
    </a>
  @endforeach
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Jadwal</th>
          <th>Tipe</th>
          <th>Pesanan</th>
          <th>Alamat</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @forelse ($tugas as $d)
          <tr>
            <td class="text-nowrap">
              {{ $d->scheduled_at ? $d->scheduled_at->format('d M Y H:i') : 'Belum dijadwalkan' }}

              <details class="mt-1">
                <summary class="small text-primary" style="cursor:pointer">
                  Atur jadwal
                </summary>

                <form method="POST"
                      action="{{ route('petugas.pengantaran.update', $d) }}"
                      class="mt-1"
                      style="min-width:220px">
                  @csrf
                  @method('PATCH')

                  <input type="datetime-local"
                         name="scheduled_at"
                         class="form-control form-control-sm mb-1"
                         value="{{ $d->scheduled_at?->format('Y-m-d\TH:i') }}">

                  <input type="text"
                         name="note"
                         class="form-control form-control-sm mb-1"
                         value="{{ $d->note }}"
                         placeholder="Catatan">

                  <button class="btn btn-sm btn-outline-primary">
                    Simpan
                  </button>
                </form>
              </details>
            </td>

            <td>
              <span class="badge text-bg-{{ $d->type === 'jemput' ? 'info' : 'primary' }}">
                {{ $d->type === 'jemput' ? 'Jemput' : 'Antar' }}
              </span>
            </td>

            <td>
              <a href="{{ route('petugas.pesanan.show', $d->order) }}"
                 class="fw-semibold">
                {{ $d->order->code }}
              </a>

              <div class="small">{{ $d->order->user->name }}</div>
              <div class="small text-body-secondary">{{ $d->order->user->phone }}</div>
            </td>

            {{-- Alamat dan Google Maps --}}
            <td style="max-width: 260px;">
              {{ $d->address }}

              @if ($d->maps_url)
                <div class="small">
                  <a href="{{ $d->maps_url }}"
                     target="_blank"
                     rel="noopener noreferrer">
                    Buka di Google Maps
                  </a>
                </div>
              @endif

              <div class="small text-body-secondary">
                {{ $d->zone->name }} &middot; Rp {{ number_format($d->fee, 0, ',', '.') }}
              </div>
            </td>

            <td>
              <span class="badge text-bg-secondary">
                {{ ucfirst(str_replace('_', ' ', $d->status)) }}
              </span>
            </td>

            <td class="text-end">
              @include('petugas.pengantaran._aksi', ['d' => $d, 'order' => $d->order])
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-body-secondary py-4">
              Tidak ada tugas.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $tugas->links() }}</div>
@endsection
