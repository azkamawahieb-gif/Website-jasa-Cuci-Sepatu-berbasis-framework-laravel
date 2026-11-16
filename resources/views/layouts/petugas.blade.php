<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') - Petugas | {{ config('app.name') }}</title>
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.9.0/dist/css/coreui.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="{{ asset('css/tema-petugas.css') }}">
  <style>
  /* Sidebar lebih ramping */
  .sidebar { --cui-sidebar-width: 13.5rem; }

  @media (min-width: 992px) {
    .sidebar.sidebar-fixed:not(.hide) ~ .wrapper { padding-inline-start: 13.5rem !important; }
  }

  /* Header sidebar */
  .sidebar-header { min-height: 3.5rem; padding: 0 1rem; }
  .sidebar-brand { font-size: 1rem; }

  /* Menu lebih padat dan rapi */
  .sidebar-nav .nav-link {
    display: flex; align-items: center;
    padding: .55rem 1rem; font-size: .9rem;
  }
  .sidebar-nav .nav-title {
    padding: 1rem 1rem .35rem;
    font-size: .68rem; letter-spacing: .06em;
  }
  .menu-ico {
    width: 1.1rem; height: 1.1rem; margin-right: .7rem; flex-shrink: 0;
    fill: none; stroke: currentColor; stroke-width: 2;
    stroke-linecap: round; stroke-linejoin: round;
  }
</style>
</head>
<body>
  @include('petugas.partials.sidebar')

  <div class="wrapper d-flex flex-column min-vh-100">
    @include('petugas.partials.header')

    <div class="body flex-grow-1">
      <div class="container-lg px-4">
        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </div>
    </div>

    <footer class="footer px-4">
      <div>{{ config('app.name') }} &copy; {{ date('Y') }}</div>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.9.0/dist/js/coreui.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>