@php
  $nama = auth()->user()->name ?? 'Petugas';
  $inisial = mb_strtoupper(collect(preg_split('/\s+/', trim($nama)))->take(2)->map(fn ($k) => mb_substr($k, 0, 1))->implode(''));
@endphp

<header class="header header-sticky mb-4">
  <div class="container-fluid px-3 px-lg-4">
    <button class="header-toggler" type="button" aria-label="Buka atau tutup menu"
            onclick="coreui.Sidebar.getOrCreateInstance(document.querySelector('#sidebar')).toggle()">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>

    <nav aria-label="breadcrumb" class="d-none d-md-block ms-2">
      <ol class="breadcrumb my-0">
        <li class="breadcrumb-item">Panel Petugas</li>
        <li class="breadcrumb-item active" aria-current="page">@yield('title', 'Beranda')</li>
      </ol>
    </nav>

    <ul class="header-nav ms-auto">
      <li class="nav-item dropdown">
        <a class="nav-link d-flex align-items-center gap-2 py-0 pe-0" data-coreui-toggle="dropdown"
           href="#" role="button" aria-haspopup="true" aria-expanded="false" aria-label="Menu akun {{ $nama }}">
          <span class="avatar-inisial" aria-hidden="true">{{ $inisial }}</span>
          <span class="d-none d-md-block fw-semibold">{{ $nama }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-end pt-0">
          <div class="dropdown-header bg-body-tertiary fw-semibold py-2">Akun</div>
          <a class="dropdown-item" href="{{ route('petugas.akun') }}">Akun saya</a>
          <a class="dropdown-item" href="{{ route('beranda') }}" target="_blank" rel="noopener">Lihat website</a>
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">Keluar</button>
          </form>
        </div>
      </li>
    </ul>
  </div>
</header>