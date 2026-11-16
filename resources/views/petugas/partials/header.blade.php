
<header class="header header-sticky p-0 mb-4">
  <div class="container-fluid border-bottom px-4">
    <button class="header-toggler" type="button"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
            style="margin-inline-start: -14px;">
      <span style="font-size: 1.5rem;">&#9776;</span>
    </button>

    <ul class="header-nav ms-auto">
      <li class="nav-item dropdown">
        <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button"
           aria-haspopup="true" aria-expanded="false">
          {{ auth()->user()->name ?? 'Petugas' }}
        </a>
        <div class="dropdown-menu dropdown-menu-end pt-0">
          <div class="dropdown-header bg-body-tertiary fw-semibold my-2">Akun</div>
          <a class="dropdown-item" href="{{ route('petugas.akun') }}">Akun Saya</a>
          <a class="dropdown-item" href="{{ url('/') }}">Lihat Situs</a>
          <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">Keluar</button>
          </form>
        </div>
      </li>
    </ul>
  </div>
</header>