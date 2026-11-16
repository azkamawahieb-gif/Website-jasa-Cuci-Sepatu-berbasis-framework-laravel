@extends('layouts.pengguna')
@section('title', 'Alamat Saya')
@push('css')
<link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
@endpush

@section('content')
<div class="area">
    <div class="halaman-judul anim-naik">
    <a href="{{ route('pengaturan.index') }}" class="kembali" aria-label="Kembali ke pengaturan">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
    </a>
    <h1>Alamat Saya</h1>
  </div>

  <div class="alamat-halaman">
    <section class="kartu-area anim-naik" style="--i:1" aria-labelledby="judul-daftar-alamat">
      <h2 id="judul-daftar-alamat">Alamat Tersimpan</h2>
      <p class="sub">Dipakai saat memilih alamat jemput atau antar di form pesanan. Maksimal 10 alamat.</p>

      @forelse ($alamat as $a)
        <article class="alamat-kartu">
          <div class="alamat-isi">
            <div class="alamat-nama">
              <strong>{{ $a->recipient_name }}</strong>
              <span class="muted">| {{ $a->phone }}</span>
              @if ($a->is_default)<span class="alamat-lencana badge-utama">Utama</span>@endif
              @if ($a->label)<span class="alamat-lencana">{{ $a->label }}</span>@endif
            </div>
            <div>{{ $a->address }}@if ($a->detail), {{ $a->detail }}@endif</div>
            @if ($a->wilayah() !== '')<div class="muted">{{ $a->wilayah() }}</div>@endif
            @if ($a->petaUrl())
              <div><a href="{{ $a->petaUrl() }}" target="_blank" rel="noopener noreferrer">Lihat di Google Maps</a></div>
            @endif
          </div>

          <div class="alamat-aksi">
            @unless ($a->is_default)
              <form method="POST" action="{{ route('alamat.utama', $a) }}" class="form-inline">
                @csrf @method('PATCH')
                <button type="submit" class="link-btn">Jadikan utama</button>
              </form>
            @endunless
            <form method="POST" action="{{ route('alamat.hapus', $a) }}" class="form-inline"
                  onsubmit="return confirm('Hapus alamat ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="link-btn teks-bahaya">Hapus</button>
            </form>
          </div>

          <details class="alamat-ubah" @if (old('_form') === 'ubah' . $a->id) open @endif>
            <summary>Ubah alamat</summary>
            @include('pengguna.alamat._form', [
              'p' => 'ubah' . $a->id, 'alamat' => $a, 'aksi' => route('alamat.update', $a),
              'metode' => 'PUT', 'tombol' => 'Simpan Perubahan',
            ])
          </details>
        </article>
      @empty
        <p class="muted">Belum ada alamat tersimpan. Tambahkan di bawah.</p>
      @endforelse
    </section>

    <section class="kartu-area anim-naik" style="--i:2" aria-labelledby="judul-tambah-alamat">
      <h2 id="judul-tambah-alamat">Tambah Alamat Baru</h2>
      @if ($alamat->count() >= 10)
        <p class="muted">Sudah 10 alamat tersimpan. Hapus salah satu untuk menambah yang baru.</p>
      @else
        @include('pengguna.alamat._form', [
          'p' => 'baru', 'alamat' => null, 'aksi' => route('alamat.simpan'),
          'metode' => null, 'tombol' => 'Simpan Alamat',
        ])
      @endif
    </section>
  </div>
</div>

<script src="{{ asset('js/alamat.js') }}" defer></script>
@endsection