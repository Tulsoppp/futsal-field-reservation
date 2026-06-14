<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Auto-batalkan reservasi 'menunggu' yang sudah melewati batas waktu 1 jam (belum upload bukti)
        $limitTime = Carbon::now()->subHour();
        Reservasi::where('status', 'menunggu')
            ->whereNull('bukti_pembayaran')
            ->where('created_at', '<', $limitTime)
            ->update([
                'status' => 'dibatalkan',
                'catatan' => 'Dibatalkan otomatis oleh sistem (melebihi batas waktu upload bukti pembayaran 1 jam)',
            ]);

        $totalPendapatanBulanIni = Reservasi::whereIn('status', ['disetujui', 'selesai', 'dibayar'])
            ->whereMonth('tanggal', $today->month)
            ->whereYear('tanggal', $today->year)
            ->sum('total_harga');

        $totalReservasiHariIni = Reservasi::whereDate('tanggal', $today)
            ->where('status', '!=', 'dibatalkan')
            ->count();

        $menungguPembayaran = Reservasi::where('status', 'menunggu')->count();

        $totalMemberAktif = User::where('membership_status', 'active')
            ->where('status_member', 1)
            ->count();

        $recentActivities = Reservasi::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.admin.dashboard', compact(
            'totalPendapatanBulanIni',
            'totalReservasiHariIni',
            'menungguPembayaran',
            'totalMemberAktif',
            'recentActivities'
        ));
    }
}
