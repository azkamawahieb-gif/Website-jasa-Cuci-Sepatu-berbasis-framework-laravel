@extends('layouts.pengguna')
@section('title', 'Buat Pesanan')

@push('css')
<link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
@endpush

@section('content')
@php
  $baris = old('items', [['service_id' => $dipilih]]);
  $mulai = collect($baris)->keys()->map(fn ($k) => (int) $k)->max() + 1;
  $opsiAntar = [
    'jemput' => 'Jemput sepatu ke alamat saya',
    'antar'  => 'Antar sepatu ke alamat saya setelah selesai',
  ];
@endphp

<div class="area">
  <div class="kepala-halaman anim-naik">
    <span class="label">Pesanan baru</span>
    <h1>Buat Pesanan</h1>
    <p>Isi data sepatu, pilih layanan, lalu tentukan apakah sepatu dijemput/diantar atau Anda antar dan ambil sendiri.</p>
  </div>

  <form method="POST" action="{{ route('pesanan.simpan') }}" enctype="multipart/form-data" id="form-pesanan">
    @csrf

    <div class="pesan-kolom">
      <div class="langkah-daftar">

        {{-- Langkah 1: Sepatu --}}
        <section class="langkah-panel anim-naik" style="--i:1" aria-labelledby="judul-sepatu">
          <span class="langkah-nomor" aria-hidden="true">1</span>
          <h2 id="judul-sepatu">Sepatu</h2>
          <p class="sub">Tambahkan satu atau beberapa pasang sepatu. Foto kondisi awal membantu kami mencatat keadaannya.</p>

          <div id="daftar-sepatu">
            @foreach ($baris as $i => $row)
              @include('pengguna.pesanan._item', ['i' => $i, 'row' => $row, 'layanan' => $layanan])
            @endforeach
          </div>

          <button type="button" class="btn btn-outline" id="tambah-sepatu">+ Tambah sepatu</button>
        </section>

        {{-- Langkah 2: Jemput & Antar --}}
        <section class="langkah-panel anim-naik" style="--i:2" aria-labelledby="judul-antar">
          <span class="langkah-nomor" aria-hidden="true">2</span>
          <h2 id="judul-antar">Jemput &amp; Antar</h2>
          <p class="sub">Boleh dipilih keduanya, salah satu, atau tidak sama sekali (antar dan ambil sendiri di tempat kami).</p>

          @if ($zona->isEmpty())
            <p class="muted">Layanan jemput/antar belum tersedia.</p>
          @else
            @foreach ($opsiAntar as $tipe => $judul)
              <div class="opsi">
                <label class="cek">
                  <input type="hidden" name="{{ $tipe }}" value="0">
                  <input type="checkbox" name="{{ $tipe }}" value="1" id="{{ $tipe }}" data-toggle="{{ $tipe }}-isi"
                         @checked(old($tipe) == '1')>
                  <strong>{{ $judul }}</strong>
                </label>

                <div id="{{ $tipe }}-isi" class="{{ old($tipe) == '1' ? '' : 'sembunyi' }}">
                  @php
                    $pilih = (string) old($tipe . '_pilih', $alamat->isNotEmpty() ? $alamat->first()->id : 'manual');
                    $terpilih = $alamat->first(fn ($a) => (string) $a->id === $pilih);
                    $manual = ! $terpilih;
                    $teksAwal = old($tipe . '_address', $terpilih ? $terpilih->lengkap() : auth()->user()->address);
                    $petaAwal = old($tipe . '_maps_url', $terpilih?->petaUrl());
                  @endphp

                  <div class="pilih-alamat" data-pilih="{{ $tipe }}">
                    <fieldset class="alamat-daftar">
                      <legend>Pilih alamat</legend>

                      @foreach ($alamat as $a)
                        <label class="alamat-opsi">
                          <input type="radio" name="{{ $tipe }}_pilih" value="{{ $a->id }}"
                                 data-teks="{{ $a->lengkap() }}" data-tautan="{{ $a->petaUrl() }}"
                                 data-json="{{ json_encode($a->ringkas()) }}"
                                 @checked(! $manual && (string) $a->id === $pilih)>
                          <span class="alamat-isi">
                            <span class="alamat-nama">
                              <strong>{{ $a->recipient_name }}</strong>
                              <span class="muted">| {{ $a->phone }}</span>
                              @if ($a->is_default)<span class="alamat-lencana badge-utama">Utama</span>@endif
                              @if ($a->label)<span class="alamat-lencana">{{ $a->label }}</span>@endif
                            </span>
                            <span>{{ $a->address }}</span>
                            @if ($a->wilayah() !== '')<span class="muted">{{ $a->wilayah() }}</span>@endif
                          </span>
                          <button type="button" class="alamat-ubah-btn" data-ubah-alamat="{{ $a->id }}">Ubah</button>
                        </label>
                      @endforeach

                      <label class="alamat-opsi" data-opsi-manual>
                        <input type="radio" name="{{ $tipe }}_pilih" value="manual" @checked($manual)>
                        <span class="alamat-isi"><strong>Isi alamat manual</strong></span>
                      </label>
                    </fieldset>

                    <button type="button" class="btn btn-outline btn-kecil" data-tambah-alamat>+ Tambah alamat baru</button>
                    <a href="{{ route('alamat.index') }}" class="alamat-kelola">Kelola alamat</a>

                    <div class="form-group">
                      <label for="{{ $tipe }}_address">Alamat</label>
                      <textarea name="{{ $tipe }}_address" id="{{ $tipe }}_address" rows="2" data-teks-alamat
                                class="form-control @error($tipe . '_address') salah @enderror"
                                @readonly(! $manual)>{{ $teksAwal }}</textarea>
                      @error($tipe . '_address')
                        <p class="pesan-salah" role="alert">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label for="{{ $tipe }}_maps_url">Tautan Google Maps (opsional)</label>
                      <div class="peta-satubaris">
                        <input type="url" name="{{ $tipe }}_maps_url" id="{{ $tipe }}_maps_url" data-teks-peta
                               class="form-control @error($tipe . '_maps_url') salah @enderror"
                               value="{{ $petaAwal }}" @readonly(! $manual)>
                        <button type="button" class="btn btn-outline btn-kecil" data-cari>Cari di Google Maps</button>
                      </div>
                      @error($tipe . '_maps_url')
                        <p class="pesan-salah" role="alert">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="{{ $tipe }}_zone">Zona (menentukan biaya)</label>
                      <select name="{{ $tipe }}_zone" id="{{ $tipe }}_zone" class="form-control @error($tipe . '_zone') salah @enderror">
                        <option value="" data-fee="0">- Pilih zona -</option>
                        @foreach ($zona as $z)
                          <option value="{{ $z->id }}" data-fee="{{ $z->fee }}" @selected(old($tipe . '_zone') == $z->id)>
                            {{ $z->name }}{{ $z->description ? ' - ' . $z->description : '' }} (Rp {{ number_format($z->fee, 0, ',', '.') }})
                          </option>
                        @endforeach
                      </select>
                      @error($tipe . '_zone')
                        <p class="pesan-salah" role="alert">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label for="{{ $tipe }}_at">Jadwal yang diinginkan (opsional)</label>
                      <input type="datetime-local" name="{{ $tipe }}_at" id="{{ $tipe }}_at"
                             class="form-control @error($tipe . '_at') salah @enderror" value="{{ old($tipe . '_at') }}">
                      @error($tipe . '_at')
                        <p class="pesan-salah" role="alert">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </section>

        {{-- Langkah 3: Catatan --}}
        <section class="langkah-panel anim-naik" style="--i:3" aria-labelledby="judul-catatan">
          <span class="langkah-nomor" aria-hidden="true">3</span>
          <h2 id="judul-catatan">Catatan</h2>
          <p class="sub">Ada pesan khusus untuk petugas? Tulis di sini.</p>

          <div class="form-group" style="margin-bottom:0">
            <label for="notes" class="sembunyi-label">Catatan tambahan</label>
            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') salah @enderror">{{ old('notes') }}</textarea>
            @error('notes')
              <p class="pesan-salah" role="alert">{{ $message }}</p>
            @enderror
          </div>
        </section>

        {{-- Langkah 4: Voucher --}}
        <section class="langkah-panel anim-naik" style="--i:4" aria-labelledby="judul-voucher">
          <span class="langkah-nomor" aria-hidden="true">4</span>
          <h2 id="judul-voucher">Voucher</h2>
          <p class="sub">Punya kode voucher? Masukkan di sini untuk mendapat potongan harga layanan.</p>

          <div class="voucher-input">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 9a2 2 0 0 0 0 6v3a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-3a2 2 0 0 1 0-6V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"/><path d="M14 5v14" stroke-dasharray="2 2.5"/></svg>
            <input type="text" id="voucher-kode" class="form-control" maxlength="30" autocomplete="off"
                   autocapitalize="characters" spellcheck="false" aria-label="Kode voucher"
                   value="{{ old('voucher_code') }}">
            <button type="button" class="btn btn-outline btn-kecil" id="voucher-pakai">Pakai</button>
          </div>
          <p class="voucher-pesan" id="voucher-pesan" role="status" aria-live="polite"></p>

          <div class="voucher-aktif sembunyi" id="voucher-aktif">
            <span class="voucher-tag" aria-hidden="true">%</span>
            <span class="voucher-info"><strong id="voucher-nama"></strong><small id="voucher-ket"></small></span>
            <button type="button" class="link-btn teks-bahaya" id="voucher-hapus">Hapus</button>
          </div>

          <input type="hidden" name="voucher_code" id="voucher-hidden" value="">
          @error('voucher_code')
            <p class="pesan-salah" role="alert">{{ $message }}</p>
          @enderror
        </section>

        {{-- Langkah 5: Metode pembayaran --}}
        @php
          $metodeBayar = config('pembayaran.metode');
          $metodeDipilih = old('payment_method', 'cod');
          $channelLama = old('payment_channel');
          $qrisFile = config('pembayaran.qris.gambar');
          $qrisAda = $qrisFile && file_exists(public_path($qrisFile));
        @endphp
        <section class="langkah-panel anim-naik" style="--i:5" aria-labelledby="judul-bayar">
          <span class="langkah-nomor" aria-hidden="true">5</span>
          <h2 id="judul-bayar">Metode Pembayaran</h2>
          <p class="sub">Pilih cara membayar. Pembayaran dicatat petugas setelah pesanan dikonfirmasi.</p>

          <div class="bayar-pilihan" role="radiogroup" aria-label="Metode pembayaran">
            @foreach ($metodeBayar as $kode => $m)
              <label class="bayar-kartu">
                <input type="radio" name="payment_method" value="{{ $kode }}" data-nama="{{ $m['nama'] }}" @checked($metodeDipilih === $kode)>
                <span class="bayar-isi">
                  <span class="bayar-ikon" aria-hidden="true">
                    @if ($kode === 'cod')
                      <svg viewBox="0 0 24 24"><rect x="2.5" y="6" width="19" height="12" rx="2.5"/><circle cx="12" cy="12" r="2.6"/><path d="M6 9.5v.01M18 14.5v.01"/></svg>
                    @elseif ($kode === 'transfer')
                      <svg viewBox="0 0 24 24"><path d="m3 9 9-5 9 5"/><path d="M5 10v7M9.5 10v7M14.5 10v7M19 10v7M3 20h18"/></svg>
                    @else
                      <svg viewBox="0 0 24 24"><rect x="3.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.2"/><path d="M14 14h2.5v2.5H14zM18.5 14v.01M14 19v.01M17 18.5h3.5V21"/></svg>
                    @endif
                  </span>
                  <span class="bayar-teks"><strong>{{ $m['nama'] }}</strong><small>{{ $m['ket'] }}</small></span>
                  <span class="bayar-centang" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>
                </span>
              </label>
            @endforeach
          </div>
          @error('payment_method')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror

          {{-- COD --}}
          <div class="bayar-panel {{ $metodeDipilih === 'cod' ? '' : 'sembunyi' }}" data-panel="cod">
            <p class="bayar-catatan"><strong>Bayar di tempat.</strong> Siapkan uang tunai sesuai total tagihan. Petugas mencatat pembayaran saat sepatu diterima kembali.</p>
          </div>

          {{-- Transfer --}}
          <fieldset class="bayar-panel {{ $metodeDipilih === 'transfer' ? '' : 'sembunyi' }}" data-panel="transfer" @disabled($metodeDipilih !== 'transfer')>
            <legend>Pilih bank tujuan</legend>
            <div class="bank-daftar">
              @foreach (config('pembayaran.bank') as $b)
                <label class="bank-kartu">
                  <input type="radio" name="payment_channel" value="{{ $b['nama'] }}" @checked($metodeDipilih === 'transfer' && $channelLama === $b['nama'])>
                  <span class="bank-isi">
                    <span class="bank-logo" aria-hidden="true">{{ $b['nama'] }}</span>
                    <span class="bank-data">
                      <strong>{{ $b['rekening'] }}</strong>
                      <small>a.n. {{ $b['atas_nama'] }}</small>
                    </span>
                    <button type="button" class="bank-salin" data-salin="{{ $b['rekening'] }}">Salin</button>
                  </span>
                </label>
              @endforeach
            </div>
            @error('payment_channel')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
            <p class="bayar-catatan">Transfer sesuai total setelah pesanan dikonfirmasi petugas, lalu kirim bukti transfer lewat menu <a href="{{ route('chat.index') }}">Chat</a>.</p>
          </fieldset>

          {{-- QRIS --}}
          <fieldset class="bayar-panel {{ $metodeDipilih === 'qris' ? '' : 'sembunyi' }}" data-panel="qris" @disabled($metodeDipilih !== 'qris')>
            <legend>Scan QRIS</legend>
            <div class="qris-kotak">
              <div class="qris-gambar">
                @if ($qrisAda)
                  <img src="{{ asset($qrisFile) }}" alt="Kode QRIS {{ config('pembayaran.qris.merchant') }}" loading="lazy">
                @else
                  <div class="qris-kosong" role="img" aria-label="Kode QRIS belum dipasang">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.2"/><path d="M14 14h2.5v2.5H14zM18.5 14v.01M14 19v.01M17 18.5h3.5V21"/></svg>
                    <span>QRIS toko</span>
                  </div>
                @endif
              </div>
              <div class="qris-info">
                <strong>{{ config('pembayaran.qris.merchant') }}</strong>
                <span class="muted">Total yang dibayar</span>
                <span class="qris-total" id="qris-total">Rp 0</span>
                @if ($qrisAda)
                  <a class="btn btn-outline btn-kecil" href="{{ asset($qrisFile) }}" download>Unduh QRIS</a>
                @endif
              </div>
            </div>

            <span class="qris-judul" id="judul-aplikasi">Aplikasi yang akan dipakai (opsional)</span>
            <div class="app-daftar" role="radiogroup" aria-labelledby="judul-aplikasi">
              @foreach (config('pembayaran.qris.aplikasi') as $app)
                <label class="pilih-chip">
                  <input type="radio" name="payment_channel" value="{{ $app }}" @checked($metodeDipilih === 'qris' && $channelLama === $app)>
                  <span>{{ $app }}</span>
                </label>
              @endforeach
            </div>
            <p class="bayar-catatan">Buka aplikasi pembayaran apa pun yang mendukung QRIS lalu scan kode di atas. Kirim bukti pembayaran lewat menu <a href="{{ route('chat.index') }}">Chat</a>.</p>
          </fieldset>
        </section>

      </div>

      {{-- Ringkasan pembayaran: paling bawah, selebar halaman --}}
      <section class="ringkas-bawah anim-naik" style="--i:6" aria-labelledby="judul-ringkas">
        <div class="ringkas-kepala">
          <h2 id="judul-ringkas">Ringkasan Pembayaran</h2>
          <span class="ringkas-metode" id="r-metode">COD</span>
        </div>

        <div class="ringkas-isi">
          <div class="ringkasan">
            <div class="baris"><span>Subtotal layanan</span><span id="r-sub">Rp 0</span></div>
            <div class="baris"><span>Biaya jemput/antar</span><span id="r-ongkir">Rp 0</span></div>
            <div class="baris baris-diskon sembunyi" id="r-diskon-baris"><span>Diskon voucher <b id="r-diskon-kode"></b></span><span id="r-diskon">- Rp 0</span></div>
            <div class="baris total"><span>Perkiraan total</span><span id="r-total">Rp 0</span></div>
          </div>

          <div class="ringkas-aksi">
            <p class="bantu">Total akhir dikonfirmasi petugas setelah sepatu diperiksa.</p>
            <button type="submit" class="btn btn-block">Kirim Pesanan</button>
          </div>
        </div>
      </section>
    </div>
  </form>
