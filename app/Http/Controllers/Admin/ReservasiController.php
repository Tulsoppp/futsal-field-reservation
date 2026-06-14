<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservasiController extends Controller
{
    public function index()
    {
        // 1. Auto-batalkan reservasi 'menunggu' yang sudah melewati batas waktu 1 jam (belum upload bukti)
        $limitTime = Carbon::now()->subHour();
        Reservasi::where('status', 'menunggu')
            ->whereNull('bukti_pembayaran')
            ->where('created_at', '<', $limitTime)
            ->update([
                'status' => 'dibatalkan',
                'catatan' => 'Dibatalkan otomatis oleh sistem (melebihi batas waktu upload bukti pembayaran 1 jam)',
            ]);

        // 2. Auto-selesaikan reservasi yang waktu mainnya sudah lewat
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
        $countMenungguPembayaran = Reservasi::where('status', 'menunggu')->count();
        $countDisetujui = Reservasi::whereIn('status', ['disetujui', 'dibayar'])->count();
        $countSelesai = Reservasi::where('status', 'selesai')->count();

        return view('pages.admin.reservasi', compact(
            'validasi',
            'riwayat',
            'countMenungguBayar',
            'countMenungguPembayaran',
            'countDisetujui',
            'countSelesai'
        ));
    }

    public function terimaPembayaran(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);

        // Hanya bisa menerima pembayaran dari status 'pending' (sudah upload bukti)
        if ($reservasi->status !== 'pending') {
            return redirect()->back()->with('error', 'Reservasi ini tidak dalam status menunggu konfirmasi pembayaran.');
        }

        $reservasi->status = 'disetujui';
        $reservasi->save();

        return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function tolakPembayaran(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);

        // Hanya bisa menolak dari status 'pending' atau 'menunggu'
        if (!in_array($reservasi->status, ['pending', 'menunggu'])) {
            return redirect()->back()->with('error', 'Reservasi ini tidak dapat ditolak dari status saat ini.');
        }

        $reservasi->status = 'dibatalkan';
        $reservasi->catatan = $reservasi->catatan
            ? $reservasi->catatan . ' | Ditolak oleh admin.'
            : 'Ditolak oleh admin.';
        $reservasi->save();

        return redirect()->back()->with('success', 'Pembayaran ditolak / reservasi dibatalkan.');
    }

    public function selesaikanReservasi(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);

        // Hanya bisa menyelesaikan dari status 'disetujui' atau 'dibayar'
        if (!in_array($reservasi->status, ['disetujui', 'dibayar'])) {
            return redirect()->back()->with('error', 'Hanya reservasi yang sudah disetujui yang dapat diselesaikan.');
        }

        $reservasi->status = 'selesai';
        $reservasi->save();

        return redirect()->back()->with('success', 'Reservasi diselesaikan.');
    }
}
