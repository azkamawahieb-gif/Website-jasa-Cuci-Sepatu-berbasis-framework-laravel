
@extends('layouts.pengguna')
@section('title', 'Pesanan ' . $order->code)
@push('css')
<link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
@endpush

@section('content')
@php
  // Langkah bilah kemajuan. Menunggu Jemput dan Dalam Pengantaran hanya muncul bila pesanan memakainya.
  $urutan = [
    'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
    'menunggu_jemput'     => 'Menunggu Jemput',
    'diterima'            => 'Diterima',
    'dicuci'              => 'Sedang Dicuci',
    'siap'                => 'Siap',
    'dalam_pengantaran'   => 'Dalam Pengantaran',
    'selesai'             => 'Selesai',
  ];

  $sekarang = $order->status->value;

  $langkah = collect($urutan)->filter(fn ($label, $kunci) => match ($kunci) {
    'menunggu_jemput'   => $order->jemput() || $sekarang === $kunci,
    'dalam_pengantaran' => $order->antar() || $sekarang === $kunci,
    default             => true,
  });

  $kunciLangkah = $langkah->keys()->values()->all();
  $posisi = array_search($sekarang, $kunciLangkah, true);
  $jumlah = count($kunciLangkah);
  $persen = $posisi === false ? 0 : ($jumlah > 1 ? round($posisi / ($jumlah - 1) * 100) : 100);
  $dibatalkan = $sekarang === 'dibatalkan';
@endphp

