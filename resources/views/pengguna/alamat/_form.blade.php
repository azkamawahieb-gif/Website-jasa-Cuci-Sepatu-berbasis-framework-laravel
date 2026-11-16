{{-- Form satu alamat gaya Shopee. Dipakai di halaman Alamat Saya (tambah dan ubah) dan di kotak
     "Tambah alamat baru" pada form pesanan.
     Variabel: $p (awalan id, unik per form), $alamat (Address atau null), $aksi (URL), $metode ('PUT' atau null),
               $tombol (teks tombol), $ajax (true bila dikirim lewat JavaScript).
     Atribut data-peta, data-lat/lng/url/lokasi/cari/status/preview/frame dan class tandai-pilih dipakai public/js/alamat.js. --}}
@php
  $adaGalat = old('_form') === $p;
  $nilai = fn (string $k, $bawaan = null) => $adaGalat ? old($k) : $bawaan;
  $galat = fn (string $k) => $adaGalat ? $errors->first($k) : null;
  $labelAwal = $nilai('label', $alamat?->label);
@endphp

<form method="POST" action="{{ $aksi }}" class="form-alamat" @if (!empty($ajax)) data-ajax-alamat @endif novalidate>
  @csrf
  @if (!empty($metode)) @method($metode) @endif
  <input type="hidden" name="_form" value="{{ $p }}">

  {{-- Kartu 1: Alamat --}}
  <section class="fa-kartu" aria-labelledby="{{ $p }}_h_alamat">
    <h3 id="{{ $p }}_h_alamat">Alamat</h3>

    <div class="fa-baris">
      <div class="form-group">
        <label for="{{ $p }}_province">Provinsi</label>
        <input type="text" id="{{ $p }}_province" name="province" maxlength="100" required
               class="form-control @if ($galat('province')) salah @endif"
               value="{{ $nilai('province', $alamat?->province) }}" placeholder="Contoh: Riau">
        @if ($galat('province'))<p class="pesan-salah" role="alert">{{ $galat('province') }}</p>@endif
      </div>
      <div class="form-group">
        <label for="{{ $p }}_regency">Kabupaten / Kota</label>
        <input type="text" id="{{ $p }}_regency" name="regency" maxlength="100" required
               class="form-control @if ($galat('regency')) salah @endif"
               value="{{ $nilai('regency', $alamat?->regency ?? $alamat?->city) }}" placeholder="Contoh: Kota Pekanbaru">
        @if ($galat('regency'))<p class="pesan-salah" role="alert">{{ $galat('regency') }}</p>@endif
      </div>
      <div class="form-group">
        <label for="{{ $p }}_district">Kecamatan</label>
        <input type="text" id="{{ $p }}_district" name="district" maxlength="100" required
               class="form-control @if ($galat('district')) salah @endif"
               value="{{ $nilai('district', $alamat?->district) }}" placeholder="Contoh: Senapelan">
        @if ($galat('district'))<p class="pesan-salah" role="alert">{{ $galat('district') }}</p>@endif
      </div>
      <div class="form-group">
        <label for="{{ $p }}_postal_code">Kode Pos</label>
        <input type="text" id="{{ $p }}_postal_code" name="postal_code" inputmode="numeric" maxlength="5" required
               class="form-control @if ($galat('postal_code')) salah @endif"
               value="{{ $nilai('postal_code', $alamat?->postal_code) }}" placeholder="28151">
        @if ($galat('postal_code'))<p class="pesan-salah" role="alert">{{ $galat('postal_code') }}</p>@endif
      </div>
    </div>

    <div class="form-group">
      <label for="{{ $p }}_address">Nama Jalan, Gedung, No. Rumah</label>
      <textarea id="{{ $p }}_address" name="address" rows="2" maxlength="500" required
                class="form-control @if ($galat('address')) salah @endif"
                placeholder="Contoh: Jalan Singkawang Gang Mahoni No. 4">{{ $nilai('address', $alamat?->address) }}</textarea>
      @if ($galat('address'))<p class="pesan-salah" role="alert">{{ $galat('address') }}</p>@endif
    </div>

    <div class="form-group">
      <label for="{{ $p }}_detail">Detail Lainnya (Cth: Blok / Unit No., Patokan)</label>
      <input type="text" id="{{ $p }}_detail" name="detail" maxlength="200" class="form-control"
             value="{{ $nilai('detail', $alamat?->detail) }}" placeholder="Contoh: Pagar hijau, dekat masjid">
    </div>
  </section>

  {{-- Kartu 2: Informasi Penerima --}}
  <section class="fa-kartu" aria-labelledby="{{ $p }}_h_penerima">
    <h3 id="{{ $p }}_h_penerima">Informasi Penerima</h3>
    <p class="bantu">Orang yang menyerahkan sepatu saat dijemput, atau yang menerima sepatu saat diantar.</p>

    <div class="form-group">
      <label for="{{ $p }}_recipient_name">Nama Lengkap</label>
      <input type="text" id="{{ $p }}_recipient_name" name="recipient_name" maxlength="100" required
             class="form-control @if ($galat('recipient_name')) salah @endif" autocomplete="name"
             value="{{ $nilai('recipient_name', $alamat?->recipient_name ?? auth()->user()->name) }}">
      @if ($galat('recipient_name'))<p class="pesan-salah" role="alert">{{ $galat('recipient_name') }}</p>@endif
    </div>

    <div class="form-group">
      <label for="{{ $p }}_phone">Nomor Telepon</label>
      <input type="text" id="{{ $p }}_phone" name="phone" inputmode="tel" maxlength="20" required
             class="form-control @if ($galat('phone')) salah @endif" autocomplete="tel"
             value="{{ $nilai('phone', $alamat?->phone ?? auth()->user()->phone) }}">
      @if ($galat('phone'))<p class="pesan-salah" role="alert">{{ $galat('phone') }}</p>@endif
    </div>
  </section>

  {{-- Kartu 3: Pengaturan --}}
  <section class="fa-kartu" aria-label="Pengaturan alamat">
    <div class="fa-saklar-baris">
      <label class="saklar" for="{{ $p }}_is_default">
        <span>Atur sebagai Alamat Utama</span>
        <input type="checkbox" role="switch" id="{{ $p }}_is_default" name="is_default" value="1"
               @checked($nilai('is_default', $alamat?->is_default))>
        <span class="saklar-geser" aria-hidden="true"></span>
      </label>
    </div>

    <div class="tandai">
      <span id="{{ $p }}_h_tandai" class="tandai-judul">Tandai Sebagai:</span>
      <div class="tandai-pilih" role="radiogroup" aria-labelledby="{{ $p }}_h_tandai">
        @foreach (['Kantor', 'Rumah'] as $opsi)
          <label class="chip">
            <input type="radio" name="label" value="{{ $opsi }}" @checked($labelAwal === $opsi)>
            <span>{{ $opsi }}</span>
          </label>
        @endforeach
      </div>
    </div>
    @if ($galat('label'))<p class="pesan-salah" role="alert">{{ $galat('label') }}</p>@endif
  </section>

  {{-- Titik lokasi lewat Google Maps (opsional) --}}
  <fieldset class="peta-blok" data-peta>
    <legend>Titik lokasi di Google Maps (opsional)</legend>
    <p class="bantu">Membantu petugas menemukan lokasi. Pakai lokasi Anda sekarang, atau cari di Google Maps lalu tempel tautannya.</p>

    <input type="hidden" name="latitude" data-lat value="{{ $nilai('latitude', $alamat?->latitude) }}">
    <input type="hidden" name="longitude" data-lng value="{{ $nilai('longitude', $alamat?->longitude) }}">

    <div class="peta-aksi">
      <button type="button" class="btn btn-outline btn-kecil" data-lokasi>Pakai lokasi saya</button>
      <button type="button" class="btn btn-outline btn-kecil" data-cari>Cari di Google Maps</button>
    </div>

    <div class="form-group">
      <label for="{{ $p }}_maps_url">Tautan Google Maps</label>
      <input type="url" id="{{ $p }}_maps_url" name="maps_url" maxlength="500" data-url
             class="form-control @if ($galat('maps_url')) salah @endif"
             value="{{ $nilai('maps_url', $alamat?->maps_url) }}" placeholder="https://maps.app.goo.gl/...">
      @if ($galat('maps_url'))<p class="pesan-salah" role="alert">{{ $galat('maps_url') }}</p>@endif
    </div>

    <p class="bantu" data-status role="status" aria-live="polite"></p>

    <div class="peta-preview sembunyi" data-preview>
      <iframe data-frame title="Pratinjau titik lokasi di Google Maps" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </fieldset>

  <button type="submit" class="btn btn-block">{{ $tombol }}</button>
</form>