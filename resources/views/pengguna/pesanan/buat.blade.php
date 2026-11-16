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

    <div class="pesan-grid">
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
                                 data-teks="{{ $a->lengkap() }}" data-peta="{{ $a->petaUrl() }}"
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
                               value="{{ $petaAwal }}" placeholder="https://maps.app.goo.gl/..." @readonly(! $manual)>
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
            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') salah @enderror"
                      placeholder="Catatan tambahan untuk petugas (opsional)">{{ old('notes') }}</textarea>
            @error('notes')
              <p class="pesan-salah" role="alert">{{ $message }}</p>
            @enderror
          </div>
        </section>
      </div>

      {{-- Ringkasan biaya --}}
      <aside class="pesan-ringkas anim-naik" style="--i:4" aria-label="Ringkasan biaya">
        <div class="ringkas-kartu">
          <h2>Ringkasan Biaya</h2>
          <div class="ringkasan">
            <div class="baris"><span>Subtotal layanan</span><span id="r-sub">Rp 0</span></div>
            <div class="baris"><span>Biaya jemput/antar</span><span id="r-ongkir">Rp 0</span></div>
            <div class="baris total"><span>Perkiraan total</span><span id="r-total">Rp 0</span></div>
          </div>
          <p class="bantu">
            Total akhir dikonfirmasi petugas. Pembayaran dilakukan saat sepatu diterima kembali (tunai, transfer, atau QRIS).
          </p>
          <button type="submit" class="btn btn-block">Kirim Pesanan</button>
        </div>
      </aside>
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

    document.getElementById('r-sub').textContent = rupiah(sub);
    document.getElementById('r-ongkir').textContent = rupiah(ongkir);
    document.getElementById('r-total').textContent = rupiah(sub + ongkir);
  }

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

  document.getElementById('form-pesanan').addEventListener('change', e => {
    if (e.target.matches('[data-toggle]')) {
      document.getElementById(e.target.dataset.toggle).classList.toggle('sembunyi', !e.target.checked);
    }

    hitung();
  });

  nomori();
  hitung();
})();
</script>
<script src="{{ asset('js/alamat.js') }}" defer></script>
@endsection