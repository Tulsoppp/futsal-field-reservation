@extends('layouts.admin')

@section('title', 'Admin Dashboard | Jaya Futsal')

@section('content')
  @include('partials.admin.header', [
    'title' => 'Dashboard Admin Jaya Futsal',
    'active' => 'dashboard',
    'showWebsiteLink' => true,
  ])

  <main class="py-4 py-lg-5">
    <div class="container">
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card border-left-primary shadow-sm bg-white p-4 rounded-3 text-center">
            <h6 class="text-secondary mb-2">Reservasi Hari Ini</h6>
            <h2 class="mb-0 fw-bold text-dark">{{ $totalReservasiHariIni }} <small class="text-muted fs-6">Sesi</small></h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card border-left-warning shadow-sm bg-white p-4 rounded-3 text-center">
            <h6 class="text-secondary mb-2">Menunggu Pembayaran</h6>
            <h2 class="mb-0 fw-bold text-warning">{{ $menungguPembayaran }}</h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card border-left-success shadow-sm bg-white p-4 rounded-3 text-center">
            <h6 class="text-secondary mb-2">Member Aktif</h6>
            <h2 class="mb-0 fw-bold text-success">{{ $totalMemberAktif }} <small class="text-muted fs-6">Orang</small></h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card border-left-info shadow-sm bg-white p-4 rounded-3 text-center">
            <h6 class="text-secondary mb-2">Pendapatan Bulan Ini</h6>
            <h2 class="mb-0 fw-bold text-primary">Rp{{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}</h2>
          </article>
        </div>
      </div>

      <section class="panel-box bg-white p-4 rounded-3 border shadow-sm mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <h3 class="h5 mb-0">Aktivitas Reservasi Terbaru</h3>
          <a class="btn btn-sm btn-outline-dark" href="{{ url('/admin/reservasi') }}">
            Buka Kelola Reservasi
          </a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Pemesan</th>
                <th>Tanggal Main</th>
                <th>Jam</th>
                <th>Total Harga</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentActivities as $activity)
                <tr>
                  <td>
                    <span class="fw-bold">{{ $activity->user->nama ?? 'User' }}</span>
                  </td>
                  <td>{{ \Carbon\Carbon::parse($activity->tanggal)->format('d M Y') }}</td>
                  <td><span class="badge bg-secondary">{{ substr($activity->jam_mulai, 0, 5) }} - {{ substr($activity->jam_selesai, 0, 5) }}</span></td>
                  <td>Rp{{ number_format($activity->total_harga, 0, ',', '.') }}</td>
                  <td>
                    @if ($activity->status === 'menunggu')
                      <span class="badge bg-warning text-dark">Menunggu</span>
                    @elseif ($activity->status === 'dibayar')
                      <span class="badge bg-success">Berhasil/Dibayar</span>
                    @elseif ($activity->status === 'dibatalkan')
                      <span class="badge bg-danger">Dibatalkan</span>
                    @else
                      <span class="badge bg-info">{{ ucfirst($activity->status) }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">Belum ada aktivitas terbaru.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
@endsection
