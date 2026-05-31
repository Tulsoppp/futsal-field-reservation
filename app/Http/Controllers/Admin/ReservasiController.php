<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Illuminate\Http\Request;

class ReservasiController extends Controller
{
    public function index()
    {
        // Ambil semua data reservasi beserta user
        $reservasi = Reservasi::with('user')->latest()->get();

        // Hitung status untuk dashboard / card indikator
        $countMenungguBayar = $reservasi->where('status', 'pending')->count();
        $countMenungguBatal = 0; // opsional, jika kamu mau deteksi cancel request (misalkan ada status 'menunggu_batal')
        $countDisetujui = $reservasi->whereIn('status', ['disetujui', 'dibayar'])->count();
        $countSelesai = $reservasi->where('status', 'selesai')->count();

        return view('pages.admin.reservasi', compact(
            'reservasi',
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
