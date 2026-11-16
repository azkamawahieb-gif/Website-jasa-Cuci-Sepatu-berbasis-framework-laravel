@php
  $menu = [
    ['judul' => null, 'item' => [
      ['Dashboard', 'petugas.dashboard', 'petugas.dashboard',
        '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
    ]],
    ['judul' => 'Operasional', 'item' => [
      ['Pesanan', 'petugas.pesanan.index', 'petugas.pesanan.*',
        '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>'],
      ['Jemput & Antar', 'petugas.pengantaran.index', 'petugas.pengantaran.*',
        '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>'],
    ]],
    ['judul' => 'Data Master', 'item' => [
      ['Layanan', 'petugas.layanan.index', 'petugas.layanan.*',
        '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>'],
      ['Zona Antar', 'petugas.zona.index', 'petugas.zona.*',
        '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>'],
      ['Pengguna', 'petugas.pengguna.index', 'petugas.pengguna.*',
        '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
    ]],
  ];
@endphp

<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
  <div class="sidebar-header border-bottom">
    <div class="sidebar-brand fw-bold">{{ config('app.name') }}</div>
    <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Tutup"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
  </div>

  <ul class="sidebar-nav">
    @foreach ($menu as $grup)
      @if ($grup['judul'])
        <li class="nav-title">{{ $grup['judul'] }}</li>
      @endif

      @foreach ($grup['item'] as [$label, $rute, $pola, $ikon])
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs($pola) ? 'active' : '' }}"
             href="{{ $rute ? route($rute) : '#' }}">
            <svg class="menu-ico" viewBox="0 0 24 24" aria-hidden="true">{!! $ikon !!}</svg>
            {{ $label }}
          </a>
        </li>
      @endforeach
    @endforeach
  </ul>
</div>