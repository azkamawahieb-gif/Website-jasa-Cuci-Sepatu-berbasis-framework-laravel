@extends('layouts.auth')
@section('title', 'Masuk')

@section('panel')
  <h2 class="ap-merek">
    <span class="sr-only">{{ config('app.name') }}</span>
    <span aria-hidden="true">@foreach (mb_str_split(config('app.name')) as $i => $h)<span class="huruf" style="--h: {{ $i }}">{{ $h }}</span>@endforeach</span>
  </h2>
  <p class="ap-tagline anim-naik" style="--i: 5">Care Beyond Clean.</p>
  <p class="ap-subtag anim-naik" style="--i: 6">Perawatan lebih dari sekadar kebersihan.</p>
  <span class="ap-garis" aria-hidden="true"></span>
  <p class="ap-judul anim-naik" style="--i: 8">Senang melihat Anda kembali!</p>
  <p class="ap-teks anim-naik" style="--i: 9">Masuk ke akun Anda untuk melanjutkan pemesanan, memantau status perawatan, dan menikmati kemudahan layanan Cucci.in.</p>
@endsection

@section('alt')
  <span>Belum punya akun?</span>
  <a href="{{ route('register') }}" class="btn btn-kecil btn-putih">Daftar</a>
@endsection

@section('content')
  <h1 class="anim-naik" style="--i: 0">Selamat Datang Kembali!</h1>
  <p class="auth-lead anim-naik" style="--i: 1">Akses akun Anda dan lanjutkan pengalaman perawatan sepatu bersama kami.</p>

  @if (session('success'))
    <div class="auth-alert auth-alert-ok">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}" class="auth-form" data-auth-form>
    @csrf

    <div class="fld anim-naik {{ $errors->has('email') ? 'ada-error' : '' }}" style="--i: 2">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}"
             autocomplete="email" required autofocus>
      @error('email')<p class="fld-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="fld anim-naik {{ $errors->has('password') ? 'ada-error' : '' }}" style="--i: 3">
      <label for="password">Password</label>
      <div class="pw">
        <input type="password" id="password" name="password" autocomplete="current-password" required>
        <button type="button" class="pw-toggle" data-pw-toggle aria-label="Tampilkan password" aria-pressed="false">
          <svg class="ic-on" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg class="ic-off" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.9 17.9A10.9 10.9 0 0 1 12 19C5 19 1 12 1 12a18.5 18.5 0 0 1 5.1-5.9M9.9 5.1A10.4 10.4 0 0 1 12 5c7 0 11 7 11 7a18.6 18.6 0 0 1-2.2 3.2M1 1l22 22"/></svg>
        </button>
      </div>
      @error('password')<p class="fld-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <label class="auth-cek anim-naik" style="--i: 4">
      <input type="checkbox" name="remember" value="1"> Ingat saya
    </label>

    <button type="submit" class="btn btn-block anim-naik" style="--i: 5" data-loading="Memproses...">Masuk</button>
  </form>
@endsection