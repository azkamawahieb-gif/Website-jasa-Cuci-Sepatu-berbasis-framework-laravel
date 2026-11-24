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

  {{-- ================= SLIDER FOTO HERO ================= --}}
  @php
    // Foto slider: semua gambar di public/img/hero (urut nama file)
    $dirHero = public_path('img/hero');
    $fotoHero = collect(is_dir($dirHero) ? scandir($dirHero) : [])
      ->filter(fn ($f) => preg_match('/\.(jpe?g|png|webp)$/i', $f))
      ->sort(SORT_NATURAL | SORT_FLAG_CASE)->values();
  @endphp

  <div class="ilustrasi ilustrasi-foto anim-naik" style="--i: 5">
    <div class="hero-slider" data-slider role="region" aria-roledescription="carousel" aria-label="Foto hasil cuci sepatu">
      <div class="hs-track" tabindex="0">
        @forelse ($fotoHero as $berkas)
          <figure class="hs-slide" aria-roledescription="slide" aria-label="Foto {{ $loop->iteration }} dari {{ $loop->count }}">
            <img src="{{ asset('img/hero/'.rawurlencode($berkas)) }}" alt="Foto layanan Cucci.in {{ $loop->iteration }}"
                 @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
          </figure>
        @empty
          <figure class="hs-slide hs-kosong">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2.5"/><circle cx="9" cy="10" r="1.8"/><path d="m21 16-5-5-8 9"/></svg>
            <span>Foto segera hadir</span>
          </figure>
        @endforelse
      </div>

      @if ($fotoHero->count() > 1)
        <button type="button" class="hs-nav hs-prev" data-prev aria-label="Foto sebelumnya">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>
        </button>
        <button type="button" class="hs-nav hs-next" data-next aria-label="Foto berikutnya">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 5 7 7-7 7"/></svg>
        </button>
        <div class="hs-dots">
          @foreach ($fotoHero as $berkas)
            <button type="button" class="hs-dot" data-dot="{{ $loop->index }}" aria-label="Ke foto {{ $loop->iteration }}"></button>
          @endforeach
        </div>
      @endif
    </div>
    </span>
  </div>
</section>

@if ($fotoHero->count() > 1)
  <script src="{{ asset('js/hero-slider.js') }}" defer></script>
@endif
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