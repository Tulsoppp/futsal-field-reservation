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
                    @if($m->membership_proof === 'cash')
                      <span class="badge text-bg-secondary">Cash</span>
                    @elseif($m->membership_proof)
                      <button
                        class="badge bg-info text-decoration-none border-0"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalBuktiMembership"
                        data-bs-file="{{ asset('storage/' . $m->membership_proof) }}">
                        Cek Bukti
                      </button>
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
                <tr><td colspan="6" class="text-center text-secondary py-4">Tidak ada data yang tersedia.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($memberships->hasPages())
          <div class="d-flex justify-content-center mt-3">
            {{ $memberships->links() }}
          </div>
        @endif
      </section>
    </div>
  </main>

  <div class="modal fade" id="modalBuktiMembership" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Bukti Membership</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="buktiMembershipImg" src="" alt="Bukti Membership" class="img-fluid rounded d-none" />
          <iframe id="buktiMembershipFrame" class="w-100 d-none" style="height: 70vh" title="Bukti Membership"></iframe>
          <div id="buktiMembershipFallback" class="text-secondary small mt-3 d-none">
            File tidak dapat ditampilkan. Silakan unduh:
            <a id="buktiMembershipLink" href="#" target="_blank" rel="noopener">Buka Bukti</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modalBuktiMembership');
        const img = document.getElementById('buktiMembershipImg');
        const frame = document.getElementById('buktiMembershipFrame');
        const fallback = document.getElementById('buktiMembershipFallback');
        const link = document.getElementById('buktiMembershipLink');

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
