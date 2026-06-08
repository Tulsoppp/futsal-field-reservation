@extends('layouts.app')

@section('title', 'Dashboard Reservasi | Jaya Futsal')
@section('body-class', 'page-reservasi')

@section('content')
    @include('partials.user.reservasi-navbar')

    @push('scripts')
        <script>
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000
            };

            @if (session())
                toastr.success(@json(session('success')));
            @endif
            
            @if(session('error'))
                toastr.error(@json(session('error')));
            @endif

            // Logika menampilkan QRIS
            document.addEventListener("DOMContentLoaded", function() {
                const mtBayar = document.getElementById("metodeBayar");
                const qrisBox = document.getElementById("qrisBox");

                if (mtBayar && qrisBox) {
                    mtBayar.addEventListener("change", function() {
                        if (this.value === "QRIS") {
                            qrisBox.classList.remove("d-none");
                        } else {
                            qrisBox.classList.add("d-none");
                        }
                    });
                    
                    // Trigger load awal kalau default udah QRIS
                    mtBayar.dispatchEvent(new Event('change'));
                }
                
                // Cek jika ada unfinished payment saat render awal
                const unpaidId = "{{ $unpaidReservasi ? $unpaidReservasi->id : '' }}";
                const unpaidTotal = "{{ $unpaidReservasi ? $unpaidReservasi->total_harga : '' }}";
                
                if (unpaidId) {
                    // Trigger setSummary supaya data durasi, jam dan semuanya muncul di Card Samping Kanan
                    if(typeof setSummary === 'function'){
                        setSummary('Menunggu pembayaran (Bukti Tagihan)');
                    }

                    // Set parameter JS
                    window.currentUnpaidId = unpaidId;
                    
                    // Update tampilan nominal jika ada element-nya
                    const totalPembayaranElems = document.querySelectorAll(
                        '.step-panel[data-step="3"] .estimated-total strong',
                    );
                    totalPembayaranElems.forEach((el) => {
                        el.textContent = "Rp" + new Intl.NumberFormat("id-ID").format(unpaidTotal);
                    });
                    
                    // Otomatis buka step ke 3
                    if (typeof setStep === 'function' && typeof setMaxStep === 'function') {
                        setMaxStep(3);
                        setStep(3);
                    }

                    const countdownBox = document.getElementById('paymentCountdown');
                    if (countdownBox) {
                        const expiry = countdownBox.getAttribute('data-expiry');
                        const timerText = countdownBox.querySelector('[data-countdown-text]');

                        if (expiry && timerText) {
                            const expiryTime = new Date(expiry).getTime();

                            const updateCountdown = () => {
                                const now = Date.now();
                                const diff = expiryTime - now;

                                if (diff <= 0) {
                                    timerText.textContent = 'Waktu pembayaran sudah habis.';
                                    return false;
                                }

                                const totalSeconds = Math.floor(diff / 1000);
                                const hours = Math.floor(totalSeconds / 3600);
                                const minutes = Math.floor((totalSeconds % 3600) / 60);
                                const seconds = totalSeconds % 60;

                                timerText.textContent = `Sisa waktu: ${hours}j ${minutes}m ${seconds}d`;
                                return true;
                            };

                            updateCountdown();
                            const timerId = setInterval(() => {
                                if (!updateCountdown()) {
                                    clearInterval(timerId);
                                }
                            }, 1000);
                        }
                    }
                }
                
                // Interaksi tombol bayar di tabel riwayat
                const btnRiwayatList = document.querySelectorAll('.btn-bayar-riwayat');
                btnRiwayatList.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const id = this.getAttribute('data-id');
                        const total = this.getAttribute('data-total');
                        
                        document.getElementById('reservasiId').value = id;
                        
                        // Update total nominal step 3
                        const h3Elems = document.querySelectorAll('.step-panel[data-step="3"] .estimated-total strong');
                        h3Elems.forEach((el) => {
                            el.textContent = "Rp" + new Intl.NumberFormat("id-ID").format(total);
                        });
                        
                        // Arahkan ke step 3 (scroll ke atas gais)
                        if (typeof setStep === 'function' && typeof setMaxStep === 'function') {
                            setMaxStep(3);
                            setStep(3);
                            document.getElementById('bookingSteps').scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                });
            });
        </script>
    @endpush
    <main class="py-4 py-lg-5">
        <div class="container">
            <section class="mb-4">
                <div class="pitch-banner">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <p class="section-kicker mb-2">Reservasi Futsal</p>
                            <h1 class="h3 mb-1">Siapkan Jadwal, Atur Lapangan, Gas Main</h1>
                            <p class="mb-0 text-dark-70">
                                Lihat slot kosong, isi detail tim, dan lanjutkan pembayaran tanpa ribet.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge text-bg-dark">Lapangan Ada</span>
                            <div class="pitch-visual">
                                <img class="pitch-img" src="{{ asset('assets/img/futsal-pitch.svg') }}"
                                    alt="Ilustrasi lapangan futsal" />
                            </div>
                        </div>
                    </div>
                    <div class="pitch-steps">
                        <span class="pitch-step">Tanggal & Jam</span>
                        <span class="pitch-step">Durasi</span>
                        <span class="pitch-step">Konfirmasi</span>
                        <span class="pitch-step">Bayar</span>
                    </div>
                </div>
            </section>
            <section class="mb-4 mb-lg-5">
                <div class="panel-box">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="h4 mb-0">Form Pemesanan Lapangan</h2>
                        <span class="badge text-bg-dark">Step by Step</span>
                    </div>
                    <p class="text-dark-70 mb-3 booking-caption">
                        Ikuti urutan ini: isi jadwal, cek ringkasan otomatis, lalu konfirmasi dan lakukan
                        pembayaran.
                        @if(Auth::user() && Auth::user()->membership_status === 'active' && Auth::user()->status_member == 1)
                            <br><span class="text-success fw-bold"><i class="bi bi-tags-fill"></i> Harga Spesial Member: Rp80.000 / Jam otomatis diterapkan!</span>
                        @else
                            <br><span class="text-secondary"><i class="bi bi-info-circle"></i> Harga Normal: Rp100.000 / Jam. (Daftar member untuk dapatkan harga spesial)</span>
                        @endif
                    </p>
                    <div class="steps-wrap mb-4" id="bookingSteps">
                        <button class="step-item active" id="step-1" type="button" data-step="1">
                            1. Pilih Jadwal
                        </button>
                        <button class="step-item" id="step-2" type="button" data-step="2" disabled>
                            2. Konfirmasi Pesanan
                        </button>
                        <button class="step-item" id="step-3" type="button" data-step="3" disabled>
                            3. Pembayaran
                        </button>
                        <button class="step-item" id="step-4" type="button" data-step="4" disabled>
                            4. Status Booking
                        </button>
                    </div>

            

                    <div class="row g-4 booking-layout" id="bookingFlow">
                        <div class="col-lg-12">
                            <div class="step-panel is-active" data-step="1">
                                <form class="row g-3" id="bookingForm">
                                    @csrf
                                    <input type="hidden" id="reservasiId" value="{{ $unpaidReservasi ? $unpaidReservasi->id : '' }}">
                                    <input type="hidden" id="id_jadwal" name="id_jadwal" value="1">
                                    <div class="col-12 mb-3">
                                        @if($unpaidReservasi)
                                            <div class="alert alert-danger mb-0">
                                                <strong>Belum Dibayar!</strong> Anda memiliki pesanan yang belum dibayar. <br>
                                                Silakan selesaikan pembayaran terlebih dahulu sebelum membuat jadwal baru.
                                            </div>
                                        @else
                                            <div class="form-hint-card">
                                                <strong>Mulai dari sini:</strong> isi tanggal, jam, dan durasi.
                                                Ringkasan pesanan akan muncul di langkah berikutnya.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <div class="booking-input-group row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="tanggalMain">Tanggal Main</label>
                                                <input class="form-control" type="date" id="tanggalMain" name="tanggal" 
                                                    value="{{ $unpaidReservasi ? $unpaidReservasi->tanggal : '' }}"
                                                    {{ $unpaidReservasi ? 'disabled' : 'required' }} />
                                                <div id="tanggalError" class="invalid-feedback d-none" style="display: block;">
                                                    Maaf, jadwal di tanggal ini tidak tersedia / penuh.
                                                </div>
                                                <div id="tanggalSuccess" class="valid-feedback d-none" style="display: block; color: green;">
                                                    Jadwal tersedia! Silakan pilih jam mulai.
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="jamMain">Jam Mulai</label>
                                                <select class="form-select" id="jamMain" name="jam_mulai" disabled>
                                                    @if($unpaidReservasi)
                                                        <option value="{{ substr($unpaidReservasi->jam_mulai, 0, 5) }}" selected>
                                                            {{ substr($unpaidReservasi->jam_mulai, 0, 5) }}
                                                        </option>
                                                    @else
                                                        <option value="">Pilih Tanggal Main Terlebih Dahulu...</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="jamSelesaiMain">Jam Selesai</label>
                                                <select class="form-select" id="jamSelesaiMain" name="jam_selesai" disabled>
                                                    @if($unpaidReservasi)
                                                        <option value="{{ substr($unpaidReservasi->jam_selesai, 0, 5) }}" selected>
                                                            {{ substr($unpaidReservasi->jam_selesai, 0, 5) }}
                                                        </option>
                                                    @else
                                                        <option value="">Pilih Jam Mulai Terlebih Dahulu...</option>
                                                    @endif
                                                </select>
                                            </div>
                                 
                                            <div class="col-12">
                                                <label class="form-label" for="catatan">Catatan Tambahan</label>
                                                <textarea class="form-control" id="catatan" rows="3" name="catatan" placeholder="Contoh: sparing internal, butuh bola 2" {{ $unpaidReservasi ? 'disabled' : '' }}>{{ $unpaidReservasi ? $unpaidReservasi->catatan : '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button class="btn btn-accent" id="btnToConfirm" type="button" {{ $unpaidReservasi ? 'disabled' : '' }}>
                                            Lanjut ke Konfirmasi
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="step-panel" data-step="2">
                                <div class="form-hint-card mb-3">
                                    <strong>Konfirmasi Pesanan:</strong> cek ulang detail jadwal sebelum
                                    lanjut ke pembayaran.
                                </div>
                                <div class="booking-input-group">
                                    <div class="mb-3">
                                        <span class="text-secondary d-block mb-1">Catatan Tambahan</span>
                                        <div class="booking-note" id="catatanPreview">-</div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-outline-dark" id="btnBackToStep1" type="button">
                                            Kembali
                                        </button>
                                        <button class="btn btn-accent" id="btnKonfirmasi" type="button">
                                            Konfirmasi Pesanan
                                        </button>
                                        <button class="btn btn-outline-danger" id="btnBatal" type="button">
                                            Batalkan Pesanan
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="step-panel" data-step="3">
                                <div class="form-hint-card mb-3">
                                    <strong>Pembayaran:</strong> pilih metode pembayaran dan upload bukti reservasi.
                                </div>
                                <div class="booking-input-group">
                                    <p class="text-dark-70 mb-3">
                                        Transfer ke rekening Jaya Futsal, lalu upload bukti pembayaran untuk aktivasi
                                        booking.
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-outline-dark" id="btnBackToStep2" type="button">
                                            Kembali
                                        </button>
                                        <button class="btn btn-dark" id="btnBayar" type="button">
                                            Upload Bukti & Bayar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="step-panel" data-step="4">
                                <div class="booking-input-group">
                                    <h3 class="h5 mb-2">Status Booking</h3>
                                    <p class="mb-3 text-dark-70">
                                        <span class="badge text-bg-warning" id="bookingStatusBadge">Pending</span>
                                    </p>
                                    <p class="mb-4" id="bookingStatusText">Pembayaran diterima. Menunggu konfirmasi admin.</p>
                                    <button class="btn btn-accent" id="btnResetBooking" type="button">
                                        Buat Reservasi Baru
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="step-panel" data-step="2">
                                <div class="order-summary">
                                    <h3 class="h5 mb-3">Ringkasan Pesanan</h3>
                                    <ul class="list-unstyled mb-3 small" id="summaryList">
                                        <li>Tanggal: -</li>
                                        <li>Jam: -</li>
                                        <li>Durasi: -</li>
                                        <li>Status: Menunggu konfirmasi</li>
                                    </ul>
                                    <div class="estimated-total mb-3">
                                        Estimasi Total
                                        <strong id="estimasiTotal">Rp120.000</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="step-panel" data-step="3">
                                <div class="order-summary">
                                    <h3 class="h5 mb-3">Pembayaran Reservasi</h3>
                                    <div class="estimated-total mb-3">
                                        Total Pembayaran
                                        <strong>Rp120.000</strong>
                                    </div>
                                    @if($unpaidReservasi && $unpaidReservasi->status === 'menunggu' && empty($unpaidReservasi->bukti_pembayaran))
                                        <div
                                            class="alert alert-danger py-2 small"
                                            id="paymentCountdown"
                                            data-expiry="{{ \Carbon\Carbon::parse($unpaidReservasi->created_at)->addHour()->toIso8601String() }}">
                                            <div class="fw-semibold">Batas Upload Bukti Pembayaran</div>
                                            <div data-countdown-text>Sisa waktu: -</div>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label" for="metodeBayar">Metode Pembayaran</label>
                                        <select class="form-select" id="metodeBayar" name="metode_pembayaran">
                                        
                                            <option value="QRIS">QRIS</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 d-none text-center p-3 border rounded shadow-sm" id="qrisBox">
                                        <p class="mb-2 fw-bold">Scan QRIS Berikut:</p>
                                        <img src="{{ asset('assets/img/qris.png') }}" alt="QRIS Futsal" class="img-fluid" style="max-height: 250px;">
                                    </div>
                                    <div class="upload-proof mb-3">
                                        <label class="form-label" for="buktiReservasi">
                                            Upload Bukti Pembayaran Reservasi
                                        </label>
                                        <input class="form-control" type="file" id="buktiReservasi" accept="image/*,.pdf" name="bukti_pembayaran" />
                                        <small class="text-secondary d-block mt-2" id="buktiReservasiInfo">
                                            Belum ada file dipilih.
                                        </small>
                                        <div class="invalid-feedback d-block" id="buktiReservasiError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-panel" data-step="4">
                                <div class="order-summary">
                                    <h3 class="h5 mb-3">Ringkasan Booking</h3>
                                    <ul class="list-unstyled mb-3 small" id="summaryListFinal"></ul>
                                    <small class="text-secondary d-block">
                                        Simpan bukti pembayaran untuk verifikasi admin.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="riwayat" class="mb-4 mb-lg-5">
                <div class="panel-box">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="h4 mb-0">Riwayat Booking Terakhir</h2>
                        <div>
                            <span class="badge text-bg-secondary me-2">3 Data Terakhir</span>
                            <a href="{{ route('reservasi.riwayat-lengkap') }}" class="btn btn-sm btn-outline-dark">Lihat Semua</a>
                        </div>
                    </div>

                    @php
                        $adaBelumBayar = collect($riwayat ?? [])->contains(function ($item) {
                            return $item->status === 'menunggu' && empty($item->bukti_pembayaran);
                        });
                    @endphp

                    @if($adaBelumBayar)
                        <div class="alert alert-warning mb-3">
                            <strong>Perhatian!</strong> Kamu memiliki reservasi yang belum selesai (belum upload bukti pembayaran). Silakan selesaikan pembayaran dan upload buktinya agar jadwal tidak dibatalkan otomatis.
                            <br><small><i>(Silakan ulangi proses pilih jadwal yang sama dan lanjutkan ke langkah pembayaran)</i></small>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Lapangan</th>
                                    <th>Durasi</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayat ?? [] as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                        <td>Lapangan Utama</td>
                                        <td>{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</td>
                                        <td>Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($item->status === 'selesai')
                                                <span class="badge text-bg-primary">Selesai</span>
                                            @elseif (in_array($item->status, ['disetujui', 'dibayar']))
                                                <span class="badge text-bg-success">Aktif</span>
                                            @elseif ($item->status === 'pending')
                                                <span class="badge text-bg-info">Pending</span>
                                            @elseif ($item->status === 'dibatalkan')
                                                <span class="badge text-bg-danger">Dibatalkan</span>
                                            @else
                                                <span class="badge text-bg-warning">Menunggu</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status === 'menunggu' && empty($item->bukti_pembayaran))
                                                <button class="btn btn-sm btn-primary btn-bayar-riwayat" 
                                                    data-id="{{ $item->id }}" 
                                                    data-total="{{ $item->total_harga }}">
                                                    Bayar Sekarang
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-secondary">Belum ada data booking.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="membership-form" class="d-none mb-4 mb-lg-5">
                <div class="panel-box">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="h4 mb-0">Daftar Membership</h2>
                        <span class="badge text-bg-success">Promo Spesial</span>
                    </div>
                    <div class="alert alert-info border-0 shadow-sm rounded">
                        <strong>Mengapa Member?</strong> Jadilah member dan nikmati harga lebih murah di reservasi kamu berikutnya, prioritas booking, serta beragam promo menarik lainnya. <br>
                        <em>Catatan:</em> Jika kamu mendaftar member sekarang, pengajuanmu akan diproses oleh admin (Pending). Proses reservasi saat ini dibatalkan sementara. Silakan buat reservasi kembali setelah status member kamu Aktif untuk mendapatkan harga spesial.
                    </div>
                    <p class="text-dark-70 mb-4">
                        Isi form di bawah ini lalu lakukan pembayaran via QRIS untuk mengaktifkan membership.
                    </p>
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <form class="booking-input-group row g-3" id="formDaftarMembership" onsubmit="event.preventDefault(); submitMembership();">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label" for="paketMember">Pilih Paket</label>
                                    <select class="form-select" id="paketMember" name="membership_type" required>
                                        <option value="149000">Basic (1 Bulan) - Rp149.000</option>
                                        <option value="299000">Pro Team (2 Bulan) - Rp299.000</option>
                                        <option value="449000">Elite League (6 Bulan) - Rp449.000</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="metodeBayarMember">Metode Pembayaran</label>
                                    <select class="form-select" id="metodeBayarMember" disabled>
                                        <option value="QRIS" selected>QRIS Resmi</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-3 text-center p-3 border rounded shadow-sm">
                                    <p class="mb-2 fw-bold">Scan QRIS Untuk Bayar Membership:</p>
                                    <img src="{{ asset('assets/img/qris.png') }}" alt="QRIS Futsal" class="img-fluid" style="max-height: 250px;">
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label" for="buktiMembership">
                                        Upload Bukti Bayar Membership
                                    </label>
                                    <input class="form-control" type="file" id="buktiMembership" name="bukti_membership" accept="image/*,.pdf" required />
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-5">
                            <div class="order-summary membership-summary">
                                <h3 class="h5 mb-3">Ringkasan Membership</h3>
                                <ul class="list-unstyled mb-3 small">
                                    <li>Paket Terpilih: <span id="lblPaketMember">-</span></li>
                                    <li>Masa Aktif: <span id="lblMasaAktif">-</span></li>
                                </ul>
                                <div class="estimated-total mb-3">
                                    Total Harga
                                    <strong id="estimasiMemberText">Rp149.000</strong>
                                </div>
                                <button class="btn btn-accent w-100" id="btnDaftarMembership" type="button" onclick="submitMembership()">
                                    Kirim Data Membership
                                </button>
                                <small class="text-secondary d-block mt-2">
                                    Format file bukti: JPG, PNG. Maksimal 2MB.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Modal Penawaran Member -->
    <div class="modal fade" id="modalTawaranMember" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title"><i class="bi bi-star-fill text-warning me-2"></i>Tawaran Spesial Member!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <img src="{{ asset('assets/img/undraw_discount.svg') }}" onerror="this.style.display='none'" class="img-fluid mb-4" width="150" alt="Promo">
                    <h5>Mau Gabung Member Jaya Futsal?</h5>
                    <p class="text-muted">
                        Dapatkan banyak keuntungan, diskon rutin, dan prioritas booking saat turnamen atau event!
                    </p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center pb-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" id="btnTolakMember">Tidak, Cuma Booking</button>
                    <button type="button" class="btn btn-warning px-4 rounded-pill text-dark fw-bold" id="btnTerimaMember">Ya, Saya Mau!</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Set harga dinamis menggunakan Blade: Jika member aktif dapat harga 80RB, jika bukan 100RB
        window.HARGA_SEWA = {{ (Auth::user() && Auth::user()->membership_status === 'active' && Auth::user()->status_member == 1) ? 80000 : 100000 }};
    </script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bookingForm = document.getElementById('bookingForm');
            const btnKonfirmasi = document.getElementById('btnKonfirmasi');
            const btnBatal = document.getElementById('btnBatal');
            const btnBayar = document.getElementById('btnBayar');
            const reservasiIdInput = document.getElementById('reservasiId');
            
            // Override window.alert sementara untuk menangkap notif dari app.js jika perlu
            // (Opsional, karena kode fetch kita sendiri yang urus prosesnya)

            const getCsrf = () => bookingForm.querySelector('input[name="_token"]').value;

            let wantMembership = false;
            let tawaranModal = null;
            
            const isUserMember = {{ (empty(Auth::user()->membership_type) || Auth::user()->membership_status === 'rejected') ? 'false' : 'true' }};

            // --- 1. Buat Pesanan ---
            btnKonfirmasi?.addEventListener('click', async () => {
                if (!isUserMember) {
                    if (!tawaranModal) {
                        tawaranModal = new bootstrap.Modal(document.getElementById('modalTawaranMember'));
                    }
                    tawaranModal.show();
                    return; // pending until modal interacted
                } else {
                    processBooking(false);
                }
            });

            document.getElementById('btnTolakMember')?.addEventListener('click', () => {
                if(tawaranModal) tawaranModal.hide();
                processBooking(false);
            });

            document.getElementById('btnTerimaMember')?.addEventListener('click', () => {
                if(tawaranModal) tawaranModal.hide();
                
                // Tampilkan form membership tanpa membuat reservasi
                const mForm = document.getElementById('membership-form');
                mForm.classList.remove('d-none');
                mForm.scrollIntoView({ behavior: 'smooth' });
                
                // Nonaktifkan tombol booking
                if(btnKonfirmasi) btnKonfirmasi.disabled = true;
            });

            async function processBooking() {
                const formData = new FormData(bookingForm);
                formData.append('metode_pembayaran', 'Belum bayar');

                try {
                    const res = await fetch("{{ route('reservasi.buat') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrf(),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();
                    if (!res.ok) {
                        let errorMsg = data.message || 'Gagal membuat reservasi.';
                        if (data.errors) {
                            const firstError = Object.values(data.errors)[0][0];
                            if (firstError) errorMsg = firstError;
                        }
                        alert(errorMsg);
                        return;
                    }

                    reservasiIdInput.value = data.reservasi.id;
                    toastr.success(data.message);
                    
                    setSummary("Pesanan terkonfirmasi");
                    setMaxStep(3);
                    setStep(3);

                    setStep(3);

                } catch (err) {
                    console.error(err);
                }
            }

            // Update perhitungan Member UI
            const selectPaketMember = document.getElementById('paketMember');
            const lblPaketMember = document.getElementById('lblPaketMember');
            const lblMasaAktif = document.getElementById('lblMasaAktif');
            const estimasiMemberText = document.getElementById('estimasiMemberText');

            function updateMemberUI() {
                if(!selectPaketMember) return;
                const val = selectPaketMember.value;
                let nama = 'Basic';
                let aktif = '1 Bulan';
                if(val == '299000') { nama = 'Pro Team'; aktif = '2 Bulan'; }
                if(val == '449000') { nama = 'Elite League'; aktif = '6 Bulan'; }
                
                if(lblPaketMember) lblPaketMember.innerHTML = `<b>${nama}</b>`;
                if(lblMasaAktif) lblMasaAktif.innerHTML = `<b>${aktif}</b>`;
                if(estimasiMemberText) estimasiMemberText.textContent = "Rp" + new Intl.NumberFormat("id-ID").format(val);
            }
            selectPaketMember?.addEventListener('change', updateMemberUI);
            updateMemberUI();

            // Submit Membership
            window.submitMembership = async function() {
                const f = document.getElementById('formDaftarMembership');
                const btn = document.getElementById('btnDaftarMembership');
                const formData = new FormData(f);
                
                if(!document.getElementById('buktiMembership').files.length) {
                    alert("Harap upload bukti pembayaran membership!");
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = 'Mengirim...';

                try {
                    const res = await fetch("{{ route('membership.register') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrf(),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    const d = await res.json();

                    if (!res.ok) {
                        let eMsg = d.message || 'Gagal.';
                        alert(eMsg);
                    } else {
                        toastr.success(d.message);
                        f.reset();
                        document.getElementById('membership-form').classList.add('d-none');
                        setTimeout(() => {
                            window.location.href = "{{ route('profile') }}"; 
                        }, 2000);
                    }
                } catch(e) {
                    console.error(e);
                }
                btn.disabled = false;
                btn.innerHTML = 'Kirim Data Membership';
            };

            // --- 2. Proses Pembayaran ---
            btnBayar?.addEventListener('click', async () => {
                const reservasiId = reservasiIdInput.value;
                if (!reservasiId) {
                    alert('Reservasi belum dibuat (ID kosong).');
                    return;
                }

                const bukti = document.getElementById('buktiReservasi').files[0];
                const metode = document.getElementById('metodeBayar').value;

                if(!bukti) {
                     // alert sudah di handle app.js, skip req
                     return;
                }

                const fd = new FormData();
                fd.append('metode_pembayaran', metode);
                fd.append('bukti_pembayaran', bukti);
                
                // Gunakan id untuk di-inject ke dalam dynamic URL
                try {
                    const res = await fetch(`{{ url('/reservasi') }}/${reservasiId}/bayar`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': getCsrf(),
                            'Accept': 'application/json'
                        },
                        body: fd
                    });

                    const data = await res.json();
                    if (!res.ok) {
                        const errContainer = document.getElementById('buktiReservasiError');
                        if (data.errors && data.errors.bukti_pembayaran && errContainer) {
                            errContainer.textContent = data.errors.bukti_pembayaran[0];
                            document.getElementById('buktiReservasi').classList.add('is-invalid');
                        } else {
                            alert(data.message || 'Gagal upload pembayaran.');
                        }
                        return;
                    }
                    
                    const errContainer = document.getElementById('buktiReservasiError');
                    if (errContainer) errContainer.textContent = '';
                    document.getElementById('buktiReservasi').classList.remove('is-invalid');
                    toastr.success(data.message);
                } catch (err) {
                    console.error(err);
                }
            });

            // --- 3. Batal Pesanan ---
            btnBatal?.addEventListener('click', async () => {
                const reservasiId = reservasiIdInput.value;
                if (!reservasiId) {
                    alert('Reservasi belum dibuat (ID kosong).');
                    return;
                }

                try {
                    const res = await fetch(`{{ url('/reservasi') }}/${reservasiId}/batal`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': getCsrf(),
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();
                    if (!res.ok) {
                        alert(data.message || 'Gagal membatalkan.');
                        return;
                    }
                    toastr.success(data.message);
                } catch (err) {
                    console.error(err);
                }
            });

            // --- 4. Cek Ketersediaan Jadwal AJAX ---
            const tanggalMainInput = document.getElementById('tanggalMain');
            const jamSelesaiSelect = document.getElementById('jamSelesaiMain');

            async function handleTanggalChange() {
                const tanggalPilih = tanggalMainInput?.value;
                const jamMainSelect = document.getElementById('jamMain');
                const errMessage = document.getElementById('tanggalError');
                const successMessage = document.getElementById('tanggalSuccess');

                // Reset state
                jamMainSelect.innerHTML = '<option value="">Memuat...</option>';
                jamMainSelect.disabled = true;
                if (jamSelesaiSelect) {
                    jamSelesaiSelect.innerHTML = '<option value="">Pilih jam mulai terlebih dahulu...</option>';
                    jamSelesaiSelect.disabled = true;
                }
                errMessage.classList.add('d-none');
                successMessage.classList.add('d-none');

                if (!tanggalPilih) return;

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const pickedDate = new Date(tanggalPilih);

                if (pickedDate < today) {
                    errMessage.textContent = 'Jangan pilih hari yang sudah berlalu.';
                    errMessage.classList.remove('d-none');
                    jamMainSelect.innerHTML = '<option value="">Tanggal sudah berlalu</option>';
                    jamMainSelect.disabled = true;
                    if (jamSelesaiSelect) {
                        jamSelesaiSelect.innerHTML = '<option value="">Tanggal sudah berlalu</option>';
                        jamSelesaiSelect.disabled = true;
                    }
                    return;
                }

                errMessage.textContent = 'Maaf, jadwal di tanggal ini tidak tersedia / penuh.';

                let jadwalOperasional = [];

                try {
                    const response = await fetch(`/reservasi/cek-jadwal?tanggal=${tanggalPilih}`);
                    const result = await response.json();
                    
                    jadwalOperasional = result.data || [];

                    jamMainSelect.innerHTML = ''; // Kosongkan
                    
                    if(jadwalOperasional.length > 0) {
                        let adaTersedia = false;
                        
                        // Loop data jadwal yang tersedia ke option select
                        jadwalOperasional.forEach(j => {
                            const opt = document.createElement('option');
                            opt.value = j.jam; 

                            if (j.status === 'Tersedia') {
                                opt.textContent = `${j.jam} (Tersedia)`;
                                adaTersedia = true;
                            } else {
                                opt.textContent = `${j.jam} - ${j.keterangan}`;
                                opt.disabled = true;
                            }
                            
                            jamMainSelect.appendChild(opt);
                        });
                        
                        // Simpan data operasional SEBELUM auto-select agar updateJamSelesai() bisa akses
                        jamMainSelect.dataset.operasional = JSON.stringify(jadwalOperasional);

                        if (adaTersedia) {
                            successMessage.classList.remove('d-none');
                            jamMainSelect.disabled = false;
                            
                            // Pilih otomatis option pertama yang tidak disabled
                            for (let i = 0; i < jamMainSelect.options.length; i++) {
                                if (!jamMainSelect.options[i].disabled) {
                                    jamMainSelect.selectedIndex = i;
                                    break;
                                }
                            }
                            
                            // Langsung panggil fungsi update jam selesai (lebih reliable daripada dispatchEvent)
                            updateJamSelesai();
                        } else {
                            errMessage.classList.remove('d-none');
                            jamMainSelect.innerHTML = '<option value="">Semua jadwal penuh</option>';
                            jamMainSelect.disabled = true;
                        }

                    } else {
                        errMessage.classList.remove('d-none');
                        jamMainSelect.innerHTML = '<option value="">Tidak ada jadwal / penuh</option>';
                        jamMainSelect.disabled = true;
                    }

                } catch(err) {
                    console.error(err);
                    jamMainSelect.innerHTML = '<option value="">Gagal memuat jadwal</option>';
                }
                
                // dataset.operasional sudah di-set sebelum auto-select di atas
            }

            tanggalMainInput?.addEventListener('change', handleTanggalChange);

            // Fungsi untuk update opsi Jam Selesai berdasarkan Jam Mulai yang dipilih
            function updateJamSelesai() {
                const jamMainSelect = document.getElementById('jamMain');
                const jamMainVal = jamMainSelect?.value;
                const jamSelesaiSelect = document.getElementById('jamSelesaiMain');
                
                jamSelesaiSelect.innerHTML = '';
                jamSelesaiSelect.disabled = true;
                
                if (!jamMainVal) return;
                
                const jadwalOperasional = JSON.parse(jamMainSelect.dataset.operasional || '[]');
                
                // Cari index jam mulai
                const startIndex = jadwalOperasional.findIndex(j => j.jam === jamMainVal);
                if (startIndex === -1) return;
                
                jamSelesaiSelect.disabled = false;
                
                // Maksimal booking mungkin bisa kita batasi misal 1-5 jam, pastikan jam berurut tersedia
                let maxDurasi = 5; 
                let curDurasi = 1;
                
                for (let i = startIndex + 1; i <= jadwalOperasional.length; i++) {
                    if (curDurasi > maxDurasi) break;
                    
                    // Kita bisa ambil jam selanjutnya. Jika mencapai jam penutup (misal index out of bound), kita gunakan jam + 1.
                    let jamSelesaiStr = '';
                    if (i < jadwalOperasional.length) {
                        // Cek apakah slot jam ini tersedia, kalau penuh langsung break (tidak bisa di-book sampai sini)
                        if (jadwalOperasional[i].status !== 'Tersedia') break;
                        jamSelesaiStr = jadwalOperasional[i].jam;
                    } else {
                        // Hitung manual untuk jam tutup (setelah slot terakhir)
                        let lastJam = parseInt(jadwalOperasional[i-1].jam.split(':')[0]);
                        jamSelesaiStr = (lastJam + 1).toString().padStart(2, '0') + ':00';
                    }
                    
                    const opt = document.createElement('option');
                    opt.value = jamSelesaiStr;
                    opt.textContent = `${jamSelesaiStr} (${curDurasi} Jam)`;
                    jamSelesaiSelect.appendChild(opt);
                    
                    curDurasi++;
                }

                jamSelesaiSelect.dispatchEvent(new Event('change'));
            }

            // Update Jam Selesai saat Jam Mulai dipilih
            document.getElementById('jamMain')?.addEventListener('change', updateJamSelesai);
            
            document.getElementById('jamSelesaiMain')?.addEventListener('change', function() {
                // Update hal lain jika diperlukan
            });

            if (tanggalMainInput?.value) {
                handleTanggalChange();
            }
        });
    </script>
@endpush
