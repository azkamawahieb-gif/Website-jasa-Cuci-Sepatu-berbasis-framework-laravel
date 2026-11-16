<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Masuk') - {{ config('app.name') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">

  <link rel="stylesheet" href="{{ asset('css/tema.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <script>document.documentElement.classList.add('js')</script>
</head>
<body class="auth-body">
  <div class="auth-bg" aria-hidden="true"><span class="blob b1"></span><span class="blob b2"></span></div>

  <main class="auth-wrap">
    <div class="auth-shell">

      <section class="auth-form-side">
        <div class="auth-top">
          <a href="{{ route('beranda') }}" class="auth-brand">
            <span class="auth-logo">@include('auth._sepatu')</span>
            <span>
              <span class="auth-brand-nama">{{ config('app.name') }}</span>
              <span class="auth-brand-sub">Care Beyond Clean</span>
            </span>
          </a>
        </div>

        <div class="auth-main">
          @yield('content')
          <div class="auth-switch">@yield('alt')</div>
        </div>

        <div class="auth-bottom">
          <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
          <a href="{{ route('beranda') }}">&larr; Kembali ke beranda</a>
        </div>
      </section>

      @include('auth._panel')
    </div>
  </main>

  <script src="{{ asset('js/auth.js') }}" defer></script>
</body>
</html>