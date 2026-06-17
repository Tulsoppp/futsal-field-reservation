@extends('layouts.app')

@section('title', 'Profil Member | Jaya Futsal')

@section('content')
    @include('partials.user.reservasi-navbar')

    <main class="py-4 py-lg-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-dark text-white p-4 border-0 rounded-top-4 d-flex align-items-center gap-3">
                            <img src="{{ asset('assets/img/user-avatar.png') }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama) }}&background=fff&color=000'" alt="Profile" class="rounded-circle" width="80" height="80" style="object-fit: cover; border: 3px solid #fff;">
                            <div>
                                <h3 class="h4 mb-1">{{ $user->nama }}</h3>
                                <p class="mb-0 text-white-50">{{ $user->email }} | {{ $user->no_hp }}</p>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="h5 mb-4">Status Keanggotaan (Membership)</h4>
                            
                            @if(empty($user->membership_type) || $user->membership_status === 'rejected')
                                <div class="alert alert-secondary">
                                    <h5 class="alert-heading">Belum Terdaftar Member</h5>
                                    <p class="mb-0">Kamu saat ini belum tergabung sebagai member eksklusif. Daftarkan timmu untuk dapatkan promo khusus!</p>
                                    @if($user->membership_status === 'rejected')
                                        <hr>
                                        <p class="mb-0 text-danger"><b>Info:</b> Pengajuan membership kamu sebelumnya ditolak oleh admin.</p>
                                    @endif
                                </div>
                            @elseif($user->membership_status === 'pending')
                                <div class="alert alert-warning">
                                    <h5 class="alert-heading">Menunggu Verifikasi</h5>
                                    <p class="mb-0">Pengajuan paket <b>{{ $user->membership_type }}</b> kamu sedang diperiksa oleh admin. Harap tunggu!</p>
                                </div>
                            @elseif($user->membership_status === 'active')
                                @php
                                    $expires = \Carbon\Carbon::parse($user->membership_expires_at);
                                    $sisaHari = now()->diffInDays($expires, false);
                                @endphp
                                @if($sisaHari < 0)
                                    <div class="alert alert-danger">
                                        <h5 class="alert-heading">Membership Kedaluwarsa!</h5>
                                        <p class="mb-0">Membership <b>Member Jaya Futsal</b> kamu sudah habis masa aktifnya sejak {{ $expires->format('d M Y') }}.</p>
                                    </div>
                                @else
                                    <div class="alert alert-success">
                                        <h5 class="alert-heading d-flex align-items-center gap-2">
                                            <i class="bi bi-patch-check-fill text-success fs-4"></i>
                                            Member Aktif: Member Jaya Futsal
                                        </h5>
                                        <p class="mb-0 mt-2">Masa aktif membership kamu sampai dengan <b>{{ $expires->format('d M Y') }}</b>.</p>
                                        <hr>
                                        <p class="mb-0"><b>Sisa Waktu:</b> {{ intval($sisaHari) }} hari lagi.</p>
                                        <p class="mb-0 mt-1">
                                            <b>Voucher Free 1 Jam:</b>
                                            @if(!$user->membership_free_hour_used)
                                                <span class="badge bg-success">Tersedia</span> — Akan otomatis diterapkan saat booking berikutnya.
                                            @else
                                                <span class="badge bg-secondary">Sudah Digunakan</span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            @endif

                            <div class="mt-4">
                                <a href="{{ route('reservasi.index') }}" class="btn btn-outline-dark">Kembali ke Reservasi</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection