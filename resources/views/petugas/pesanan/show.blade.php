@extends('layouts.petugas')
@section('title', 'Pesanan ' . $order->code)

@section('content')
<a href="{{ route('petugas.pesanan.index') }}" class="small">&laquo; Kembali ke daftar pesanan</a>

<div class="d-flex flex-wrap justify-content-between align-items-center my-3">
  <h1 class="h4 mb-0">{{ $order->code }}</h1>
  <span class="badge text-bg-{{ $order->status->color() }} fs-6">{{ $order->statusLabel() }}</span>
</div>

<div class="row g-4">
  {{-- KOLOM KIRI --}}
  <div class="col-lg-8">

    <div class="card mb-4">
      <div class="card-header">Sepatu ({{ $order->items->count() }})</div>
      <div class="card-body">
        @foreach ($order->items as $item)
          <div class="d-flex gap-3 pb-3 mb-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
            <div class="d-flex flex-column gap-2">
              @if ($item->before_photo)
                <a href="{{ asset('storage/' . $item->before_photo) }}" target="_blank">
                  <img src="{{ asset('storage/' . $item->before_photo) }}" alt="Sebelum" width="84" height="84"
                       style="object-fit:cover;border-radius:8px;">
                </a>
                <div class="small text-body-secondary text-center">Sebelum</div>
              @endif
            </div>
            <div class="d-flex flex-column gap-2">
              @if ($item->after_photo)
                <a href="{{ asset('storage/' . $item->after_photo) }}" target="_blank">
                  <img src="{{ asset('storage/' . $item->after_photo) }}" alt="Sesudah" width="84" height="84"
                       style="object-fit:cover;border-radius:8px;">
                </a>
                <div class="small text-body-secondary text-center">Sesudah</div>
              @endif
            </div>

            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <div>
                  <strong>{{ $item->shoe_name }}</strong>
                  @if ($item->shoe_color) <span class="text-body-secondary">({{ $item->shoe_color }})</span> @endif
                  <div>{{ $item->service->name }}</div>
                </div>
                <div>Rp {{ number_format($item->price, 0, ',', '.') }}</div>
              </div>
              <div class="small text-body-secondary mt-1">Kondisi: {{ $item->condition_notes ?: '-' }}</div>

              <details class="mt-2">
                <summary class="small text-primary" style="cursor:pointer">Ubah kondisi / unggah foto</summary>
                <form method="POST" action="{{ route('petugas.item.update', $item) }}" enctype="multipart/form-data"
                      class="mt-2 p-3 border rounded bg-body-tertiary">
                  @csrf
                  <div class="mb-2">
                    <label class="form-label small">Catatan kondisi</label>
                    <textarea name="condition_notes" rows="2" class="form-control form-control-sm">{{ $item->condition_notes }}</textarea>
                  </div>
                  <div class="row">
                    <div class="col-sm-6 mb-2">
                      <label class="form-label small">Foto sebelum (kondisi awal)</label>
                      <input type="file" name="before_photo" accept="image/*" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 mb-2">
                      <label class="form-label small">Foto sesudah dicuci</label>
                      <input type="file" name="after_photo" accept="image/*" class="form-control form-control-sm">
                    </div>
                  </div>
                  <button class="btn btn-sm btn-primary">Simpan</button>
                </form>
              </details>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    @if ($order->deliveries->isNotEmpty())
      <div class="card mb-4">
        <div class="card-header">Jemput &amp; Antar</div>
        <div class="card-body">
          @foreach ($order->deliveries as $d)
            <div class="d-flex justify-content-between align-items-start gap-3 {{ ! $loop->last ? 'pb-3 mb-3 border-bottom' : '' }}">
              <div>
                <strong>{{ $d->type === 'jemput' ? 'Penjemputan' : 'Pengantaran' }}</strong>
                <span class="badge text-bg-secondary">{{ ucfirst(str_replace('_', ' ', $d->status)) }}</span>
                <div>{{ $d->address }}</div>
                <div class="small text-body-secondary">
                  {{ $d->zone->name }} &middot; Rp {{ number_format($d->fee, 0, ',', '.') }}
                  @if ($d->scheduled_at) &middot; Jadwal {{ $d->scheduled_at->format('d M Y H:i') }} @endif
                </div>
                @if ($d->note) <div class="small text-body-secondary">Catatan: {{ $d->note }}</div> @endif
              </div>
              <div class="text-end">
                @include('petugas.pengantaran._aksi', ['d' => $d, 'order' => $order])
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <div class="card mb-4">
      <div class="card-header">Riwayat Status</div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          @foreach ($order->logs as $log)
            <li class="mb-3">
              <strong>{{ $order->statusLabel($log->status) }}</strong>
              <div class="small text-body-secondary">
                {{ $log->created_at->format('d M Y H:i') }}
                @if ($log->user) &middot; oleh {{ $log->user->name }} @endif
                @if ($log->note) &middot; {{ $log->note }} @endif
              </div>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>

  {{-- KOLOM KANAN --}}
  <div class="col-lg-4">

    <div class="card mb-4">
      <div class="card-header">Pengguna</div>
      <div class="card-body">
        <div class="fw-semibold">{{ $order->user->name }}</div>
        <div><a href="tel:{{ $order->user->phone }}">{{ $order->user->phone }}</a></div>
        <div class="small text-body-secondary">{{ $order->user->email }}</div>
        @if ($order->user->address) <div class="small mt-1">{{ $order->user->address }}</div> @endif
        @if ($order->notes) <div class="small mt-2"><em>Catatan: {{ $order->notes }}</em></div> @endif
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Tindakan</div>
      <div class="card-body">
        @if ($order->estimated_done_at)
          <div class="small text-body-secondary mb-3">Estimasi selesai {{ $order->estimated_done_at->format('d M Y') }}</div>
        @endif

        @if ($langkah)
          <form method="POST" action="{{ route('petugas.pesanan.lanjut', $order) }}">
            @csrf
            <input type="text" name="note" class="form-control form-control-sm mb-2" placeholder="Catatan (opsional)">
            <button class="btn btn-primary w-100">{{ $langkah }}</button>
          </form>
        @elseif ($order->status->value === 'menunggu_jemput')
          <div class="alert alert-info mb-0 small">Selesaikan penjemputan di bagian Jemput &amp; Antar.</div>
        @elseif ($order->status->value === 'dalam_pengantaran')
          <div class="alert alert-info mb-0 small">Selesaikan pengantaran di bagian Jemput &amp; Antar (pembayaran harus lunas).</div>
        @else
          <div class="text-body-secondary small">Tidak ada tindakan lanjutan.</div>
        @endif

        @if (in_array($order->status->value, ['siap', 'dalam_pengantaran']))
          <a href="{{ $order->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-success w-100 mt-3">
            Kabari via WhatsApp
          </a>
        @endif
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Pembayaran</div>
      <div class="card-body">
        <div class="d-flex justify-content-between"><span>Subtotal layanan</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
        <div class="d-flex justify-content-between"><span>Jemput/antar</span><span>Rp {{ number_format($order->delivery_fee_total, 0, ',', '.') }}</span></div>

        {{-- Diskon voucher --}}
        @if ($order->discount_total > 0)
          <div class="d-flex justify-content-between text-success">
            <span>Diskon voucher @if ($order->voucher_code)({{ $order->voucher_code }})@endif</span>
            <span>- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</span>
          </div>
        @endif

        <div class="d-flex justify-content-between fw-bold border-top mt-2 pt-2">
          <span>Total</span>
          <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>

        {{-- Pilihan metode pembayaran pelanggan --}}
        @if ($order->payment_status !== 'lunas' && $order->payment_method)
          <div class="small text-body-secondary mt-2">
            Pilihan pelanggan: {{ $order->metodeLabel() }}
          </div>
        @endif

        <div class="mt-3">
          @if ($order->payment_status === 'lunas')
            <span class="badge text-bg-success">Lunas</span>
            <span class="small text-body-secondary">via {{ $order->metodeLabel() }}</span>
          @elseif ($order->status->value !== 'dibatalkan')
            <form method="POST" action="{{ route('petugas.pesanan.bayar', $order) }}">
              @csrf
              <select name="payment_method" class="form-select form-select-sm mb-2" required>
                <option value="cod" @selected(in_array($order->payment_method, ['cod', 'tunai'], true))>COD / Tunai</option>
                <option value="transfer" @selected($order->payment_method === 'transfer')>Transfer</option>
                <option value="qris" @selected($order->payment_method === 'qris')>QRIS</option>
              </select>
              <button class="btn btn-outline-success btn-sm w-100">Catat Lunas</button>
            </form>
          @endif
        </div>
      </div>
    </div>

    @if ($bisaBatal)
      <div class="card mb-4 border-danger">
        <div class="card-header text-danger">Batalkan Pesanan</div>
        <div class="card-body">
          <form method="POST" action="{{ route('petugas.pesanan.batal', $order) }}"
                onsubmit="return confirm('Batalkan pesanan {{ $order->code }}?')">
            @csrf
            <input type="text" name="alasan" class="form-control form-control-sm mb-2" placeholder="Alasan pembatalan" required>
            <button class="btn btn-outline-danger btn-sm w-100">Batalkan</button>
          </form>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
