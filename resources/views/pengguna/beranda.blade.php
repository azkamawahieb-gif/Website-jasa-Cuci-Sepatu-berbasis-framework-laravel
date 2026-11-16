
@extends('layouts.pengguna')
@section('title', 'Beranda')

@push('css')
  <link rel="stylesheet" href="{{ asset('css/beranda.css') }}">
@endpush

@section('hero')

{{-- ================= HERO ================= --}}
<section class="hero-utama" id="beranda">
  <span class="hero-label anim-naik" style="--i: 0">Care Beyond Clean.</span>
  <h1 class="anim-naik" style="--i: 1">Perawatan lebih dari sekadar <span class="teks-gradien">kebersihan.</span></h1>
  <p class="hero-sub anim-naik" style="--i: 2">Cuci sepatu profesional dengan layanan jemput dan antar langsung ke rumah Anda.</p>
  <div class="hero-aksi anim-naik" style="--i: 3">
    <a href="#layanan" class="btn btn-ghost btn-lg">Lihat Layanan</a>
    <a href="{{ route('pesanan.buat') }}" class="btn btn-lg">Pesan Sekarang</a>
  </div>

  {{-- Ilustrasi dekoratif: platform isometrik, sepatu, chip kaca, gelembung --}}
  <div class="ilustrasi anim-naik" style="--i: 5" aria-hidden="true">
    <svg viewBox="0 0 520 330" focusable="false">
      <ellipse cx="260" cy="302" rx="215" ry="24" fill="#0B2545" opacity=".12"/>

      <polygon points="60,215 260,285 260,305 60,235" fill="#90E1DA"/>
      <polygon points="460,215 260,285 260,305 460,235" fill="#71ADD4"/>
      <polygon points="60,215 260,145 460,215 260,285" fill="#E8FFF7"/>

      <polygon points="120,197 260,246 260,264 120,215" fill="#80C6D8"/>
      <polygon points="400,197 260,246 260,264 400,215" fill="#6394D3"/>
      <polygon points="120,197 260,148 400,197 260,246" fill="#FFFFFF"/>

      <g class="ngambang">
        <polygon points="176,183 260,212 260,226 176,197" fill="#71ADD4"/>
        <polygon points="344,183 260,212 260,226 344,197" fill="#2D5BB0"/>
        <polygon points="176,183 260,154 344,183 260,212" fill="#E8FFF7"/>

        <g transform="translate(260 183) scale(.9)">
          <ellipse cx="0" cy="2" rx="60" ry="8" fill="#0B2545" opacity=".14"/>
          <path d="M-60 -10 L-60 -34 C-60 -40 -55 -44 -49 -44 L-30 -46 C-24 -46 -20 -42 -16 -38 C-8 -28 4 -24 18 -22 C38 -19 60 -17 62 -10 Z" fill="#fff" stroke="#2D5BB0" stroke-width="2.4" stroke-linejoin="round"/>
          <path d="M-48 -27 C-30 -23 -10 -20 10 -14" fill="none" stroke="#6394D3" stroke-width="3" stroke-linecap="round"/>
          <path d="M-20 -40 l6 7 M-12 -36 l6 7 M-4 -32 l6 7" fill="none" stroke="#2D5BB0" stroke-width="2.2" stroke-linecap="round"/>
          <rect x="-64" y="-11" width="128" height="11" rx="5.5" fill="#2D5BB0"/>
        </g>
      </g>
    </svg>

    <span class="gel gel-1"></span><span class="gel gel-2"></span>
    <span class="gel gel-3"></span><span class="gel gel-4"></span>

    <span class="chip chip-1">
      <span class="chip-ico"><svg viewBox="0 0 24 24"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
      Dijemput
    </span>
    <span class="chip chip-2">
      <span class="chip-ico"><svg viewBox="0 0 24 24"><path d="M12 3s6 6.2 6 10.5A6 6 0 0 1 6 13.5C6 9.2 12 3 12 3z"/></svg></span>
      Sedang dicuci
    </span>
    <span class="chip chip-3">
      <span class="chip-ico"><svg viewBox="0 0 24 24"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/></svg></span>
      Siap diantar
    </span>
  </div>
