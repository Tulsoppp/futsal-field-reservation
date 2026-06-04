@extends('layouts.admin')

@section('title', 'Laporan Penyewaan & Keuangan | Admin Jaya Futsal')

@section('content')
  @include('partials.admin.header', [
    'title' => 'Laporan Penyewaan & Keuangan',
    'active' => 'laporan',
  ])

  <main class="py-4 py-lg-5">
    <div class="container">
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card">
            <p>Total Pendapatan</p>
            <h2 id="lapTotalPendapatan">Rp{{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card">
            <p>Total Transaksi Lunas</p>
            <h2 id="lapTotalTransaksi">{{ $totalTransaksi ?? 0 }}</h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card">
            <p>Total Dibatalkan</p>
            <h2 id="lapTotalBatal">{{ $totalBatal ?? 0 }}</h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card">
            <p>Rata-rata Transaksi</p>
            <h2 id="lapRataRata">Rp{{ number_format($rataRata ?? 0, 0, ',', '.') }}</h2>
          </article>
        </div>
      </div>

      <section class="panel-box mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h2 class="h5 mb-1">Filter Laporan</h2>
            <p class="text-secondary small mb-0">Gunakan filter untuk melihat data spesifik.</p>
          </div>
          <a href="{{ route('admin.laporan.export', request()->query()) }}" class="btn btn-sm btn-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export Excel (CSV)
          </a>
        </div>
        <form class="row g-2 align-items-end" method="GET" id="filterForm">
          <div class="col-md-3 col-6">
            <label class="form-label small text-secondary" for="filterBulan">Bulan</label>
            <select class="form-select form-select-sm" id="filterBulan" name="bulan" onchange="this.form.submit()">
              <option value="">Semua Bulan</option>
              @foreach(($availableMonths ?? []) as $bulan)
                @php
                  $label = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y');
                @endphp
                <option value="{{ $bulan }}" {{ ($bulanFilter ?? '') === $bulan ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2 col-6">
            <label class="form-label small text-secondary" for="filterStatus">Status</label>
            <select class="form-select form-select-sm" id="filterStatus" name="status" onchange="this.form.submit()">
              <option value="">Semua Status</option>
              <option value="disetujui" {{ ($statusFilter ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
              <option value="selesai" {{ ($statusFilter ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
              <option value="dibatalkan" {{ ($statusFilter ?? '') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
          </div>
          <div class="col-md-3 col-6">
            <label class="form-label small text-secondary" for="filterDari">Tanggal Dari</label>
            <input class="form-control form-control-sm" type="date" id="filterDari" name="tanggal_dari" value="{{ $tanggalDari ?? '' }}" onchange="this.form.submit()">
          </div>
          <div class="col-md-3 col-6">
            <label class="form-label small text-secondary" for="filterSampai">Tanggal Sampai</label>
            <input class="form-control form-control-sm" type="date" id="filterSampai" name="tanggal_sampai" value="{{ $tanggalSampai ?? '' }}" onchange="this.form.submit()">
          </div>
          <div class="col-md-1 col-12">
            <a href="{{ route('admin.laporan') }}" class="btn btn-sm btn-outline-dark w-100" title="Reset Filter">Reset</a>
          </div>
        </form>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <span class="badge text-bg-dark">Filter: {{ $filterLabel ?? 'Semua Data' }}</span>
          <span class="badge text-bg-success">Pendapatan: Rp{{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</span>
          <span class="badge text-bg-primary">Transaksi: {{ $totalTransaksi ?? 0 }}</span>
          <span class="badge text-bg-danger">Batal: {{ $totalBatal ?? 0 }}</span>
        </div>
      </section>

      <section class="panel-box mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <h2 class="h5 mb-0">Laporan Penyewaan</h2>
          <span class="badge text-bg-dark">Data Operasional</span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tim</th>
                <th>Lapangan</th>
                <th>Durasi</th>
                <th>Total Bayar</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="laporanSewaBody">
              @forelse($laporanSewa ?? [] as $row)
                <tr>
                  <td>#RSV-{{ str_pad($row->id, 4, '0', STR_PAD_LEFT) }}</td>
                  <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                  <td>{{ $row->user->nama ?? '-' }}</td>
                  <td>Lapangan Utama</td>
                  <td>{{ \Carbon\Carbon::parse($row->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($row->jam_selesai)->format('H:i') }}</td>
                  <td>Rp{{ number_format($row->total_harga, 0, ',', '.') }}</td>
                  <td>
                    @if(in_array($row->status, ['disetujui', 'dibayar']))
                      <span class="badge text-bg-success">Disetujui</span>
                    @elseif($row->status === 'selesai')
                      <span class="badge text-bg-primary">Selesai</span>
                    @elseif($row->status === 'dibatalkan')
                      <span class="badge text-bg-danger">Dibatalkan</span>
                    @else
                      <span class="badge text-bg-secondary">{{ ucfirst($row->status) }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-secondary py-4">Tidak ada data yang tersedia.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($laporanSewa->hasPages())
          <div class="d-flex justify-content-center mt-3">
            {{ $laporanSewa->links() }}
          </div>
        @endif
      </section>

      <section class="panel-box">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <h2 class="h5 mb-0">Laporan Keuangan Bulanan</h2>
          <span class="badge text-bg-secondary">Ringkasan</span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Bulan</th>
                <th>Transaksi Lunas</th>
                <th>Reservasi Batal</th>
                <th>Pendapatan</th>
              </tr>
            </thead>
            <tbody id="laporanKeuanganBody">
              @forelse($laporanBulanan ?? [] as $row)
                <tr>
                  <td>{{ $row['label'] }}</td>
                  <td>{{ $row['transaksi_lunas'] }}</td>
                  <td>{{ $row['reservasi_batal'] }}</td>
                  <td>Rp{{ number_format($row['pendapatan'], 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-secondary py-4">Tidak ada data yang tersedia.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
@endsection
