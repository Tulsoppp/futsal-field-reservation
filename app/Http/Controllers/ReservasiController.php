<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservasiController extends Controller
{
    //
    public function tampilRiwayatBooking()
    {
        $riwayat = Reservasi::where('id_user', Auth::id())
            ->latest()
            ->take(3)
            ->get();

        $unpaidReservasi = Reservasi::where('id_user', Auth::id())
            ->where('status', 'menunggu')
            ->whereNull('bukti_pembayaran')
            ->first();

        $lastReservasiId = session('last_reservasi_id');

        return view('pages.user.reservasi', compact('riwayat', 'lastReservasiId', 'unpaidReservasi'));
    }

    public function riwayatLengkap()
    {
        $semuaRiwayat = Reservasi::where('id_user', Auth::id())
            ->latest()
            ->get();

        return view('pages.user.riwayat-lengkap', compact('semuaRiwayat'));
    }

    public function buatPesanan(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Anda harus login terlebih dahulu untuk membuat pesanan.'], 401);
            }
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu untuk membuat pesanan.');
        }

        // Cek apakah user memiliki tanggungan pembayaran
        $unpaidCount = Reservasi::where('id_user', Auth::id())
            ->where('status', 'menunggu')
            ->whereNull('bukti_pembayaran')
            ->count();

        if ($unpaidCount > 0) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Selesaikan pembayaran di jadwal sebelumnya terlebih dahulu.'], 400);
            }
            return redirect()->back()->with('error', 'Selesaikan pembayaran di jadwal sebelumnya terlebih dahulu.');
        }

        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'jam_selesai' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', 'after:jam_mulai'],
            'catatan' => 'nullable|string|max:500',
        ]);

        $jamMulai = Carbon::parse($validatedData['jam_mulai']);
        $jamSelesai = Carbon::parse($validatedData['jam_selesai']);

        // CEK BENTROK JADWAL
        $bentrok = Reservasi::where('tanggal', $validatedData['tanggal'])
            ->whereNotIn('status', ['dibatalkan']) // Abaikan yang dibatalkan
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->whereTime('jam_mulai', '<=', $jamMulai->format('H:i:s'))
                        ->whereTime('jam_selesai', '>', $jamMulai->format('H:i:s'));
                })
                    ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                        $q->whereTime('jam_mulai', '<', $jamSelesai->format('H:i:s'))
                            ->whereTime('jam_selesai', '>=', $jamSelesai->format('H:i:s'));
                    })
                    ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                        $q->whereTime('jam_mulai', '>=', $jamMulai->format('H:i:s'))
                            ->whereTime('jam_mulai', '<', $jamSelesai->format('H:i:s'));
                    });
            })->exists();

        if ($bentrok) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Maaf, lapangan sudah dibooking pada jam tersebut.'], 400);
            }
            return redirect()->back()->with('error', 'Maaf, lapangan sudah dibooking pada jam tersebut. Silakan pilih waktu lain.');
        }

        // Harga Sewa Tetap per jam
        $hargaPerJam = env('HARGA_SEWA_PER_JAM', 100000);

        $user = Auth::user();
        if ($user && $user->membership_status === 'active' && $user->status_member == 1) {
            // Jika user adalah member aktif, berikan harga spesial / potongan!
            $hargaPerJam = 80000;
        }

        $durasiJam = $jamMulai->diffInHours($jamSelesai);
        $totalHarga = $durasiJam * $hargaPerJam;

        $reservasi = Reservasi::create([
            'id_user' => Auth::id(),
            'tanggal' => $validatedData['tanggal'],
            'jam_mulai' => $validatedData['jam_mulai'],
            'jam_selesai' => $validatedData['jam_selesai'],
            'total_harga' => $totalHarga,
            'status' => 'menunggu',
            'catatan' => $validatedData['catatan'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.',
                'reservasi' => $reservasi
            ], 200);
        }

        return redirect()
            ->route('reservasi.index')
            ->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.')
            ->with('last_reservasi_id', $reservasi->id);
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);

        if ($reservasi->id_user !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $reservasi->metode_pembayaran = $validatedData['metode_pembayaran'];
        $reservasi->bukti_pembayaran = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        $reservasi->status = 'dibayar';
        $reservasi->save();

        return response()->json([
            'message' => 'Pembayaran berhasil, Silahkan tunggu konfirmasi dari admin',
            'reservasi' => $reservasi
        ], 200);
    }

    public function batalkanPesanan(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);

        if ($reservasi->id_user !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservasi->status = 'dibatalkan';
        $reservasi->save();

        return response()->json([
            'message' => 'Pesanan berhasil dibatalkan',
            'reservasi' => $reservasi
        ], 200);
    }

    public function cetakTiket($id)
    {
        $reservasi = Reservasi::with('user')->where('id', $id)
            ->where('id_user', Auth::id())
            ->whereIn('status', ['dibayar', 'selesai'])
            ->firstOrFail();

        return view('pages.user.cetak-tiket', compact('reservasi'));
    }

    public function cekJadwalTersedia(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->format('Y-m-d'));

        // Ambil data booking yang SUDAH ADA hari itu dan statusnya bukan dibatalkan
        $bookingHariIni = Reservasi::where('tanggal', $tanggal)
            ->whereNotIn('status', ['dibatalkan'])
            ->get();

        $jamOperasional = [];
        $start = 8; // Buka jam 08:00
        $end = 23; // Tutup jam 23:00

        for ($i = $start; $i <= $end; $i++) {
            $jamFormat = sprintf('%02d:00', $i);
            $status = 'Tersedia';
            $keterangan = '';

            // Cek apakah jam ini masuk di range data booking yang sudah ada
            foreach ($bookingHariIni as $booking) {
                $jamMulaiBooking = (int) substr($booking->jam_mulai, 0, 2);
                $jamSelesaiBooking = (int) substr($booking->jam_selesai, 0, 2);

                if ($i >= $jamMulaiBooking && $i < $jamSelesaiBooking) {
                    $status = 'Sudah Dibooking';
                    $keterangan = "Sudah ada yang booking dari jam " . substr($booking->jam_mulai, 0, 5) . " sampai " . substr($booking->jam_selesai, 0, 5);
                }
            }

            $jamOperasional[] = [
                'jam' => $jamFormat,
                'status' => $status,
                'keterangan' => $keterangan
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $jamOperasional
        ]);
    }
}