</section>
@endsection

@section('content')

{{-- ================= CARA KERJA ================= --}}
<section id="cara-kerja" class="bagian">
  <div class="bagian-kepala reveal">
    <span class="eyebrow">Cara Kerja</span>
    <h2>Empat langkah, sepatu bersih kembali</h2>
    <p>Dari pesan sampai sepatu kembali ke tangan Anda.</p>
  </div>

  <div class="langkah">
    <div class="langkah-item reveal" style="--i: 0">
      <div class="nomor">1</div>
      <h3>Pesan</h3>
      <p>Pilih layanan dan isi data sepatu Anda.</p>
    </div>
    <div class="langkah-item reveal" style="--i: 1">
      <div class="nomor">2</div>
      <h3>Dijemput atau antar sendiri</h3>
      <p>Petugas menjemput ke alamat Anda, atau Anda mengantar sepatu sendiri.</p>
    </div>
    <div class="langkah-item reveal" style="--i: 2">
      <div class="nomor">3</div>
      <h3>Dicuci</h3>
      <p>Sepatu dicuci sesuai layanan, dengan foto kondisi sebelum dan sesudah.</p>
    </div>
    <div class="langkah-item reveal" style="--i: 3">
      <div class="nomor">4</div>
      <h3>Diantar atau diambil</h3>
      <p>Sepatu diantar ke alamat Anda atau diambil sendiri. Bayar saat sepatu diterima.</p>
    </div>
  </div>
</section>

{{-- ================= LAYANAN ================= --}}
<section id="layanan" class="bagian">
  <div class="bagian-kepala reveal">
    <span class="eyebrow">Layanan</span>
    <h2>Layanan Kami</h2>
    <p>Pilih perawatan sesuai kondisi dan bahan sepatu Anda.</p>
  </div>

  @if ($layanan->isNotEmpty())
    <div class="kartu-grid">
      @foreach ($layanan as $s)
        @php $foto = $s->fotoUrl(); @endphp

        <div class="kartu-layanan reveal" style="--i: {{ $loop->index }}">
          @if ($foto)
            <div class="kartu-foto">
              <img src="{{ $foto }}" alt="{{ $s->name }}" width="800" height="600" loading="lazy" decoding="async">
            </div>
          @else
            <div class="kartu-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M12 3s6 6.2 6 10.5A6 6 0 0 1 6 13.5C6 9.2 12 3 12 3z"/></svg>
            </div>
          @endif

          <h3>{{ $s->name }}</h3>
          <p>{{ $s->description }}</p>

          <div class="kartu-meta">
            <div class="kartu-harga">Rp {{ number_format($s->price, 0, ',', '.') }} <small>/ pasang</small></div>
            <span class="kartu-est">Estimasi {{ $s->estimated_days }} hari</span>
          </div>

          <div class="kartu-aksi">
            <a href="{{ route('layanan.detail', $s->slug()) }}" class="btn btn-ghost">Detail</a>
            <a href="{{ route('pesanan.buat', ['layanan' => $s->id]) }}" class="btn">Pesan</a>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <p class="kosong">Layanan belum tersedia. Silakan kembali lagi nanti.</p>
  @endif
</section>

