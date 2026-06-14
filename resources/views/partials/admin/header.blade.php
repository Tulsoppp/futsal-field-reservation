@php
  $active = $active ?? '';
  $title = $title ?? 'Panel Admin';
  $showWebsiteLink = $showWebsiteLink ?? false;
@endphp

<header class="admin-header border-bottom bg-white sticky-top mt-2">
  <div class="container py-3">
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
      <div>
        <p class="section-kicker mb-1">Panel Admin</p>
        <h1 class="h3 mb-0">{{ $title }}</h1>
      </div>
      <div class="d-flex gap-2">
        @if ($showWebsiteLink)
          <a href="{{ url('/') }}" class="btn btn-outline-dark rounded-pill px-3">
            Website User
          </a>
        @endif
        <a href="{{ route('logout') }}" class="btn btn-dark rounded-pill px-3" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">Logout</a>
        <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </div>
    </div>
    <nav class="admin-nav mt-3">
      <a class="admin-nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">Dashboard</a>
      <a class="admin-nav-link {{ $active === 'reservasi' ? 'active' : '' }}" href="{{ url('/admin/reservasi') }}">Kelola Reservasi</a>
      <a class="admin-nav-link {{ $active === 'laporan' ? 'active' : '' }}" href="{{ url('/admin/laporan') }}">Laporan</a>
      <a class="admin-nav-link {{ $active === 'membership' ? 'active' : '' }}" href="{{ url('/admin/membership') }}">Data Membership</a>
      <a class="admin-nav-link {{ $active === 'pelanggan' ? 'active' : '' }}" href="{{ url('/admin/pelanggan') }}">Daftar Pelanggan</a>
    </nav>
  </div>
</header>
