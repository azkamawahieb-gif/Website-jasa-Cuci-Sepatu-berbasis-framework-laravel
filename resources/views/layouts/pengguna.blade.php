<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">

  <link rel="stylesheet" href="{{ asset('css/pengguna.css') }}">
  <link rel="stylesheet" href="{{ asset('css/tema.css') }}">
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  @stack('css')
  <script>document.documentElement.classList.add('js')</script>
</head>
<body>
  <header class="header">
    <nav class="container nav" aria-label="Navigasi utama">
      <a href="{{ route('beranda') }}" class="brand">
        <span class="brand-logo" aria-hidden="true">@include('auth._sepatu')</span>
        <span>{{ config('app.name') }}</span>
      </a>

      <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="menu-utama" aria-label="Buka menu">
        <span class="garis-menu"></span>
      </button>

      <div class="nav-menu" id="menu-utama">
        <div class="nav-links nav-tengah">
          <a href="{{ route('beranda') }}">Beranda</a>
          <a href="{{ route('beranda') }}#layanan">Layanan</a>
          <a href="{{ route('beranda') }}#antar">Jemput dan Antar</a>
          <a href="{{ route('beranda') }}#cara-kerja">Cara Kerja</a>
          <a href="{{ route('beranda') }}#faq">FAQ</a>
        </div>

        <div class="nav-links nav-kanan">
          @guest
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-kecil">Daftar</a>
          @endguest

          @auth
            @if (auth()->user()->isPetugas())
              <a href="{{ route('petugas.dashboard') }}">Dashboard Petugas</a>
            @else
              <a href="{{ route('pesanan.buat') }}" class="btn btn-kecil">Pesan</a>
              <a href="{{ route('pesanan.index') }}">Pesanan Saya</a>
              <a href="{{ route('profil') }}">Profil</a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="form-inline">
              @csrf
              <button type="submit" class="link-btn">Keluar</button>
            </form>
          @endauth
        </div>
      </div>
    </nav>
  </header>

  @yield('hero')

  <main class="container">
    @if (session('success'))
      <div class="alert alert-sukses">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-error">
        <ul style="margin-left:1.2rem">
          @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </main>

  <footer class="footer">
    <div class="container footer-isi">
      <div class="footer-merek">
        <strong>{{ config('app.name') }}</strong>
        <p>Care Beyond Clean. Perawatan lebih dari sekadar kebersihan.</p>
      </div>
      <nav class="footer-link" aria-label="Tautan footer">
        <a href="{{ route('beranda') }}#layanan">Layanan</a>
        <a href="{{ route('beranda') }}#antar">Jemput dan Antar</a>
        <a href="{{ route('beranda') }}#cara-kerja">Cara Kerja</a>
        <a href="{{ route('beranda') }}#faq">FAQ</a>
      </nav>
    </div>
    <div class="container footer-bawah">&copy; {{ date('Y') }} {{ config('app.name') }}</div>
  </footer>

  <script src="{{ asset('js/tema.js') }}" defer></script>
</body>
</html>