{{-- ================= JEMPUT DAN ANTAR ================= --}}
<section id="antar" class="bagian">
  <div class="bagian-kepala reveal">
    <span class="eyebrow">Jemput dan Antar</span>
    <h2>Jemput &amp; Antar</h2>
    <p>Sepatu bisa kami jemput dan antar kembali. Biaya mengikuti zona, dihitung per sekali jemput atau antar.</p>
  </div>

  @if ($zona->isNotEmpty())
    <div class="zona-grid">
      @foreach ($zona as $z)
        <div class="zona-kartu reveal" style="--i: {{ $loop->index }}">
          <div>
            <h3>{{ $z->name }}</h3>
            <p>{{ $z->description ?: 'Area layanan' }}</p>
          </div>
          <div class="zona-fee">Rp {{ number_format($z->fee, 0, ',', '.') }}</div>
        </div>
      @endforeach
    </div>
    <p class="catatan">Jemput dan antar bebas dikombinasikan: antar sendiri atau dijemput, ambil sendiri atau diantar.</p>
  @else
    <p class="kosong">Zona jemput dan antar belum tersedia.</p>
  @endif
</section>

{{-- ================= KEUNGGULAN ================= --}}
<section id="keunggulan" class="bagian">
  <div class="bagian-kepala reveal">
    <span class="eyebrow">Keunggulan</span>
    <h2>Mudah dipantau, jelas buktinya</h2>
  </div>

  <div class="fitur-grid">
    <div class="fitur reveal" style="--i: 0">
      <div class="fitur-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 12h4l3-8 4 16 3-8h4"/></svg></div>
      <h3>Pantau status</h3>
      <p>Lihat tahap pesanan Anda kapan saja setelah masuk.</p>
    </div>
    <div class="fitur reveal" style="--i: 1">
      <div class="fitur-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/></svg></div>
      <h3>Foto sebelum dan sesudah</h3>
      <p>Kondisi sepatu didokumentasikan sebagai bukti.</p>
    </div>
    <div class="fitur reveal" style="--i: 2">
      <div class="fitur-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 7h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M3 7V6a2 2 0 0 1 2-2h12"/><circle cx="17" cy="14" r="1"/></svg></div>
      <h3>Bayar saat sepatu diterima</h3>
      <p>Pembayaran tunai, transfer, atau QRIS setelah sepatu kembali.</p>
    </div>
    <div class="fitur reveal" style="--i: 3">
      <div class="fitur-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4z"/><path d="M10 8v8"/></svg></div>
      <h3>Kode pesanan</h3>
      <p>Setiap pesanan punya kode unik sebagai bukti ambil.</p>
    </div>
  </div>
</section>

{{-- ================= FAQ ================= --}}
<section id="faq" class="bagian">
  <div class="bagian-kepala reveal">
    <span class="eyebrow">FAQ</span>
    <h2>Pertanyaan Umum</h2>
  </div>

  <div class="faq">
    <details>
      <summary>Bagaimana cara memesan?</summary>
      <p>Pilih layanan, klik Pesan, lalu daftar atau masuk. Isi data sepatu dan pilih apakah sepatu dijemput atau diantar sendiri.</p>
    </details>
    <details>
      <summary>Bagaimana saya tahu sepatu sudah sampai di tahap mana?</summary>
      <p>Setelah masuk, buka menu Pesanan Saya. Status dan riwayat tiap pesanan tampil di sana.</p>
    </details>
    <details>
      <summary>Kapan saya membayar?</summary>
      <p>Pembayaran dilakukan saat sepatu diterima kembali, bisa tunai, transfer, atau QRIS.</p>
    </details>
    <details>
      <summary>Apakah pesanan bisa dibatalkan?</summary>
      <p>Bisa, selama statusnya masih Menunggu Konfirmasi atau Menunggu Jemput.</p>
    </details>
  </div>
</section>

{{-- ================= BANNER AJAKAN ================= --}}
<section class="cta reveal">
  <h2>Siap membuat sepatu Anda bersih kembali?</h2>
  <p>Pesan sekarang dan biarkan kami yang menjemput, mencuci, dan mengantarnya.</p>
  <a href="{{ route('pesanan.buat') }}" class="btn btn-lg">Pesan Sekarang</a>
</section>

@endsection
