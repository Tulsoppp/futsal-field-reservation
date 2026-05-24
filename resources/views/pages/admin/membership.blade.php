@extends('layouts.admin')

@section('title', 'Kelola Data Membership | Admin Jaya Futsal')

@section('content')
  @include('partials.admin.header', [
    'title' => 'Kelola Data Membership',
    'active' => 'membership',
  ])

  <main class="py-4 py-lg-5">
    <div class="container">
      <section class="panel-box">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h2 class="h5 mb-1">Data Pengajuan Membership & Validasi</h2>
            <p class="text-secondary small mb-0">
              Setujui atau tolak pengajuan membership pelanggan di sini.
            </p>
          </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Nama Pelanggan</th>
                <th>Tipe Paket</th>
                <th>Bukti Transfer</th>
                <th>Kadaluarsa</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="membershipTableBody">
              @forelse($memberships as $m)
                <tr>
                  <td>{{ $m->nama }}<br><small class="text-secondary">{{ $m->no_hp }}</small></td>
                  <td><span class="badge text-bg-dark">{{ $m->membership_type }}</span></td>
                  <td>
                    @if($m->membership_proof)
                      <a href="{{ asset('storage/' . $m->membership_proof) }}" target="_blank" class="badge bg-info text-decoration-none">Cek Bukti</a>
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $m->membership_expires_at ? \Carbon\Carbon::parse($m->membership_expires_at)->format('d M Y') : '-' }}</td>
                  <td>
                    @if($m->membership_status === 'pending')
                      <span class="badge bg-warning">Menunggu</span>
                    @elseif($m->membership_status === 'active')
                      <span class="badge bg-success">Aktif</span>
                    @elseif($m->membership_status === 'rejected')
                      <span class="badge bg-danger">Ditolak</span>
                    @endif
                  </td>
                  <td>
                    @if($m->membership_status === 'pending')
                      <div class="d-flex gap-1">
                        <form action="{{ route('admin.membership.terima', $m->id) }}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                        </form>
                        <form action="{{ route('admin.membership.tolak', $m->id) }}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak member ini?')">Tolak</button>
                        </form>
                      </div>
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center">Belum ada data pengajuan membership.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
@endsection
