@extends('layouts.pengguna')

@section('title', 'Pesanan ' . $order->code)

@push('css')
    <link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
@endpush

@section('content')
    @php
        // Langkah bilah kemajuan pesanan.
        // Menunggu Jemput dan Dalam Pengantaran hanya muncul jika pesanan memakainya.

        $urutan = [
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'menunggu_jemput' => 'Menunggu Jemput',
            'diterima' => 'Diterima',
            'dicuci' => 'Sedang Dicuci',
            'siap' => $order->antar() ? 'Siap Diantar' : 'Siap Diambil',
            'dalam_pengantaran' => 'Dalam Pengantaran',
            'selesai' => 'Selesai',
        ];

        $sekarang = $order->status->value;

        $langkah = collect($urutan)->filter(fn ($label, $kunci) => match ($kunci) {
            'menunggu_jemput' => $order->jemput() || $sekarang === $kunci,
            'dalam_pengantaran' => $order->antar() || $sekarang === $kunci,
            default => true,
        });

        $kunciLangkah = $langkah->keys()->values()->all();
        $posisi = array_search($sekarang, $kunciLangkah, true);
        $jumlah = count($kunciLangkah);

        $persen = $posisi === false
            ? 0
            : ($jumlah > 1 ? round($posisi / ($jumlah - 1) * 100) : 100);

        $dibatalkan = $sekarang === 'dibatalkan';
    @endphp

    <div class="area">

        <a href="{{ route('pesanan.index') }}" class="kembali">
            &laquo; Kembali ke Pesanan Saya
        </a>

        {{-- Kepala halaman --}}
        <div class="kepala-halaman anim-naik" style="margin-top:.6rem">
            <div class="kepala-baris">
                <div>
                    <span class="label">Detail pesanan</span>
                    <h1>{{ $order->code }}</h1>
                </div>

                <span class="badge badge-{{ $order->status->color() }}">
                    {{ $order->statusLabel() }}
                </span>

                <a href="{{ route('chat.order', $order) }}" class="btn btn-outline btn-kecil">Chat petugas</a>
            </div>

            <p>
                Dibuat {{ $order->created_at->format('d M Y H:i') }}

                @if ($order->estimated_done_at)
                    &middot; Estimasi selesai
                    {{ $order->estimated_done_at->format('d M Y') }}
                @endif
            </p>
        </div>

        {{-- Bilah kemajuan status --}}
        @if ($dibatalkan)

            <div class="dibatalkan-info anim-naik" style="--i:1" role="status">
                Pesanan ini dibatalkan.
            </div>

        @else

            <div
                class="stepper anim-naik"
                style="--i:1; --n: {{ $jumlah }}"
                role="group"
                aria-label="Kemajuan pesanan: langkah {{ ($posisi === false ? 0 : $posisi) + 1 }} dari {{ $jumlah }}"
            >
                <div class="stepper-jalur" aria-hidden="true">
                    <span class="progres-isi" style="--pct: {{ $persen }}%"></span>
                </div>

                <ol class="stepper-titik">
                    @foreach ($langkah as $kunci => $label)

                        @php
                            $i = array_search($kunci, $kunciLangkah, true);
                        @endphp

                        <li
                            class="{{ $i < $posisi || $sekarang === 'selesai' ? 'selesai' : ($i === $posisi ? 'sekarang' : '') }}"
                            @if ($i === $posisi) aria-current="step" @endif
                        >
                            <span class="titik" aria-hidden="true">

                                @if ($i < $posisi || $sekarang === 'selesai')
                                    <svg viewBox="0 0 24 24">
                                        <path d="M5 12.500l4.500 4.500L19 7.500"/>
                                    </svg>
                                @endif

                            </span>

                            <span class="label-langkah">
                                {{ $label }}
                            </span>
                        </li>

                    @endforeach
                </ol>
            </div>

        @endif

        {{-- Informasi ketika sepatu sudah siap --}}
        @if ($order->status === \App\Enums\OrderStatus::Siap)

            <div class="info-langkah anim-naik" style="--i:1" role="status">

                @if ($order->antar())

                    <strong>
                        Sepatu sudah bersih dan siap diantar.
                    </strong>

                    <span>
                        Petugas akan mengantar ke alamat pengantaran sesuai jadwal.
                        Siapkan pembayaran, karena dibayar saat sepatu diterima.
                    </span>

                @else

                    <strong>
                        Sepatu sudah bersih dan siap diambil.
                    </strong>

                    <span>
                        Datang ke toko dan tunjukkan kode
                        <b>{{ $order->code }}</b>
                        sebagai bukti ambil.
                        Pembayaran dilakukan saat sepatu diterima.
                    </span>

                @endif

            </div>

        @endif

        {{-- Detail utama --}}
        <div class="detail-grid">

            <div>

                {{-- Sepatu --}}
                <section class="kartu-area anim-naik" style="--i:2">

                    <h2>Sepatu</h2>

                    @foreach ($order->items as $item)

                        <div class="sepatu-baris">

                            <div class="sepatu-kepala">

                                <div>

                                    <strong>
                                        {{ $item->shoe_name }}
                                    </strong>

                                    @if ($item->shoe_color)
                                        <span class="muted">
                                            ({{ $item->shoe_color }})
                                        </span>
                                    @endif

                                    {{-- Ukuran sepatu --}}
                                    @if ($item->shoe_size)
                                        <span class="muted">
                                            &middot; Ukuran {{ $item->shoe_size }}
                                        </span>
                                    @endif

                                    <div class="layanan-nama">
                                        {{ $item->service->name }}
                                    </div>

                                </div>

                                <span class="sepatu-harga">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </span>

                            </div>

                            @if ($item->condition_notes)
                                <div class="bantu">
                                    Kondisi: {{ $item->condition_notes }}
                                </div>
                            @endif

                            {{-- Foto sebelum dan sesudah --}}
                            <div class="foto-dua">

                                <figure class="foto-kotak">

                                    <figcaption>Sebelum</figcaption>

                                    @if ($item->before_photo)

                                        <img
                                            src="{{ asset('storage/' . $item->before_photo) }}"
                                            alt="Foto kondisi awal {{ $item->shoe_name }}"
                                            loading="lazy"
                                        >

                                    @else

                                        <div class="foto-kosong">
                                            Tidak ada foto kondisi awal
                                        </div>

                                    @endif

                                </figure>

                                <figure class="foto-kotak">

                                    <figcaption>Sesudah</figcaption>

                                    @if ($item->after_photo)

                                        <img
                                            src="{{ asset('storage/' . $item->after_photo) }}"
                                            alt="Foto {{ $item->shoe_name }} setelah dicuci"
                                            loading="lazy"
                                        >

                                    @else

                                        <div class="foto-kosong">
                                            {{ $dibatalkan
                                                ? 'Tidak ada foto'
                                                : 'Foto hasil muncul setelah sepatu dicuci'
                                            }}
                                        </div>

                                    @endif

                                </figure>

                            </div>

                        </div>

                    @endforeach

                </section>

                {{-- Jemput dan antar --}}
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

                                    <div>
                                        {{ $d->address }}
                                    </div>

                                    {{-- Tautan Google Maps --}}
                                    @if ($d->maps_url)

                                        <div>
                                            <a
                                                href="{{ $d->maps_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                Lihat di Google Maps
                                            </a>
                                        </div>

                                    @endif

                                    <div class="bantu">

                                        {{ $d->zone->name }}

                                        @if ($d->scheduled_at)
                                            &middot; Jadwal
                                            {{ $d->scheduled_at->format('d M Y H:i') }}
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

                            <li
                                class="{{ $loop->first ? 'terkini' : '' }}"
                                style="--i:{{ $loop->index }}"
                            >

                                <strong>
                                    {{ $order->statusLabel($log->status) }}
                                </strong>

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

            {{-- Bagian samping --}}
            <aside class="detail-samping">

                <section class="kartu-area anim-naik" style="--i:3">

                  
