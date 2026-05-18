@extends('layouts.admin')

@section('title', 'Kelola Reservasi | Admin Jaya Futsal')

@section('content')
  @include('partials.admin.header', [
    'title' => 'Kelola Reservasi',
    'active' => 'reservasi',
  ])

  <main class="py-4 py-lg-5">
    <div class="container">
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card">
            <p>Menunggu Dikonfirmasi</p>
            <h2 id="rvMenungguBayar">{{ $countMenungguBayar ?? 0 }}</h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card">
            <p>Validasi / Menunggu Batal</p>
            <h2 id="rvMenungguBatal">{{ $countMenungguBatal ?? 0 }}</h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card">
            <p>Reservasi Disetujui</p>
            <h2 id="rvDisetujui">{{ $countDisetujui ?? 0 }}</h2>
          </article>
        </div>
        <div class="col-6 col-lg-3">
          <article class="admin-stat-card">
            <p>Reservasi Selesai</p>
            <h2 id="rvSelesai">{{ $countSelesai ?? 0 }}</h2>
          </article>
        </div>
      </div>

      <!-- Jika ada pesan success / error -->
      @if (session('success'))
          <div class="alert alert-success mt-2">{{ session('success') }}</div>
      @endif

      <section class="panel-box mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <h2 class="h5 mb-0">Validasi Pembayaran & Pembatalan</h2>
          <span class="badge text-bg-dark">Kelola Reservasi</span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>No Transaksi</th>
                <th>Nama PIC / Tim</th>
                <th>Tanggal & Lapangan</th>
                <th>Jam (Durasi)</th>
                <th>Total / Bukti</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="reservasiTableBody">
              @forelse($reservasi ?? [] as $r)
                @if(in_array($r->status, ['menunggu', 'dibayar', 'menunggu_pembayaran']))
                  <tr>
                    <td>#RSV-{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $r->user->nama ?? 'Unknown' }}<br><small class="text-secondary">{{ $r->catatan }}</small></td>
                    <td>
                      {{ \Carbon\Carbon::parse($r->jadwal->tanggal)->format('d M Y') }}<br>
                      <span class="badge text-bg-secondary">{{ $r->jadwal->nama_lapangan }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($r->jadwal->jam_mulai)->format('H:i') }} ({{ $r->durasi_jam }} Jam)</td>
                    <td>
                      Rp{{ number_format($r->total_harga, 0, ',', '.') }}<br>
                      @if($r->bukti_pembayaran)
                        <a href="{{ asset('storage/' . $r->bukti_pembayaran) }}" target="_blank" class="badge text-bg-info text-decoration-none">Lihat Bukti</a>
                      @else
                        <span class="badge text-bg-light">Belum Upload</span>
                      @endif
                    </td>
                    <td>
                      @if($r->status === 'menunggu')
                        <span class="badge text-bg-warning">Pending</span>
                      @elseif($r->status === 'dibayar')
                         <span class="badge text-bg-success">Aktif / Disetujui</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        @if($r->status === 'menunggu')
                          <form action="{{ route('admin.reservasi.terima', $r->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-success" type="submit">Terima</button>
                          </form>
                          <form action="{{ route('admin.reservasi.tolak', $r->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Tolak & Batalkan?')">Tolak</button>
                          </form>
                        @elseif($r->status === 'dibayar')
                          <form action="{{ route('admin.reservasi.selesai', $r->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-primary" type="submit" onclick="return confirm('Tandai Selesai?')">Selesai Ber-main</button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endif
              @empty
                <tr><td colspan="7" class="text-center text-secondary">Belum ada data butuh divalidasi.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>

      <section class="panel-box">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <h2 class="h5 mb-0">Melihat Riwayat Penyewaan & Batal</h2>
          <span class="badge text-bg-secondary">Histori</span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal Main</th>
                <th>Nama PIC / Tim</th>
                <th>Lapangan</th>
                <th>Total Bayar</th>
                <th>Status Akhir</th>
              </tr>
            </thead>
            <tbody id="riwayatSewaBody">
              @foreach($reservasi ?? [] as $rev)
                @if(in_array($rev->status, ['selesai', 'dibatalkan']))
                  <tr>
                    <td>#RSV-{{ str_pad($rev->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ \Carbon\Carbon::parse($rev->jadwal->tanggal)->format('d M Y') }}</td>
                    <td>{{ $rev->user->nama ?? '-' }}</td>
                    <td>{{ $rev->jadwal->nama_lapangan ?? '-' }}</td>
                    <td>Rp{{ number_format($rev->total_harga, 0, ',', '.') }}</td>
                    <td>
                      @if($rev->status === 'selesai')
                         <span class="badge text-bg-primary">Selesai</span>
                      @else
                         <span class="badge text-bg-danger">Batal</span>
                      @endif
                    </td>
                  </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
@endsection
