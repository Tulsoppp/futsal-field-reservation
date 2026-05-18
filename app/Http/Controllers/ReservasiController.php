<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservasiController extends Controller
{
    //
    public function tampilRiwayatBooking()
    {
        $riwayat = Reservasi::with('jadwal')
            ->where('id_user', Auth::id())
            ->latest()
            ->take(3)
            ->get();

        $lastReservasiId = session('last_reservasi_id');

        return view('pages.user.reservasi', compact('riwayat', 'lastReservasiId'));
    }

    public function buatPesanan(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Anda harus login terlebih dahulu untuk membuat pesanan.'], 401);
            }
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu untuk membuat pesanan.');
        }

        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'durasi_jam' => 'required|integer|min:1|max:6',
            'catatan' => 'nullable|string|max:500',
        ]);

        $jamSelesai = Carbon::parse($validatedData['jam_mulai'])
            ->addHours((int) $validatedData['durasi_jam'])
            ->format('H:i:s');

        // Kita cukup mencari Jadwal berdasarkan ID atau jam mulai, karena admin yang menentukan slot
        $jadwal = Jadwal::where('tanggal', $validatedData['tanggal'])
            ->where('jam_mulai', 'like', $validatedData['jam_mulai'] . '%')
            ->where('status', 'tersedia')
            ->first();

        if (!$jadwal) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Maaf, slot yang Anda pilih tidak tersedia. Silakan pilih waktu lain.'], 404);
            }
            return redirect()->back()->with('error', 'Maaf, slot yang Anda pilih tidak tersedia. Silakan pilih waktu lain.');
        }

        // proses menghitung total harga berdasarkan durasi jam yang dipesan dan harga per jam dari jadwal
        $totalHarga = $validatedData['durasi_jam'] * $jadwal->harga_per_jam;

        $reservasi = Reservasi::create([
            'id_user' => Auth::id(),
            'id_jadwal' => $jadwal->id,
            'durasi_jam' => $validatedData['durasi_jam'],
            'total_harga' => $totalHarga,
            'status' => 'menunggu',
            'catatan' => $validatedData['catatan'],
        ]);

        // proses update kolom di table jadwal   
        $jadwal->status = 'tidak_tersedia';
        $jadwal->save();


        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.',
                'reservasi' => $reservasi
            ], 200);
        }

        return redirect()
            ->route('reservasi.riwayat')
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

    public function cekJadwalTersedia(Request $request)
    {
        $tanggal = $request->query('tanggal');

        // Cari jadwal yang statusnya 'tersedia' di tanggal yang dipilih
        $jadwalTersedia = \App\Models\Jadwal::where('tanggal', $tanggal)
            ->where('status', 'tersedia')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $jadwalTersedia
        ]);
    }
}
