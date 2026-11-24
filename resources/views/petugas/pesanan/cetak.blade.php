<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $lunas ? 'Bukti Pembayaran' : 'Tagihan' }} {{ $order->code }}</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; background: #eef2f6; color: #1b2a3a; font: 14px/1.5 "Plus Jakarta Sans", Arial, sans-serif; }
    .aksi { max-width: 720px; margin: 16px auto 0; display: flex; gap: 8px; justify-content: flex-end; padding: 0 16px; }
    .aksi button, .aksi a { font: inherit; padding: 8px 16px; border-radius: 8px; border: 1px solid #2f5fb3; cursor: pointer; text-decoration: none; }
    .aksi button { background: #2f5fb3; color: #fff; }
    .aksi a { background: #fff; color: #2f5fb3; }
    .struk { max-width: 720px; margin: 16px auto; background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .kepala { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; border-bottom: 2px solid #1b2a3a; padding-bottom: 14px; margin-bottom: 16px; }
    .merek { font-size: 22px; font-weight: 700; }
    .judul { text-align: right; }
    .judul strong { display: block; font-size: 16px; letter-spacing: .04em; text-transform: uppercase; }
    .stempel { display: inline-block; margin-top: 4px; padding: 2px 10px; border: 2px solid; border-radius: 6px; font-weight: 700; font-size: 12px; letter-spacing: .08em; }
    .lunas { color: #15803d; } .belum { color: #b45309; }
    .info { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 24px; margin-bottom: 18px; }
    .info dt { color: #64748b; font-size: 12px; } .info dd { margin: 0 0 6px; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; font-size: 12px; color: #64748b; border-bottom: 1px solid #cbd5e1; padding: 6px 4px; }
    td { padding: 8px 4px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
    .kanan { text-align: right; white-space: nowrap; }
    .kecil { color: #64748b; font-size: 12px; }
    .total { width: 100%; max-width: 300px; margin: 14px 0 0 auto; }
    .total div { display: flex; justify-content: space-between; padding: 3px 0; }
    .total .akhir { border-top: 2px solid #1b2a3a; margin-top: 6px; padding-top: 8px; font-size: 16px; font-weight: 700; }
    .diskon { color: #15803d; }
    .pembayaran { margin-top: 18px; padding: 12px 14px; background: #f1f5f9; border-radius: 8px; }
    .tutup { margin-top: 24px; text-align: center; color: #64748b; font-size: 12px; }
    @page { size: A4; margin: 12mm; }
    @media print {
      body { background: #fff; }
      .aksi { display: none; }
      .struk { margin: 0; max-width: none; box-shadow: none; border-radius: 0; padding: 0; }
      .pembayaran { background: none; border: 1px solid #cbd5e1; }
      tr { break-inside: avoid; }
    }
  </style>
</head>
<body>
  <div class="aksi">
    <a href="{{ route('petugas.pesanan.show', $order) }}">Kembali</a>
    <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
  </div>

  <main class="struk">
    <header class="kepala">
      <div>
        <div class="merek">{{ config('app.name') }}</div>
        <div class="kecil">Layanan cuci sepatu</div>
      </div>
      <div class="judul">
        <strong>{{ $lunas ? 'Bukti Pembayaran' : 'Tagihan' }}</strong>
        <span class="stempel {{ $lunas ? 'lunas' : 'belum' }}">{{ $lunas ? 'LUNAS' : 'BELUM LUNAS' }}</span>
      </div>
    </header>

    <dl class="info">
      <div><dt>No. pesanan</dt><dd>{{ $order->code }}</dd></div>
      <div><dt>Tanggal pesanan</dt><dd>{{ $order->created_at->translatedFormat('d F Y, H:i') }}</dd></div>
      <div><dt>Pelanggan</dt><dd>{{ $order->user->name }}</dd></div>
      <div><dt>{{ $lunas ? 'Tanggal pembayaran' : 'Status pesanan' }}</dt>
        <dd>{{ $lunas ? ($tanggalBayar ?? $order->updated_at)->translatedFormat('d F Y, H:i') : $order->statusLabel() }}</dd></div>
      <div><dt>Telepon</dt><dd>{{ $order->user->phone ?: '-' }}</dd></div>
      <div><dt>Estimasi selesai</dt><dd>{{ $order->estimated_done_at?->translatedFormat('d F Y') ?? '-' }}</dd></div>
    </dl>

    <table>
      <thead>
        <tr><th style="width:32px">#</th><th>Sepatu</th><th>Layanan</th><th class="kanan">Harga</th></tr>
      </thead>
      <tbody>
        @foreach ($order->items as $i => $item)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>
              <strong>{{ $item->shoe_name }}</strong>
              <div class="kecil">
                {{ collect([$item->shoe_color, $item->shoe_size ? 'Ukuran '.$item->shoe_size : null])->filter()->implode(' · ') }}
              </div>
            </td>
            <td>{{ $item->service->name ?? '-' }}</td>
            <td class="kanan">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="total">
      <div><span>Subtotal layanan</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
      <div><span>Jemput/antar</span><span>Rp {{ number_format($order->delivery_fee_total, 0, ',', '.') }}</span></div>
      @if ($order->discount_total > 0)
        <div class="diskon">
          <span>Diskon @if ($order->voucher_code)({{ $order->voucher_code }})@endif</span>
          <span>- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</span>
        </div>
      @endif
      <div class="akhir"><span>Total</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
    </div>

    <div class="pembayaran">
      <strong>Pembayaran:</strong> {{ $order->metodeLabel() }}
      @if ($lunas) — <span class="lunas"><strong>Lunas</strong></span>@endif
    </div>

    <p class="tutup">
      Terima kasih telah memakai {{ config('app.name') }}.<br>
      Dicetak {{ now()->translatedFormat('d F Y, H:i') }} oleh {{ auth()->user()->name }}.
    </p>
  </main>
</body>
</html>