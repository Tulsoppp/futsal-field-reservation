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
                        <h2 class="h4 mb-0">
                             User</h2>
                        <span class="badge text-bg-dark">Step by Step</span>
                    </div>
                    <p class="text-dark-70 mb-3 booking-caption">
                        Ikuti urutan ini: isi jadwal, cek ringkasan otomatis, lalu konfirmasi dan lakukan
                        pembayaran.
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
                                    <input type="hidden" id="reservasiId" value="">
                                    <input type="hidden" id="id_jadwal" name="id_jadwal" value="1">
                                    <div class="col-12 mb-3">
                                        <div class="form-hint-card">
                                                <strong>Mulai dari sini:</strong> isi tanggal, jam, dan durasi.
                                            Ringkasan pesanan akan muncul di langkah berikutnya.
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="booking-input-group row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="tanggalMain">Tanggal Main</label>
                                                <input class="form-control" type="date" id="tanggalMain" name="tanggal" required />
                                                <div id="tanggalError" class="invalid-feedback d-none" style="display: block;">
                                                    Maaf, jadwal di tanggal ini tidak tersedia / penuh.
                                                </div>
                                                <div id="tanggalSuccess" class="valid-feedback d-none" style="display: block; color: green;">
                                                    Jadwal tersedia! Silakan pilih jam mulai.
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="durasiMain">Durasi</label>
                                                <select class="form-select" id="durasiMain" name="durasi_jam">
                                                    <option value="1">1 Jam</option>
                                                    <option value="2">2 Jam</option>
                                                    <option value="3">3 Jam</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label" for="jamMain">Jam Mulai</label>
                                                <select class="form-select" id="jamMain" name="jam_mulai" disabled>
                                                    <option value="">Pilih Tanggal Main Terlebih Dahulu...</option>
                                                </select>
                                            </div>
                                 
                                            <div class="col-12">
                                                <label class="form-label" for="catatan">Catatan Tambahan</label>
                                                <textarea class="form-control" id="catatan" rows="3" name="catatan" placeholder="Contoh: sparing internal, butuh bola 2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button class="btn btn-accent" id="btnToConfirm" type="button">
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
                                        <span class="badge text-bg-success" id="bookingStatusBadge">Aktif</span>
                                    </p>
                                    <p class="mb-4" id="bookingStatusText">Pembayaran berhasil. Booking kamu aktif.</p>
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
                                    <div class="mb-3">
                                        <label class="form-label" for="metodeBayar">Metode Pembayaran</label>
                                        <select class="form-select" id="metodeBayar" name="metode_pembayaran">
                                            <option value="Transfer Bank">Transfer Bank</option>
                                            <option value="QRIS">QRIS</option>
                                            <option value="E-Wallet">E-Wallet</option>
                                        </select>
                                    </div>
                                    <div class="upload-proof mb-3">
                                        <label class="form-label" for="buktiReservasi">
                                            Upload Bukti Pembayaran Reservasi
                                        </label>
                                        <input class="form-control" type="file" id="buktiReservasi" accept="image/*,.pdf" name="bukti_pembayaran" />
                                        <small class="text-secondary d-block mt-2" id="buktiReservasiInfo">
                                            Belum ada file dipilih.
                                        </small>
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
                        <h2 class="h4 mb-0">Melihat Riwayat Booking</h2>
                        <span class="badge text-bg-secondary">3 Data Terakhir</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Lapangan</th>
                                    <th>Durasi</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayat ?? [] as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->jadwal->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $item->jadwal->nama_lapangan ?? 'Lapangan Utama' }}</td>
                                        <td>{{ $item->durasi_jam }} Jam</td>
                                        <td>Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($item->status === 'dibayar')
                                                <span class="badge text-bg-success">Aktif</span>
                                            @elseif ($item->status === 'dibatalkan')
                                                <span class="badge text-bg-danger">Dibatalkan</span>
                                            @else
                                                <span class="badge text-bg-warning">Menunggu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-secondary">Belum ada data booking.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="membership-form">
                <div class="panel-box">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="h4 mb-0">Daftar Membership</h2>
                        <span class="badge text-bg-success">Paket Tim</span>
                    </div>
                    <p class="text-dark-70 mb-4">
                        Isi data tim, pilih paket, lalu upload bukti pembayaran membership.
                    </p>
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <form class="booking-input-group row g-3" id="membershipForm">
                                <div class="col-md-6">
                                    <label class="form-label" for="paketMember">Pilih Paket</label>
                                    <select class="form-select" id="paketMember">
                                        <option value="149000">Basic - Rp149.000</option>
                                        <option value="299000">Pro Team - Rp299.000</option>
                                        <option value="449000">Elite League - Rp449.000</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="metodeBayarMember">Metode Pembayaran</label>
                                    <select class="form-select" id="metodeBayarMember">
                                        <option>Transfer Bank</option>
                                        <option>QRIS</option>
                                        <option>E-Wallet</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="namaPIC">Nama PIC</label>
                                    <input class="form-control" type="text" id="namaPIC"
                                        placeholder="Nama penanggung jawab" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="kontakPIC">Kontak PIC</label>
                                    <input class="form-control" type="text" id="kontakPIC"
                                        placeholder="08xxxxxxxxxx" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="buktiMembership">
                                        Upload Bukti Pembayaran Membership
                                    </label>
                                    <input class="form-control" type="file" id="buktiMembership"
                                        accept="image/*,.pdf" />
                                    <small class="text-secondary d-block mt-2" id="buktiMembershipInfo">
                                        Belum ada file dipilih.
                                    </small>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-5">
                            <div class="order-summary membership-summary">
                                <h3 class="h5 mb-3">Ringkasan Membership</h3>
                                <ul class="list-unstyled mb-3 small">
                                    <li>Paket: Menunggu pilihan</li>
                                    <li>Status: Menunggu pembayaran</li>
                                    <li>Aktivasi: Maks. 1x24 jam setelah verifikasi</li>
                                </ul>
                                <div class="estimated-total mb-3">
                                    Total Membership
                                    <strong id="estimasiMember">Rp149.000</strong>
                                </div>
                                <button class="btn btn-accent w-100" id="btnDaftarMembership" type="button">
                                    Kirim Data Membership
                                </button>
                                <small class="text-secondary d-block mt-2">
                                    Format file bukti: JPG, PNG, atau PDF. Maksimal 2MB.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection

@push('scripts')
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

            // --- 1. Buat Pesanan ---
            btnKonfirmasi?.addEventListener('click', async () => {
                // Di app.js sudah pindah step, sekarang kirim data
                const formData = new FormData(bookingForm);
                
                // Ambil harga per jam dari atribut dataset option yang dipilih
                const jamMainSelect = document.getElementById('jamMain');
                const selectedOption = jamMainSelect.options[jamMainSelect.selectedIndex];
                const hargaPerJam = selectedOption ? parseFloat(selectedOption.dataset.harga) || 120000 : 120000;
                
                // Hitung total_harga
                const durasiJam = parseFloat(document.getElementById('durasiMain').value) || 1;
                const totalHarga = durasiJam * hargaPerJam;
                
                formData.append('total_harga', totalHarga);
                // default metode aja biar lolos validasi awal, akan diupdate pas bayar
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
                        console.log(data);
                        return;
                    }

                    // Set reservasi id dari response untuk dipake step selanjutnya
                    reservasiIdInput.value = data.reservasi.id;
                    toastr.success(data.message);
                    
                    // Trigger klik tombol konfirmasi di app.js untuk move step secara frontend
                    setSummary("Pesanan terkonfirmasi");
                    setMaxStep(3);
                    setStep(3);

                } catch (err) {
                    console.error(err);
                }
            });

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
                        alert(data.message || 'Gagal upload pembayaran.');
                        return;
                    }
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
            document.getElementById('tanggalMain')?.addEventListener('change', async function() {
                const tanggalPilih = this.value;
                const jamMainSelect = document.getElementById('jamMain');
                const errMessage = document.getElementById('tanggalError');
                const successMessage = document.getElementById('tanggalSuccess');

                // Reset state
                jamMainSelect.innerHTML = '<option value="">Memuat...</option>';
                jamMainSelect.disabled = true;
                errMessage.classList.add('d-none');
                successMessage.classList.add('d-none');

                if(!tanggalPilih) return;

                try {
                    const response = await fetch(`/reservasi/cek-jadwal?tanggal=${tanggalPilih}`);
                    const result = await response.json();

                    jamMainSelect.innerHTML = ''; // Kosongkan
                    
                    if(result.data && result.data.length > 0) {
                        successMessage.classList.remove('d-none');
                        jamMainSelect.disabled = false;
                        
                        // Loop data jadwal yang tersedia ke option select
                        result.data.forEach(j => {
                            const opt = document.createElement('option');
                            opt.value = j.jam_mulai; // yang dikirim jam mulai

                            // Ambil string jam:menit saja misalnya 08:00:00 jadi 08:00
                            const formatMulai = j.jam_mulai.substring(0, 5); 
                            const formatSelesai = j.jam_selesai.substring(0, 5); 

                            opt.textContent = `${formatMulai} - ${formatSelesai} (${j.nama_lapangan}) - Rp${new Intl.NumberFormat('id-ID').format(j.harga_per_jam)}/jam`;
                            opt.dataset.id_jadwal = j.id; 
                            opt.dataset.harga = j.harga_per_jam; 
                            jamMainSelect.appendChild(opt);
                        });
                        
                        // Auto isi hidden input id_jadwal
                        document.getElementById('id_jadwal').value = jamMainSelect.options[0].dataset.id_jadwal;
                        
                        // Trigger perubahan jam untuk mengupdate ringkasan
                        jamMainSelect.dispatchEvent(new Event('change'));

                    } else {
                        // Jika kosong, tampilkan error
                        errMessage.classList.remove('d-none');
                        jamMainSelect.innerHTML = '<option value="">Tidak ada jadwal / penuh</option>';
                        jamMainSelect.disabled = true;
                    }

                } catch(err) {
                    console.error(err);
                    jamMainSelect.innerHTML = '<option value="">Gagal memuat jadwal</option>';
                }
            });

            // Update ID jadwal hidden jika user mengganti jam
            document.getElementById('jamMain')?.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if(selectedOption && selectedOption.dataset.id_jadwal) {
                    document.getElementById('id_jadwal').value = selectedOption.dataset.id_jadwal;
                }
            });
        });
    </script>
@endpush