<h2>Rincian Biaya</h2>

<div class="ringkasan">

    <div class="baris">
        <span>Subtotal layanan</span>
        <span>
            Rp {{ number_format($order->subtotal, 0, ',', '.') }}
        </span>
    </div>

    <div class="baris">
        <span>Biaya jemput/antar</span>
        <span>
            Rp {{ number_format($order->delivery_fee_total, 0, ',', '.') }}
        </span>
    </div>

    @if ($order->discount_total > 0)
        <div class="baris" style="color:#12714A;font-weight:600">
            <span>Diskon voucher @if ($order->voucher_code)({{ $order->voucher_code }})@endif</span>
            <span>- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="baris total">
        <span>Total</span>
        <span>
            Rp {{ number_format($order->total, 0, ',', '.') }}
        </span>
    </div>

</div>


                   
{{-- Status pembayaran --}}
<div class="bayar-baris">

    <span>Pembayaran:</span>

    @if ($order->payment_status === 'lunas')

        <span class="badge badge-success">
            Lunas
        </span>

        <span>
            ({{ $order->metodeLabel() }})
        </span>

    @else

        <span class="badge badge-warning">
            Belum dibayar
        </span>

    @endif

</div>

@if ($order->payment_status !== 'lunas')

    <p class="bantu" style="margin-top:.5rem">
        Metode: <strong>{{ $order->metodeLabel() }}</strong>.
        @if (in_array($order->payment_method, ['cod', 'tunai', null], true))
            Dibayar tunai saat sepatu diterima kembali.
        @endif
    </p>

    @if ($order->payment_method === 'transfer')
        @php
            $bank = \App\Models\PaymentAccount::where('bank_name', $order->payment_channel)->first();
        @endphp

        @if ($bank)
            <p class="bantu" style="margin-top:.4rem">
                Transfer ke {{ $bank->bank_name }}
                <strong>{{ $bank->account_number }}</strong>
                a.n. {{ $bank->account_name }},
                lalu kirim bukti transfer lewat menu
                <a href="{{ route('chat.order', $order) }}">Chat</a>
            </p>
        @endif

    @elseif ($order->payment_method === 'qris')

        @php
            $qrisAktif = \App\Models\QrisCode::aktif()->first();
            $qris = $qrisAktif ? 'img/qris/'.$qrisAktif->image : null;
        @endphp

        @if ($qris && file_exists(public_path($qris)))
            <img
                src="{{ asset($qris) }}"
                alt="Kode QRIS"
                style="width:160px;max-width:100%;margin-top:.6rem;border-radius:12px;border:1px solid var(--garis)"
            >
        @endif

        <p class="bantu" style="margin-top:.4rem">
            Scan QRIS dengan aplikasi apa pun, lalu kirim bukti pembayaran lewat menu
            <a href="{{ route('chat.index') }}">Chat</a>.
        </p>

    @endif

@endif

                    {{-- Tombol batalkan pesanan --}}
                    @if ($order->status->bisaDibatalkanPengguna())

                        <form
                            method="POST"
                            action="{{ route('pesanan.batal', $order) }}"
                            onsubmit="return confirm('Batalkan pesanan {{ $order->code }}?')"
                        >

                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-bahaya btn-batal"
                            >
                                Batalkan Pesanan
                            </button>

                        </form>

                    @endif

                </section>

            </aside>

        </div>

    </div>
@endsection