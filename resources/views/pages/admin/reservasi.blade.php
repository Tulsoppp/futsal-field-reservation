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
                @if(in_array($r->status, ['menunggu', 'pending', 'disetujui', 'dibayar']))
                  <tr>
                    <td>#RSV-{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $r->user->nama ?? 'Unknown' }}<br><small class="text-secondary">{{ $r->catatan }}</small></td>
                    <td>
                      {{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}<br>
                      <span class="badge text-bg-secondary">Lapangan Utama</span>
                    </td>
                    <td>
                      {{ \Carbon\Carbon::parse($r->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($r->jam_selesai)->format('H:i') }}
                    </td>
                    <td>
                      Rp{{ number_format($r->total_harga, 0, ',', '.') }}<br>
                      @if($r->bukti_pembayaran)
                        <button
                          class="badge text-bg-info text-decoration-none border-0"
                          type="button"
                          data-bs-toggle="modal"
                          data-bs-target="#modalBuktiPembayaran"
                          data-bs-file="{{ asset('storage/' . $r->bukti_pembayaran) }}">
                          Lihat Bukti
                        </button>
                      @else
                        <span class="badge text-bg-light">Belum Upload</span>
                      @endif
                    </td>
                    <td>
                      @if($r->status === 'menunggu')
                        <span class="badge text-bg-warning">Menunggu Pembayaran</span>
                      @elseif($r->status === 'pending')
                        <span class="badge text-bg-info">Menunggu Konfirmasi</span>
                      @elseif(in_array($r->status, ['disetujui', 'dibayar']))
                         <span class="badge text-bg-success">Disetujui</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        @if($r->status === 'pending')
                          <form action="{{ route('admin.reservasi.terima', $r->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-success" type="submit">Terima</button>
                          </form>
                          <form action="{{ route('admin.reservasi.tolak', $r->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Tolak & Batalkan?')">Tolak</button>
                          </form>
                        @elseif(in_array($r->status, ['disetujui', 'dibayar']))
                          <form action="{{ route('admin.reservasi.selesai', $r->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-primary" type="submit" onclick="return confirm('Tandai Selesai?')">Selesai Ber-main</button>
                          </form>
                        @else
                          -
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
                    <td>{{ \Carbon\Carbon::parse($rev->tanggal)->format('d M Y') }}</td>
                    <td>{{ $rev->user->nama ?? '-' }}</td>
                    <td>Lapangan Utama</td>
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

  <div class="modal fade" id="modalBuktiPembayaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Bukti Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="buktiPembayaranImg" src="" alt="Bukti Pembayaran" class="img-fluid rounded d-none" />
          <iframe id="buktiPembayaranFrame" class="w-100 d-none" style="height: 70vh" title="Bukti Pembayaran"></iframe>
          <div id="buktiPembayaranFallback" class="text-secondary small mt-3 d-none">
            File tidak dapat ditampilkan. Silakan unduh:
            <a id="buktiPembayaranLink" href="#" target="_blank" rel="noopener">Buka Bukti</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modalBuktiPembayaran');
        const img = document.getElementById('buktiPembayaranImg');
        const frame = document.getElementById('buktiPembayaranFrame');
        const fallback = document.getElementById('buktiPembayaranFallback');
        const link = document.getElementById('buktiPembayaranLink');

        if (!modal || !img) {
          return;
        }

        modal.addEventListener('show.bs.modal', (event) => {
          const button = event.relatedTarget;
          const fileUrl = button?.getAttribute('data-bs-file') || '';
          const isPdf = fileUrl.toLowerCase().endsWith('.pdf');

          img.classList.add('d-none');
          frame.classList.add('d-none');
          fallback.classList.add('d-none');

          if (!fileUrl) {
            fallback.classList.remove('d-none');
            link.setAttribute('href', '#');
            return;
          }

          link.setAttribute('href', fileUrl);

          if (isPdf) {
            frame.src = fileUrl;
            frame.classList.remove('d-none');
            return;
          }

          img.onerror = () => {
            img.classList.add('d-none');
            fallback.classList.remove('d-none');
          };
          img.src = fileUrl;
          img.classList.remove('d-none');
        });

        modal.addEventListener('hidden.bs.modal', () => {
          img.src = '';
          frame.src = '';
          img.onerror = null;
          img.classList.add('d-none');
          frame.classList.add('d-none');
          fallback.classList.add('d-none');
        });
      });
    </script>
  @endpush
@endsection
