<nav class="navbar navbar-expand-lg py-3 landing-navbar">
  <div class="container">
    <a class="navbar-brand brand-title" href="{{ url('/') }}">JAYA FUTSAL</a>
    <button
      class="navbar-toggler border-0"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#mainNav"
      aria-controls="mainNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul
        class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2 landing-menu"
      >
        <li class="nav-item">
          <a class="nav-link landing-link" href="#fitur">Fasilitas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link landing-link" href="#harga">Jadwal & Harga</a>
        </li>
        <li class="nav-item">
          <a class="nav-link landing-link" href="#membership">Membership</a>
        </li>
        <li class="nav-item">
          <a class="btn rounded-pill px-3 btn-topbar-secondary" href="{{ url('/login') }}">Login</a>
        </li>
        <li class="nav-item">
          <a class="btn rounded-pill px-3 btn-topbar-secondary" href="{{ url('/register') }}">Register</a>
        </li>
        <li class="nav-item">
          <a class="btn btn-accent rounded-pill px-3" href="{{ url('/reservasi') }}">Reservasi Sekarang</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
