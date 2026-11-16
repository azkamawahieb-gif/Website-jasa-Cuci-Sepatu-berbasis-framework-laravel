@extends('layouts.auth')
@section('title', 'Daftar')

@section('panel')
  <p class="ap-eyebrow anim-naik" style="--i: 3">WELCOME TO</p>
  <h2 class="ap-merek">
    <span class="sr-only">{{ config('app.name') }}</span>
    <span aria-hidden="true">@foreach (mb_str_split(config('app.name')) as $i => $h)<span class="huruf" style="--h: {{ $i }}">{{ $h }}</span>@endforeach</span>
  </h2>
  <p class="ap-tagline anim-naik" style="--i: 5">Care Beyond Clean.</p>
  <span class="ap-garis" aria-hidden="true"></span>
  <p class="ap-teks anim-naik" style="--i: 7">Selamat datang di Cucci.in! Kami hadir untuk memberikan pengalaman perawatan sepatu yang lebih mudah, praktis, dan terpercaya.</p>
  <p class="ap-teks anim-naik" style="--i: 8">Kenali lebih dekat layanan kami dan nikmati kemudahan pemesanan serta pemantauan status pesanan dengan membuat akun sekarang.</p>
  <p class="ap-ajak anim-naik" style="--i: 9">Daftarkan akun Anda dan nikmati kemudahan layanan Cucci.in.</p>
@endsection

@section('alt')
  <span>Sudah punya akun?</span>
  <a href="{{ route('login') }}" class="btn btn-kecil btn-putih">Masuk</a>
@endsection

@section('content')
  <h1 class="anim-naik" style="--i: 0">Buat akun baru</h1>
  <p class="auth-lead anim-naik" style="--i: 1">Silakan lengkapi data diri Anda untuk membuat akun dan mulai menggunakan layanan kami</p>

  <form method="POST" action="{{ route('register') }}" class="auth-form" data-auth-form>
    @csrf

    <div class="fld anim-naik {{ $errors->has('name') ? 'ada-error' : '' }}" style="--i: 2">
      <label for="name">Nama lengkap</label>
      <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
      @error('name')<p class="fld-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="fld anim-naik {{ $errors->has('email') ? 'ada-error' : '' }}" style="--i: 3">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
      @error('email')<p class="fld-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="fld anim-naik {{ $errors->has('phone') ? 'ada-error' : '' }}" style="--i: 4">
      <label for="phone">Nomor HP (WhatsApp)</label>
      <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel"
             autocomplete="tel" placeholder="081234567890" required>
      @error('phone')<p class="fld-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="fld anim-naik {{ $errors->has('password') ? 'ada-error' : '' }}" style="--i: 5">
      <label for="password">Password</label>
      <div class="pw">
        <input type="password" id="password" name="password" autocomplete="new-password" required>
        <button type="button" class="pw-toggle" data-pw-toggle aria-label="Tampilkan password" aria-pressed="false">
          <svg class="ic-on" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg class="ic-off" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.9 17.9A10.9 10.9 0 0 1 12 19C5 19 1 12 1 12a18.5 18.5 0 0 1 5.1-5.9M9.9 5.1A10.4 10.4 0 0 1 12 5c7 0 11 7 11 7a18.6 18.6 0 0 1-2.2 3.2M1 1l22 22"/></svg>
        </button>
      </div>
      <p class="fld-hint">Minimal 8 karakter.</p>
      @error('password')<p class="fld-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="fld anim-naik" style="--i: 6">
      <label for="password_confirmation">Ulangi password</label>
      <div class="pw">
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
        <button type="button" class="pw-toggle" data-pw-toggle aria-label="Tampilkan password" aria-pressed="false">
          <svg class="ic-on" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg class="ic-off" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.9 17.9A10.9 10.9 0 0 1 12 19C5 19 1 12 1 12a18.5 18.5 0 0 1 5.1-5.9M9.9 5.1A10.4 10.4 0 0 1 12 5c7 0 11 7 11 7a18.6 18.6 0 0 1-2.2 3.2M1 1l22 22"/></svg>
        </button>
      </div>
    </div>

    <button type="submit" class="btn btn-block anim-naik" style="--i: 7" data-loading="Memproses...">Daftar</button>
  </form>
@endsection