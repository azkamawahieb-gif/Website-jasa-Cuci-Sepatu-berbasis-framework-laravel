<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Panel Petugas') | {{ config('app.name') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.9.0/dist/css/coreui.min.css">
  @if (file_exists(public_path('css/tema-petugas.css')))
    <link rel="stylesheet" href="{{ asset('css/tema-petugas.css') }}">
  @endif
  <link rel="stylesheet" href="{{ asset('css/petugas-kerangka.css') }}">
  @stack('css')
</head>
<body>
  <a href="#isi-utama" class="lewati">Lewati ke konten</a>

  @include('petugas.partials.sidebar')

  <div class="wrapper d-flex flex-column min-vh-100">
    @include('petugas.partials.header')

    <div class="body flex-grow-1">
      <main id="isi-utama" class="container-fluid px-3 px-lg-4 pb-4 halaman-petugas" tabindex="-1">

        @foreach (['status', 'sukses', 'success'] as $kunci)
          @if (session($kunci))
            <div class="alert alert-success d-flex align-items-center" role="status">{{ session($kunci) }}</div>
          @endif
        @endforeach
        @foreach (['error', 'galat'] as $kunci)
          @if (session($kunci))
            <div class="alert alert-danger d-flex align-items-center" role="alert">{{ session($kunci) }}</div>
          @endif
        @endforeach

        @yield('content')
      </main>
    </div>

    <footer class="footer px-3 px-lg-4 py-3 border-top small text-body-secondary">
      <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
      <span class="ms-auto">Panel Petugas</span>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.9.0/dist/js/coreui.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>