@extends('layouts.admin')

@section('title', 'Form Jadwal Lapangan | Admin Jaya Futsal')

@section('content')
  @include('partials.admin.header', [
    'title' => 'Form Jadwal Lapangan',
    'active' => 'jadwal',
  ])

  <main class="py-4 py-lg-5">
    <div class="container">
      <section class="panel-box admin-form-card mx-auto" >
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h2 class="h5 mb-1">Input Jadwal</h2>
            <p class="text-secondary small mb-0" id="jadwalFormMode">
              Mode: Tambah Jadwal
            </p>
          </div>
          <a href="{{ url('/admin/jadwal') }}" class="btn btn-outline-dark btn-sm">
            Kembali ke List
          </a>
        </div>

        <form id="jadwalForm" class="row g-3" method="POST" action="{{ route('buat-jadwal') }}">
          @csrf
          
          <input type="hidden" id="jadwalId" />
          <div class="col-md-6">
            <label class="form-label" for="jadwalTanggal">Tanggal</label>
            <input class="form-control" type="date" id="jadwalTanggal" required name="tanggal"/>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="jadwalJamMulai">Jam Mulai</label>
            <input class="form-control" type="time" id="jadwalJamMulai" required name="jam_mulai"/>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="jadwalJamSelesai">Jam Selesai</label>
            <input class="form-control" type="time" id="jadwalJamSelesai" required name="jam_selesai" />
          </div>
          <div class="col-md-6">
            <label class="form-label" for="jadwalLapangan">Nama Lapangan</label>
            <input class="form-control" type="text" id="jadwalLapangan" placeholder="Lapangan A" required name="nama_lapangan" />
          </div>
          <div class="col-md-3">
            <label class="form-label" for="jadwalHarga">Harga/Jam</label>
            <input class="form-control" type="number" id="jadwalHarga" placeholder="200000" required name="harga_per_jam"/>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="jadwalStatus" name>Status</label>
            <select class="form-select" id="jadwalStatus" name="status">
              <option>Tersedia</option>
              <option>Penuh</option>
              <option>Perawatan</option>
            </select>
          </div>
          <div class="col-12 d-flex gap-2 flex-wrap">
            <button class="btn btn-accent" type="submit">Simpan Jadwal</button>
            <button class="btn btn-outline-dark" id="jadwalCancel" type="button">Batal</button>
          </div>
        </form>
      </section>
    </div>
  </main>
@endsection
