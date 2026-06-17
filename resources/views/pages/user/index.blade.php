@extends('layouts.app')

@section('title', 'Jaya Futsal | Reservasi Lapangan')

@section('content')
  <header class="hero-wrap position-relative overflow-hidden">
    <div class="hero-ornaments">
      <span class="hero-ornament ball"></span>
      <span class="hero-ornament arc"></span>
      <span class="hero-ornament goal"></span>
    </div>
    @include('partials.user.landing-navbar')

    <div class="container py-5 hero-content">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <p class="kicker mb-2">Sistem Reservasi Online</p>
          <div class="pitch-steps mb-3">
            <span class="pitch-step">Pilih Jadwal</span>
            <span class="pitch-step">Cek Slot</span>
            <span class="pitch-step">Bayar</span>
            <span class="pitch-step">Main</span>
          </div>
          <h1 class="display-2 hero-title mb-3">
            Main Futsal Jadi Lebih Gampang di Jaya Futsal
          </h1>
          <p class="lead text-dark-70 mb-4">
            Cek slot lapangan real-time, booking dalam hitungan menit, bayar
            langsung, dan pantau riwayat pertandinganmu dari satu dashboard.
          </p>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-accent btn-lg rounded-pill px-4" href="{{ url('/reservasi') }}">
              Mulai Reservasi
            </a>
            <a class="btn btn-outline-dark btn-lg rounded-pill px-4" href="#harga">
              Lihat Harga
            </a>
          </div>
          <div class="row g-3 mt-4 stat-row">
            <div class="col-6 col-md-4">
              <div class="stat-card">
                <h3>1</h3>
                <p>Lapangan Indoor</p>
              </div>
            </div>
            <div class="col-6 col-md-4">
              <div class="stat-card">
                <h3>24/7</h3>
                <p>Dukungan Reservasi</p>
              </div>
            </div>
            <div class="col-6 col-md-4">
              <div class="stat-card">
                <h3>1.2k+</h3>
                <p>Booking Sukses/Bulan</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="hero-visual">
            <img
              class="hero-pitch"
              src="{{ asset('assets/img/futsal-pitch.svg') }}"
              alt="Ilustrasi lapangan futsal"
            />
          </div>
          <div class="hero-panel p-4 p-md-5">
            <h2 class="h4 mb-3">Kenapa Pilih Jaya Futsal?</h2>
            <ul class="list-unstyled mb-0 d-grid gap-3">
              <li class="feature-chip">Tersedia jadwal per jam yang selalu update</li>
              <li class="feature-chip">Pembayaran fleksibel: QRIS</li>
              <li class="feature-chip">Bisa batalkan reservasi sesuai kebijakan</li>
              <li class="feature-chip">Program membership untuk diskon rutin</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main>
    <section id="fitur" class="py-5">
      <div class="container py-4">
        <div class="text-center mb-4">
          <p class="section-kicker mb-2">Keunggulan Kami</p>
          <h2 class="section-title">Fasilitas & Pelayanan Terbaik</h2>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-xl-4">
            <article class="info-card h-100">
              <h3>Rumput Sintetis Premium</h3>
              <p>
                Lapangan kami menggunakan rumput sintetis standar internasional yang aman, tidak licin, dan nyaman digunakan untuk pertandingan intens.
              </p>
            </article>
          </div>
          <div class="col-md-6 col-xl-4">
            <article class="info-card h-100">
              <h3>Fasilitas Lengkap</h3>
              <p>
                Dilengkapi dengan ruang ganti pemain yang luas, toilet bersih, shower, mushola, serta area parkir yang aman untuk kendaraan Anda.
              </p>
            </article>
          </div>
          <div class="col-md-6 col-xl-4">
            <article class="info-card h-100">
              <h3>Kantin & Ruang Tunggu</h3>
              <p>
                Tersedia kantin yang menyajikan berbagai pilihan minuman segar dan makanan ringan, serta area tribun penonton yang nyaman.
              </p>
            </article>
          </div>
          <div class="col-md-6 col-xl-4">
            <article class="info-card h-100">
              <h3>Booking Online 24/7</h3>
              <p>
                Tidak perlu repot datang atau telepon. Cek jadwal kosong secara real-time dan booking lapangan langsung dari HP kamu kapan saja.
              </p>
            </article>
          </div>
          <div class="col-md-6 col-xl-4">
            <article class="info-card h-100">
              <h3>Pembayaran Fleksibel</h3>
              <p>
                Mendukung pembayaran dengan QRIS.
              </p>
            </article>
          </div>
          <div class="col-md-6 col-xl-4">
            <article class="info-card h-100">
              <h3>Program Membership</h3>
              <p>
                Dapatkan harga spesial, diskon rutinn, dan prioritas booking pada jam-jam prime time dengan mendaftar sebagai member Jaya Futsal.
              </p>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section id="harga" class="py-5 band-section">
      <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
          <div>
            <p class="section-kicker mb-2">Melihat Jadwal & Harga</p>
            <h2 class="section-title mb-0">Harga Sewa Lapangan</h2>
          </div>
          <a href="{{ url('/reservasi') }}#step-1" class="btn btn-dark rounded-pill px-4">
            Pilih Jadwal
          </a>
        </div>
        <div class="table-responsive">
          <table class="table table-pricing align-middle">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Jam Operasional</th>
                <th>Harga Sewa per Jam</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong><i class="bi bi-sun"></i> Pagi / Siang</strong></td>
                <td>07:00 - 17:00</td>
                <td><strong>Rp60.000</strong> / jam</td>
              </tr>
              <tr>
                <td><strong><i class="bi bi-moon-stars"></i> Malam</strong></td>
                <td>17:00 - 23:00</td>
                <td><strong>Rp70.000</strong> / jam</td>
              </tr>
            </tbody>
          </table>
          <div class="alert alert-info border-0 py-2 small">
            <i class="bi bi-info-circle"></i> <strong>Member Aktif</strong> mendapatkan <strong>FREE 1 jam bermain</strong> (voucher sekali pakai) selama masa membership.
          </div>
        </div>
      </div>
    </section>

    <section id="membership" class="py-5">
      <div class="container py-4">
        <div class="row g-4 align-items-center">
          <div class="col-lg-7">
            <p class="section-kicker mb-2">Daftar Membership</p>
            <h2 class="section-title">Member Jaya Futsal</h2>
            <p class="text-dark-70">
              Daftar sebagai member Jaya Futsal dengan biaya Rp150.000 dan dapatkan keuntungan eksklusif
              berupa <strong>free 1 jam bermain</strong> (voucher sekali pakai) selama masa membership 3 bulan.
              Jika dalam 3 bulan tidak pernah melakukan booking, membership akan otomatis hangus.
            </p>
          </div>
          <div class="col-lg-5">
            <div class="membership-card">
              <h3 class="h5">Member Jaya Futsal</h3>
              <p class="display-6 fw-bold mb-2">
                Rp150.000 <span class="fs-6 fw-normal">/ 3 bulan</span>
              </p>
              <ul class="list-unstyled d-grid gap-2 mb-3">
                <li><i class="bi bi-check-circle-fill text-success"></i> Free 1 jam bermain (sekali pakai)</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Prioritas konfirmasi booking</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Masa aktif 3 bulan</li>
              </ul>
              <a href="{{ url('/reservasi') }}#membership-form" class="btn btn-accent w-100 rounded-pill">
                Daftar Sekarang
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('partials.user.footer')
@endsection
