{{-- Kepala halaman + tiga tab. Dipakai oleh profil, alamat, dan pesanan saya. --}}
<div class="kepala-halaman anim-naik">
  <div class="profil-kepala">
    <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
    <div>
      <h1>Profil</h1>
      <p>{{ auth()->user()->name }} &middot; {{ auth()->user()->email }}</p>
    </div>
  </div>
</div>

<nav class="tabs" aria-label="Menu profil">
  <a href="{{ route('profil') }}" class="{{ request()->routeIs('profil') ? 'aktif' : '' }}" @if (request()->routeIs('profil')) aria-current="page" @endif>Data Profil</a>
  <a href="{{ route('alamat.index') }}" class="{{ request()->routeIs('alamat.*') ? 'aktif' : '' }}" @if (request()->routeIs('alamat.*')) aria-current="page" @endif>Alamat Saya</a>
  <a href="{{ route('pesanan.index') }}" class="{{ request()->routeIs('pesanan.index', 'pesanan.show') ? 'aktif' : '' }}" @if (request()->routeIs('pesanan.index', 'pesanan.show')) aria-current="page" @endif>Pesanan Saya</a>
</nav>