</div>

{{-- Kotak "Tambah alamat baru". Letaknya di luar #form-pesanan agar form tidak bersarang. --}}
<dialog id="dlg-alamat" class="dlg-alamat" aria-labelledby="judul-dlg-alamat">
  <div class="dlg-kepala">
    <h2 id="judul-dlg-alamat">Tambah Alamat Baru</h2>
    <button type="button" class="link-btn" data-tutup aria-label="Tutup">Tutup</button>
  </div>
  <div class="alert alert-error sembunyi" data-galat role="alert"></div>
  @include('pengguna.alamat._form', [
    'p' => 'dlg', 'alamat' => null, 'aksi' => route('alamat.simpan'),
    'metode' => null, 'tombol' => 'Simpan Alamat', 'ajax' => true,
  ])
</dialog>

{{-- Kotak "Ubah alamat" (diisi lewat JavaScript dari alamat yang dipilih). --}}
<dialog id="dlg-ubah-alamat" class="dlg-alamat" aria-labelledby="judul-dlg-ubah" data-aksi="{{ route('alamat.update', '__ID__') }}">
  <div class="dlg-kepala">
    <h2 id="judul-dlg-ubah">Ubah Alamat</h2>
    <button type="button" class="link-btn" data-tutup aria-label="Tutup">Tutup</button>
  </div>
  <div class="alert alert-error sembunyi" data-galat role="alert"></div>
  @include('pengguna.alamat._form', [
    'p' => 'dlgubah', 'alamat' => null, 'aksi' => '#',
    'metode' => 'PUT', 'tombol' => 'Simpan Perubahan', 'ajax' => true,
  ])
</dialog>

<template id="tpl-item">
  @include('pengguna.pesanan._item', ['i' => '__I__', 'row' => [], 'layanan' => $layanan])
</template>

<script>
(function () {
  const wrap = document.getElementById('daftar-sepatu');
  const tpl = document.getElementById('tpl-item').innerHTML;
  let idx = {{ $mulai }};
  const MAKS = 10;

  const rupiah = n => 'Rp ' + n.toLocaleString('id-ID');

  function nomori() {
    wrap.querySelectorAll('[data-item]').forEach((el, n) => {
      el.querySelector('[data-nomor]').textContent = n + 1;
    });
  }

  const form = document.getElementById('form-pesanan');
  let voucher = null;   // aturan voucher yang sudah dicek server
  let subtotalKini = 0;

  function potongan(sub) {
    if (!voucher || sub < voucher.min_order) return 0;

    let p = voucher.tipe === 'persen'
      ? Math.floor(sub * Math.min(voucher.nilai, 100) / 100)
      : voucher.nilai;

    if (voucher.tipe === 'persen' && voucher.maks != null) p = Math.min(p, voucher.maks);

    return Math.max(0, Math.min(p, sub));
  }

  const elPesan = document.getElementById('voucher-pesan');
  const elAktif = document.getElementById('voucher-aktif');
  const elInput = document.getElementById('voucher-kode');
  const elHidden = document.getElementById('voucher-hidden');

  function pesanVoucher(teks, jenis) {
    elPesan.textContent = teks || '';
    elPesan.className = 'voucher-pesan' + (jenis ? ' ' + jenis : '');
  }

  function hitung() {
    let sub = 0, ongkir = 0;

    wrap.querySelectorAll('.js-layanan').forEach(s => {
      sub += Number(s.selectedOptions[0]?.dataset.price || 0);
    });

    ['jemput', 'antar'].forEach(t => {
      const cek = document.getElementById(t);
      const zona = document.getElementById(t + '_zone');

      if (cek && cek.checked && zona) {
        ongkir += Number(zona.selectedOptions[0]?.dataset.fee || 0);
      }
    });

    subtotalKini = sub;

    // Voucher: syarat minimal belanja ikut berubah saat isi pesanan berubah
    let diskon = 0;
    if (voucher) {
      if (sub < voucher.min_order) {
        pesanVoucher('Voucher ' + voucher.kode + ' belum berlaku: minimal layanan ' + rupiah(voucher.min_order) + '.', 'peringatan');
        elHidden.value = '';
      } else {
        diskon = potongan(sub);
        elHidden.value = voucher.kode;
        pesanVoucher('Voucher dipakai, hemat ' + rupiah(diskon) + '.', 'sukses');
      }
    }

    const total = sub - diskon + ongkir;

    document.getElementById('r-sub').textContent = rupiah(sub);
    document.getElementById('r-ongkir').textContent = rupiah(ongkir);
    document.getElementById('r-diskon').textContent = '- ' + rupiah(diskon);
    document.getElementById('r-diskon-kode').textContent = voucher && diskon > 0 ? '(' + voucher.kode + ')' : '';
    document.getElementById('r-diskon-baris').classList.toggle('sembunyi', diskon <= 0);
    document.getElementById('r-total').textContent = rupiah(total);
    document.getElementById('qris-total').textContent = rupiah(total);
  }

  // ----- Voucher -----
  function pasangVoucher(data) {
    voucher = { kode: data.kode, tipe: data.tipe, nilai: data.nilai, min_order: data.min_order, maks: data.maks };
    document.getElementById('voucher-nama').textContent = data.kode;
    document.getElementById('voucher-ket').textContent = data.keterangan ? data.ringkasan + ' - ' + data.keterangan : data.ringkasan;
    elAktif.classList.remove('sembunyi');
    elInput.value = data.kode;
    hitung();
  }

  function lepasVoucher() {
    voucher = null;
    elHidden.value = '';
    elAktif.classList.add('sembunyi');
    elInput.value = '';
    pesanVoucher('');
    hitung();
  }

  async function cekVoucher(kode, diam) {
    kode = kode.trim().toUpperCase();
    if (!kode) { pesanVoucher('Masukkan kode voucher.', 'galat'); return; }

    const tombol = document.getElementById('voucher-pakai');
    tombol.disabled = true;
    if (!diam) pesanVoucher('Memeriksa voucher...');

    try {
      const res = await fetch(@json(route('pesanan.voucher')), {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ kode: kode, subtotal: subtotalKini }),
      });
      const data = await res.json().catch(() => ({}));

      if (!res.ok || !data.ok) {
        voucher = null;
        elHidden.value = '';
        elAktif.classList.add('sembunyi');
        pesanVoucher(data.pesan || (data.errors && Object.values(data.errors)[0][0]) || 'Voucher tidak bisa dipakai.', 'galat');
        hitung();
        return;
      }

      pasangVoucher(data);
    } catch (e) {
      pesanVoucher('Tidak bisa terhubung ke server. Coba lagi.', 'galat');
    } finally {
      tombol.disabled = false;
    }
  }

  document.getElementById('voucher-pakai').addEventListener('click', () => cekVoucher(elInput.value, false));
  document.getElementById('voucher-hapus').addEventListener('click', lepasVoucher);
  elInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); cekVoucher(elInput.value, false); }
  });

  // ----- Metode pembayaran -----
  function tampilBayar() {
    const pilih = form.querySelector('input[name="payment_method"]:checked');
    const kode = pilih ? pilih.value : 'cod';

    form.querySelectorAll('[data-panel]').forEach(p => {
      const aktif = p.dataset.panel === kode;
      p.classList.toggle('sembunyi', !aktif);
      if (p.tagName === 'FIELDSET') p.disabled = !aktif;
    });

    document.getElementById('r-metode').textContent = pilih ? pilih.dataset.nama : '';
  }

  form.addEventListener('change', e => {
    if (e.target.name === 'payment_method') tampilBayar();
  });

  document.addEventListener('click', e => {
    const t = e.target.closest('[data-salin]');
    if (!t) return;

    e.preventDefault();
    const teks = t.dataset.salin;
    const selesai = () => { t.textContent = 'Tersalin'; setTimeout(() => { t.textContent = 'Salin'; }, 1600); };

    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(teks).then(selesai, () => {});
    } else {
      const ta = document.createElement('textarea');
      ta.value = teks; ta.style.position = 'fixed'; ta.style.opacity = '0';
      document.body.appendChild(ta); ta.select();
      try { document.execCommand('copy'); selesai(); } catch (x) {}
      ta.remove();
    }
  });

  document.getElementById('tambah-sepatu').addEventListener('click', () => {
    if (wrap.querySelectorAll('[data-item]').length >= MAKS) {
      alert('Maksimal ' + MAKS + ' sepatu per pesanan.');
      return;
    }

    wrap.insertAdjacentHTML('beforeend', tpl.replaceAll('__I__', idx++));
    nomori();
    hitung();
  });

  wrap.addEventListener('click', e => {
    if (!e.target.matches('[data-hapus]')) return;

    if (wrap.querySelectorAll('[data-item]').length <= 1) {
      alert('Minimal satu sepatu.');
      return;
    }

    e.target.closest('[data-item]').remove();
    nomori();
    hitung();
  });

  form.addEventListener('change', e => {
    if (e.target.matches('[data-toggle]')) {
      document.getElementById(e.target.dataset.toggle).classList.toggle('sembunyi', !e.target.checked);
    }

    hitung();
  });

  nomori();
  hitung();
  tampilBayar();

  // Kode voucher dari isian sebelumnya (mis. setelah galat validasi)
  if (elInput.value.trim() !== '') cekVoucher(elInput.value, true);
})();
</script>
<script src="{{ asset('js/alamat.js') }}" defer></script>
@endsection