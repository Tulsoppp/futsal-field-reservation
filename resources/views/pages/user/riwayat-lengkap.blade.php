@extends('layouts.app')

@section('title', 'Riwayat Reservasi | Jaya Futsal')

@section('content')
    @include('partials.user.reservasi-navbar')

    <main class="py-4 py-lg-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h2 class="h3 mb-0">Riwayat Booking Semua Lapangan</h2>
                        <a href="{{ route('reservasi.index') }}" class="btn btn-dark">Buat Booking Baru</a>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Tanggal Main</th>
                                            <th>Jam</th>
                                            <th>Total Harga</th>
                                            <th>Metode Bayar</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($semuaRiwayat as $r)
                                            <tr>
                                                <td class="ps-4">{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ \Carbon\Carbon::parse($r->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($r->jam_selesai)->format('H:i') }}
                                                    </span>
                                                </td>
                                                <td class="fw-bold text-success">
                                                    Rp{{ number_format($r->total_harga, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    {{ $r->metode_pembayaran ? strtoupper($r->metode_pembayaran) : '-' }}
                                                </td>
                                                <td>
                                                    @if ($r->status === 'menunggu')
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Pending</span>
                                                    @elseif ($r->status === 'dibayar' || $r->status === 'selesai')
                                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Lunas</span>
                                                    @elseif ($r->status === 'dibatalkan')
                                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Dibatalkan</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($r->status === 'dibayar' || $r->status === 'selesai')
                                                        <a href="{{ route('reservasi.cetak', $r->id) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                                            <i class="bi bi-printer"></i> Cetak
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    Belum ada riwayat booking.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </main>
@endsection