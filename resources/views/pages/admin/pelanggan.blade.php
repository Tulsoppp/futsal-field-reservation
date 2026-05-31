@extends('layouts.admin')

@section('title', 'Kelola Daftar Pelanggan | Admin Jaya Futsal')

@section('content')
  @include('partials.admin.header', [
    'title' => 'Kelola Daftar Pelanggan',
    'active' => 'pelanggan',
  ])

  <main class="py-4 py-lg-5">
    <div class="container">
      <section class="panel-box">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h2 class="h5 mb-1">Daftar Pelanggan</h2>
            <p class="text-secondary small mb-0">
              Halaman list pelanggan dipisah dari form tambah/edit.
            </p>
          </div>
          <a href="{{ route('admin.pelanggan.form') }}" class="btn btn-accent">+ Tambah Pelanggan</a>
        </div>
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="pelangganTableBody">
              @forelse($pelanggan ?? [] as $item)
                <tr>
                  <td>#PLG-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                  <td>{{ $item->nama }}</td>
                  <td>{{ $item->no_hp }}</td>
                  <td>{{ $item->email }}</td>
                  <td>
                    @if($item->status_member)
                      <span class="badge text-bg-success">Member</span>
                    @else
                      <span class="badge text-bg-secondary">Non Member</span>
                    @endif
                  </td>
                  <td class="text-nowrap">
                    <a href="{{ route('admin.pelanggan.edit', $item->id) }}" class="btn btn-sm btn-outline-dark">Edit</a>
                    <form action="{{ route('admin.pelanggan.destroy', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Hapus pelanggan ini?')">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-secondary">Belum ada data pelanggan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
@endsection
