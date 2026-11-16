@php
  $st = $order->status->value;
  $bolehMulai = $d->status === 'dijadwalkan' && (
      ($d->type === 'jemput' && $st === 'menunggu_jemput') ||
      ($d->type === 'antar' && $st === 'siap'));
  $bolehSelesai = ($d->type === 'jemput' && $st === 'menunggu_jemput') ||
                  ($d->type === 'antar' && $st === 'dalam_pengantaran');
@endphp

@if ($d->status === 'selesai')
  <div class="small text-success">Selesai {{ $d->completed_at?->format('d M H:i') }}</div>
  @if ($d->proof_photo)
    <a href="{{ asset('storage/' . $d->proof_photo) }}" target="_blank" class="small">Lihat bukti</a>
  @endif

@elseif ($d->status === 'gagal')
  <span class="badge text-bg-danger">Dibatalkan</span>

@elseif ($bolehMulai || $bolehSelesai)
  @if ($bolehMulai)
    <form method="POST" action="{{ route('petugas.pengantaran.mulai', $d) }}" class="d-inline">
      @csrf
      <button class="btn btn-sm btn-outline-primary">{{ $d->type === 'jemput' ? 'Mulai Jemput' : 'Mulai Antar' }}</button>
    </form>
  @endif

  @if ($bolehSelesai)
    <details class="d-inline-block text-start">
      <summary class="btn btn-sm btn-success" style="list-style:none;cursor:pointer">
        {{ $d->type === 'jemput' ? 'Sepatu Dijemput' : 'Sepatu Diserahkan' }}
      </summary>
      <form method="POST" action="{{ route('petugas.pengantaran.selesai', $d) }}" enctype="multipart/form-data"
            class="mt-2 p-2 border rounded bg-body-tertiary" style="min-width: 240px;">
        @csrf
        <label class="form-label small mb-1">Foto bukti (opsional)</label>
        <input type="file" name="proof_photo" accept="image/*" class="form-control form-control-sm mb-2">
        <input type="text" name="note" class="form-control form-control-sm mb-2" placeholder="Catatan (opsional)">
        <button class="btn btn-sm btn-success">Simpan</button>
      </form>
    </details>
  @endif

@else
  <span class="small text-body-secondary">Menunggu: {{ $order->status->label() }}</span>
@endif