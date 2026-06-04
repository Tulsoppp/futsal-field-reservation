@extends('layouts.admin')

@section('title', 'Form Pelanggan | Admin Jaya Futsal')

@section('content')
  @include('partials.admin.header', [
    'title' => 'Form Pelanggan',
    'active' => 'pelanggan',
  ])

  <main class="py-4 py-lg-5">
    <div class="container">
      <section class="panel-box admin-form-card mx-auto" style="max-width: 760px">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h2 class="h5 mb-1">Input Pelanggan</h2>
            <p class="text-secondary small mb-0" id="pelangganFormMode">
              Mode: {{ ($isEdit ?? false) ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}
            </p>
          </div>
          <a href="{{ route('admin.pelanggan') }}" class="btn btn-outline-dark btn-sm">
            Kembali ke List
          </a>
        </div>

        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form
          id="pelangganFormAdmin"
          class="row g-3"
          method="POST"
          action="{{ ($isEdit ?? false) ? route('admin.pelanggan.update', $pelanggan->id) : route('admin.pelanggan.store') }}">
          @csrf
          @if($isEdit ?? false)
            @method('PUT')
          @endif
          <div class="col-md-6">
            <label class="form-label" for="pelangganNama">Nama Pelanggan</label>
            <input class="form-control" id="pelangganNama" name="nama" type="text" value="{{ old('nama', $pelanggan->nama ?? '') }}" required />
          </div>
          <div class="col-md-6">
            <label class="form-label" for="pelangganTelepon">Telepon</label>
            <input class="form-control" id="pelangganTelepon" name="no_hp" type="text" value="{{ old('no_hp', $pelanggan->no_hp ?? '') }}" required />
          </div>
          <div class="col-12">
            <label class="form-label" for="pelangganEmail">Email</label>
            <input class="form-control" id="pelangganEmail" name="email" type="email" value="{{ old('email', $pelanggan->email ?? '') }}" required />
          </div>
          <div class="col-12">
            <label class="form-label" for="pelangganStatusMember">Status Membership</label>
            <select class="form-select" id="pelangganStatusMember" name="status_member">
              <option value="1" {{ old('status_member', $pelanggan->status_member ?? 0) == 1 ? 'selected' : '' }}>Member</option>
              <option value="0" {{ old('status_member', $pelanggan->status_member ?? 0) == 0 ? 'selected' : '' }}>Non Member</option>
            </select>
          </div>

          {{-- Field Membership — tampil hanya jika pilih Member --}}
          <div class="col-12" id="membershipFields" style="display: none;">
            <div class="card border-success">
              <div class="card-body">
                <h6 class="card-title text-success mb-3">
                  <i class="bi bi-star-fill me-1"></i>Detail Membership
                </h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="membershipType">Tipe Paket</label>
                    <select class="form-select" id="membershipType" name="membership_type">
                      <option value="Basic" {{ old('membership_type', $pelanggan->membership_type ?? '') === 'Basic' ? 'selected' : '' }}>Basic (1 Bulan)</option>
                      <option value="Pro Team" {{ old('membership_type', $pelanggan->membership_type ?? '') === 'Pro Team' ? 'selected' : '' }}>Pro Team (2 Bulan)</option>
                      <option value="Elite League" {{ old('membership_type', $pelanggan->membership_type ?? '') === 'Elite League' ? 'selected' : '' }}>Elite League (6 Bulan)</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Metode Pembayaran</label>
                    <input class="form-control bg-light" type="text" value="Cash (Bayar Langsung ke Admin)" disabled />
                    <small class="text-secondary">Pembayaran diterima langsung oleh admin.</small>
                  </div>
                </div>
                @if(($isEdit ?? false) && ($pelanggan->membership_expires_at ?? null))
                  <div class="mt-3">
                    <small class="text-secondary">
                      <i class="bi bi-calendar-check me-1"></i>
                      Membership aktif sampai: <strong>{{ \Carbon\Carbon::parse($pelanggan->membership_expires_at)->format('d M Y') }}</strong>
                    </small>
                  </div>
                @endif
              </div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label" for="pelangganPassword">Password</label>
            <input class="form-control" id="pelangganPassword" name="password" type="password" {{ ($isEdit ?? false) ? '' : 'required' }} />
            <small class="text-secondary">{{ ($isEdit ?? false) ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 6 karakter.' }}</small>
          </div>
          <div class="col-12 d-flex gap-2 flex-wrap">
            <button class="btn btn-accent" type="submit">Simpan Pelanggan</button>
            <a class="btn btn-outline-dark" href="{{ route('admin.pelanggan') }}">Batal</a>
          </div>
        </form>
      </section>
    </div>
  </main>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const statusSelect = document.getElementById('pelangganStatusMember');
        const membershipFields = document.getElementById('membershipFields');

        function toggleMembershipFields() {
          if (statusSelect.value === '1') {
            membershipFields.style.display = 'block';
          } else {
            membershipFields.style.display = 'none';
          }
        }

        statusSelect.addEventListener('change', toggleMembershipFields);

        // Trigger on load to show fields if editing a member
        toggleMembershipFields();
      });
    </script>
  @endpush
@endsection
