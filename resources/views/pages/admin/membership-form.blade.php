@extends('layouts.admin')

@section('title', 'Form Membership | Admin Jaya Futsal')

@section('content')
  @include('partials.admin.header', [
    'title' => 'Form Membership',
    'active' => 'membership',
  ])

  <main class="py-4 py-lg-5">
    <div class="container">
      <section class="panel-box admin-form-card mx-auto" style="max-width: 760px">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h2 class="h5 mb-1">Input Membership</h2>
            <p class="text-secondary small mb-0" id="membershipFormMode">
              Mode: Tambah Membership
            </p>
          </div>
          <a href="{{ url('/admin/membership') }}" class="btn btn-outline-dark btn-sm">
            Kembali ke List
          </a>
        </div>

        <form id="membershipFormAdmin" class="row g-3">
          <input type="hidden" id="membershipId" />
          <div class="col-md-6">
            <label class="form-label" for="membershipNamaTim">Nama Tim</label>
            <input class="form-control" id="membershipNamaTim" type="text" required />
          </div>
          <div class="col-md-6">
            <label class="form-label" for="membershipPaket">Paket</label>
            <select class="form-select" id="membershipPaket">
              <option>Member Jaya Futsal</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="membershipMasaAktif">Masa Aktif</label>
            <select class="form-select" id="membershipMasaAktif">
              <option>1 Bulan</option>
              <option>3 Bulan</option>
              <option>6 Bulan</option>
              <option>12 Bulan</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="membershipStatus">Status</label>
            <select class="form-select" id="membershipStatus">
              <option>Aktif</option>
              <option>Menunggu Aktivasi</option>
              <option>Kadaluarsa</option>
            </select>
          </div>
          <div class="col-12 d-flex gap-2 flex-wrap">
            <button class="btn btn-accent" type="submit">Simpan Membership</button>
            <button class="btn btn-outline-dark" id="membershipCancel" type="button">Batal</button>
          </div>
        </form>
      </section>
    </div>
  </main>
@endsection
