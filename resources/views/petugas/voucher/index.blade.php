@extends('layouts.petugas')
@section('title', 'Voucher')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">Voucher</h1>
  <a href="{{ route('petugas.voucher.create') }}" class="btn btn-primary">+ Tambah Voucher</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nama voucher</th>
          <th>Potongan</th>
          <th>Periode</th>
          <th class="text-center">Terpakai</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($voucher as $v)
          @php [$labelStatus, $warna] = $v->status(); @endphp
          <tr>
            <td class="fw-semibold"><code>{{ $v->code }}</code></td>
            <td>{{ $v->description }}</td>
            <td>
              {{ $v->ringkasan() }}
              @if ($v->min_order > 0)
                <div class="text-body-secondary small">Min. Rp {{ number_format($v->min_order, 0, ',', '.') }}</div>
              @endif
            </td>
            <td class="small text-nowrap">
              @if ($v->starts_at || $v->ends_at)
                {{ $v->starts_at ? $v->starts_at->format('d M Y H:i') : 'Sekarang' }}
                <br>s/d {{ $v->ends_at ? $v->ends_at->format('d M Y H:i') : 'Tanpa batas' }}
              @else
                Tanpa batas waktu
              @endif
            </td>
            <td class="text-center">
              {{ $v->used_count }} / {{ $v->quota ?? '∞' }}
            </td>
            <td><span class="badge text-bg-{{ $warna }}">{{ $labelStatus }}</span></td>
            <td class="text-end text-nowrap">
              <a href="{{ route('petugas.voucher.edit', $v) }}" class="btn btn-sm btn-outline-primary">Ubah</a>

              <form method="POST" action="{{ route('petugas.voucher.toggle', $v) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-outline-secondary">
                  {{ $v->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </form>

              <form method="POST" action="{{ route('petugas.voucher.destroy', $v) }}" class="d-inline"
                    onsubmit="return confirm('Hapus voucher {{ $v->code }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-body-secondary py-4">Belum ada voucher.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection