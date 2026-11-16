@extends('layouts.pengguna')
@section('title', 'Akun Saya')
@push('css')
<link rel="stylesheet" href="{{ asset('css/pengguna-area.css') }}">
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
@endpush

@section('content')
@php $user = auth()->user(); @endphp
<div class="profil-halaman">

  <div class="halaman-judul anim-naik">
    <a href="{{ route('pengaturan.index') }}" class="kembali" aria-label="Kembali ke pengaturan">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
    </a>
    <h1>Akun Saya</h1>
  </div>

  <section class="kartu-area anim-naik" style="--i:1" aria-labelledby="judul-data">
    <h2 id="judul-data">Data Diri</h2>
    <p class="sub">Dipakai untuk menghubungi Anda lewat WhatsApp saat sepatu siap.</p>

    <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data">
      @csrf @method('PUT')

      <div class="avatar-edit">
        <span class="profil-avatar profil-avatar-kecil">
          @if ($user->avatarUrl())
            <img src="{{ $user->avatarUrl() }}" alt="Foto profil saat ini">
          @else
            <span aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
          @endif
        </span>
        <div class="avatar-isi">
          <label for="avatar">Foto profil (maks 2 MB)</label>
          <input type="file" id="avatar" name="avatar" accept="image/*"
                 class="form-control @error('avatar') salah @enderror">
          @error('avatar')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
          @if ($user->avatar)
            <label class="cek">
              <input type="checkbox" name="hapus_avatar" value="1">
              <span>Hapus foto profil</span>
            </label>
          @endif
        </div>
      </div>

      <div class="form-group">
        <label for="name">Nama lengkap</label>
        <input type="text" id="name" name="name" class="form-control @error('name') salah @enderror"
               value="{{ old('name', $user->name) }}" required autocomplete="name">
        @error('name')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
      </div>

      <div class="form-group">
        <label for="bio">Bio (maks 160 karakter)</label>
        <textarea id="bio" name="bio" rows="2" maxlength="160" class="form-control @error('bio') salah @enderror"
                  placeholder="Contoh: Pecinta sneakers putih">{{ old('bio', $user->bio) }}</textarea>
        @error('bio')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') salah @enderror"
               value="{{ old('email', $user->email) }}" required autocomplete="email">
        @error('email')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
      </div>

      <div class="form-group">
        <label for="phone">Nomor HP (WhatsApp)</label>
        <input type="text" id="phone" name="phone" inputmode="tel" class="form-control @error('phone') salah @enderror"
               value="{{ old('phone', $user->phone) }}" required autocomplete="tel">
        @error('phone')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
      </div>

      <div class="form-group">
        <label for="address">Alamat</label>
        <textarea id="address" name="address" rows="3" class="form-control @error('address') salah @enderror"
                  autocomplete="street-address">{{ old('address', $user->address) }}</textarea>
        @error('address')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
      </div>

      <button type="submit" class="btn">Simpan Perubahan</button>
    </form>
  </section>

  <section class="kartu-area anim-naik" style="--i:2" aria-labelledby="judul-password">
    <h2 id="judul-password">Ubah Password</h2>
    <p class="sub">Gunakan minimal 8 karakter.</p>

    <form method="POST" action="{{ route('profil.password') }}">
      @csrf @method('PUT')

      <div class="form-group">
        <label for="current_password">Password lama</label>
        <input type="password" id="current_password" name="current_password"
               class="form-control @error('current_password') salah @enderror" required autocomplete="current-password">
        @error('current_password')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
      </div>

      <div class="form-group">
        <label for="password">Password baru</label>
        <input type="password" id="password" name="password" class="form-control @error('password') salah @enderror"
               required autocomplete="new-password">
        @error('password')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
      </div>

      <div class="form-group">
        <label for="password_confirmation">Ulangi password baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
               required autocomplete="new-password">
      </div>

      <button type="submit" class="btn">Ubah Password</button>
    </form>
  </section>
</div>
@endsection