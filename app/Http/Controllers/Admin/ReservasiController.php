<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Illuminate\Http\Request;

class ReservasiController extends Controller
{
    public function index()
    {
        // Auto-selesaikan reservasi yang waktu mainnya sudah lewat
        // (tanggal + jam_selesai < sekarang) dan statusnya masih disetujui/dibayar
        $now = now();
        Reservasi::whereIn('status', ['disetujui', 'dibayar'])
            ->where(function ($query) use ($now) {
                $query->where('tanggal', '<', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('tanggal', '=', $now->toDateString())
                          ->where('jam_selesai', '<=', $now->format('H:i:s'));
                    });
            })
            ->update(['status' => 'selesai']);

        // Query terpisah untuk tabel Validasi (status aktif)
        $validasi = Reservasi::with('user')
            ->whereIn('status', ['menunggu', 'pending', 'disetujui', 'dibayar'])
            ->latest()
            ->paginate(10, ['*'], 'validasi_page');

        // Query terpisah untuk tabel Riwayat (status selesai/batal)
        $riwayat = Reservasi::with('user')
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->paginate(10, ['*'], 'riwayat_page');

        // Hitung status untuk dashboard / card indikator
        $countMenungguBayar = Reservasi::where('status', 'pending')->count();
        $countMenungguBatal = Reservasi::where('status', 'dibatalkan')->count();
        $countDisetujui = Reservasi::whereIn('status', ['disetujui', 'dibayar'])->count();
        $countSelesai = Reservasi::where('status', 'selesai')->count();

        return view('pages.admin.reservasi', compact(
            'validasi',
            'riwayat',
            'countMenungguBayar',
            'countMenungguBatal',
            'countDisetujui',
            'countSelesai'
        ));
    }

    public function terimaPembayaran(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->status = 'disetujui';
        $reservasi->save();

        return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function tolakPembayaran(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->status = 'dibatalkan';
        $reservasi->save();

        return redirect()->back()->with('success', 'Pembayaran ditolak / reservasi dibatalkan.');
    }

    public function selesaikanReservasi(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->status = 'selesai';
        $reservasi->save();

        return redirect()->back()->with('success', 'Reservasi diselesaikan.');
    }
}