<div class="area">
  <a href="{{ route('pesanan.index') }}" class="kembali">&laquo; Kembali ke Pesanan Saya</a>

  <div class="kepala-halaman anim-naik" style="margin-top:.6rem">
    <div class="kepala-baris">
      <div>
        <span class="label">Detail pesanan</span>
        <h1>{{ $order->code }}</h1>
      </div>
      <span class="badge badge-{{ $order->status->color() }}">{{ $order->status->label() }}</span>
    </div>
    <p>
      Dibuat {{ $order->created_at->format('d M Y H:i') }}
      @if ($order->estimated_done_at)
        &middot; Estimasi selesai {{ $order->estimated_done_at->format('d M Y') }}
      @endif
    </p>
  </div>

  {{-- Bilah kemajuan status --}}
  @if ($dibatalkan)
    <div class="dibatalkan-info anim-naik" style="--i:1" role="status">
      Pesanan ini dibatalkan.
    </div>
  @else
    <div class="stepper anim-naik" style="--i:1; --n: {{ $jumlah }}" role="group"
         aria-label="Kemajuan pesanan: langkah {{ ($posisi === false ? 0 : $posisi) + 1 }} dari {{ $jumlah }}">
      <div class="stepper-jalur" aria-hidden="true">
        <span class="progres-isi" style="--pct: {{ $persen }}%"></span>
      </div>

      <ol class="stepper-titik">
        @foreach ($langkah as $kunci => $label)
          @php $i = array_search($kunci, $kunciLangkah, true); @endphp

          <li class="{{ $i < $posisi || $sekarang === 'selesai' ? 'selesai' : ($i === $posisi ? 'sekarang' : '') }}"
              @if ($i === $posisi) aria-current="step" @endif>
            <span class="titik" aria-hidden="true">
              @if ($i < $posisi || $sekarang === 'selesai')
                <svg viewBox="0 0 24 24">
                  <path d="M5 12.500l4.500 4.500L19 7.500"/>
                </svg>
              @endif
            </span>
            <span class="label-langkah">{{ $label }}</span>
          </li>
        @endforeach
      </ol>
    </div>
  @endif

  <div class="detail-grid">
    <div>
      {{-- Sepatu --}}
      <section class="kartu-area anim-naik" style="--i:2">
        <h2>Sepatu</h2>

        @foreach ($order->items as $item)
          <div class="sepatu-baris">
            <div class="sepatu-kepala">
              <div>
                <strong>{{ $item->shoe_name }}</strong>

                @if ($item->shoe_color)
                  <span class="muted">({{ $item->shoe_color }})</span>
                @endif

                {{-- Ukuran sepatu --}}
                @if ($item->shoe_size)
                  <span class="muted">&middot; Ukuran {{ $item->shoe_size }}</span>
                @endif

                <div class="layanan-nama">{{ $item->service->name }}</div>
              </div>

              <span class="sepatu-harga">
                Rp {{ number_format($item->price, 0, ',', '.') }}
              </span>
            </div>

            @if ($item->condition_notes)
              <div class="bantu">Kondisi: {{ $item->condition_notes }}</div>
            @endif

            <div class="foto-dua">
              <figure class="foto-kotak">
                <figcaption>Sebelum</figcaption>
                @if ($item->before_photo)
                  <img src="{{ asset('storage/' . $item->before_photo) }}"
                       alt="Foto kondisi awal {{ $item->shoe_name }}" loading="lazy">
                @else
                  <div class="foto-kosong">Tidak ada foto kondisi awal</div>
                @endif
              </figure>

              <figure class="foto-kotak">
                <figcaption>Sesudah</figcaption>
                @if ($item->after_photo)
                  <img src="{{ asset('storage/' . $item->after_photo) }}"
                       alt="Foto {{ $item->shoe_name }} setelah dicuci" loading="lazy">
                @else
                  <div class="foto-kosong">
                    {{ $dibatalkan ? 'Tidak ada foto' : 'Foto hasil muncul setelah sepatu dicuci' }}
                  </div>
                @endif
              </figure>
            </div>
          </div>
        @endforeach
      </section>

      {{-- Jemput & antar --}}
      @if ($order->deliveries->isNotEmpty())
        <section class="kartu-area anim-naik" style="--i:3">
          <h2>Jemput &amp; Antar</h2>

          @foreach ($order->deliveries as $d)
            <div class="antar-baris">
              <div>
                <strong>
                  {{ $d->type === 'jemput' ? 'Penjemputan' : 'Pengantaran' }}
                </strong>

                <span class="badge badge-secondary">
                  {{ ucfirst(str_replace('_', ' ', $d->status)) }}
                </span>

                <div>{{ $d->address }}</div>

                {{-- Tautan Google Maps --}}
                @if ($d->maps_url)
                  <div>
                    <a href="{{ $d->maps_url }}" target="_blank" rel="noopener noreferrer">
                      Lihat di Google Maps
                    </a>
                  </div>
                @endif

                <div class="bantu">
                  {{ $d->zone->name }}
                  @if ($d->scheduled_at)
                    &middot; Jadwal {{ $d->scheduled_at->format('d M Y H:i') }}
                  @endif
                </div>
              </div>

              <span class="sepatu-harga">
                Rp {{ number_format($d->fee, 0, ',', '.') }}
              </span>
            </div>
          @endforeach
        </section>
      @endif

      {{-- Riwayat status --}}
      <section class="kartu-area anim-naik" style="--i:4">
        <h2>Riwayat Status</h2>

        <ul class="timeline">
          @foreach ($order->logs as $log)
            <li class="{{ $loop->first ? 'terkini' : '' }}" style="--i:{{ $loop->index }}">
              <strong>{{ $log->status->label() }}</strong>

              <div class="bantu">
                {{ $log->created_at->format('d M Y H:i') }}
                @if ($log->note)
                  &middot; {{ $log->note }}
                @endif
              </div>
            </li>
          @endforeach
        </ul>
      </section>
    </div>

    {{-- Samping: rincian biaya, pembayaran, batalkan --}}
    <aside class="detail-samping">
      <section class="kartu-area anim-naik" style="--i:3">
        <h2>Rincian Biaya</h2>

        <div class="ringkasan">
          <div class="baris">
            <span>Subtotal layanan</span>
            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
          </div>

          <div class="baris">
            <span>Biaya jemput/antar</span>
            <span>Rp {{ number_format($order->delivery_fee_total, 0, ',', '.') }}</span>
          </div>

          <div class="baris total">
            <span>Total</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
          </div>
        </div>

        <div class="bayar-baris">
          <span>Pembayaran:</span>

          @if ($order->payment_status === 'lunas')
            <span class="badge badge-success">Lunas</span>
            <span>({{ ucfirst($order->payment_method) }})</span>
          @else
            <span class="badge badge-warning">Belum dibayar</span>
          @endif
        </div>

        @if ($order->payment_status !== 'lunas')
          <p class="bantu" style="margin-top:.5rem">
            Dibayar saat sepatu diterima kembali (tunai, transfer, atau QRIS).
          </p>
        @endif

        @if ($order->status->bisaDibatalkanPengguna())
          <form method="POST" action="{{ route('pesanan.batal', $order) }}"
                onsubmit="return confirm('Batalkan pesanan {{ $order->code }}?')">
            @csrf
            @method('PATCH')

            <button type="submit" class="btn btn-bahaya btn-batal">
              Batalkan Pesanan
            </button>
          </form>
        @endif
      </section>
    </aside>
  </div>
</div>
@endsection
