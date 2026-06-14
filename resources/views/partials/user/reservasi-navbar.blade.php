<nav class="navbar navbar-expand-lg py-3 border-bottom bg-white sticky-top">
  <div class="container">
    <a class="navbar-brand brand-title" href="{{ url('/') }}">JAYA FUTSAL</a>
    <button
      class="navbar-toggler border-0"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#dashNav"
      aria-controls="dashNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="dashNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        {{-- <li class="nav-item">
          <a class="btn btn-outline-dark rounded-pill px-3" href="{{ url('/login') }}">Login</a>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-dark rounded-pill px-3" href="{{ url('/register') }}">Register</a>
        </li> --}}
        <li class="nav-item">
          <a class="nav-link" href="{{ route('reservasi.index') }}#step-1">Reservasi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('reservasi.riwayat-lengkap') }}">Riwayat</a>
        </li>
        <li class="nav-item d-flex align-items-center ms-lg-3 gap-2">
          <a class="nav-link p-0 d-flex align-items-center gap-2" href="{{ route('profile') }}" title="Lihat Profil & Membership">
            <img src="{{ asset('assets/img/user-avatar.png') }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama ?? 'User') }}&background=0D8ABC&color=fff'" alt="Profile" class="rounded-circle" width="36" height="36" style="object-fit: cover; border: 2px solid #ddd;">
            <span class="d-none d-lg-inline fw-semibold text-dark">{{ Auth::user()->nama ?? 'Profil Saya' }}</span>
          </a>
          <button class="btn btn-outline-danger btn-sm rounded-pill ms-2" type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</button>
        </li>
      </ul>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
      </form>
    </div>
  </div>
</nav>
