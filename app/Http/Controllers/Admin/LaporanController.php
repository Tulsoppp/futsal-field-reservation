<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        $paidStatuses = ['disetujui', 'selesai', 'dibayar'];
        $bulanFilter = request('bulan');
        $filterDate = null;

        if (!empty($bulanFilter)) {
            try {
                $filterDate = Carbon::createFromFormat('Y-m', $bulanFilter);
            } catch (\Exception $ex) {
                $filterDate = null;
            }
        }

        $totalPendapatan = Reservasi::whereIn('status', $paidStatuses)->sum('total_harga');
        $totalTransaksi = Reservasi::whereIn('status', $paidStatuses)->count();
        $totalBatal = Reservasi::where('status', 'dibatalkan')->count();
        $rataRata = $totalTransaksi > 0 ? (int) round($totalPendapatan / $totalTransaksi) : 0;

        $laporanSewaQuery = Reservasi::with('user')
            ->whereIn('status', array_merge($paidStatuses, ['dibatalkan']));

        if ($filterDate) {
            $laporanSewaQuery->whereYear('tanggal', $filterDate->year)
                ->whereMonth('tanggal', $filterDate->month);
        }

        $laporanSewa = $laporanSewaQuery->orderByDesc('tanggal')->get();

        $keuanganBase = Reservasi::whereIn('status', array_merge($paidStatuses, ['dibatalkan']));
        $keuanganFiltered = clone $keuanganBase;

        if ($filterDate) {
            $keuanganFiltered->whereYear('tanggal', $filterDate->year)
                ->whereMonth('tanggal', $filterDate->month);
        }

        $pendapatanFilter = (clone $keuanganFiltered)->whereIn('status', $paidStatuses)->sum('total_harga');
        $transaksiFilter = (clone $keuanganFiltered)->whereIn('status', $paidStatuses)->count();
        $batalFilter = (clone $keuanganFiltered)->where('status', 'dibatalkan')->count();

        $laporanBulanan = $keuanganFiltered->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m');
            })
            ->map(function ($items) use ($paidStatuses) {
                $label = Carbon::parse($items->first()->tanggal)->translatedFormat('F Y');
                $transaksiLunas = $items->whereIn('status', $paidStatuses)->count();
                $reservasiBatal = $items->where('status', 'dibatalkan')->count();
                $pendapatan = $items->whereIn('status', $paidStatuses)->sum('total_harga');

                return [
                    'label' => $label,
                    'transaksi_lunas' => $transaksiLunas,
                    'reservasi_batal' => $reservasiBatal,
                    'pendapatan' => $pendapatan,
                ];
            })
            ->sortKeysDesc()
            ->values();

        $availableMonths = Reservasi::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->whereNotNull('tanggal')
            ->distinct()
            ->orderByDesc('bulan')
            ->pluck('bulan');

        $filterLabel = $filterDate ? $filterDate->translatedFormat('F Y') : 'Semua Bulan';

        return view('pages.admin.laporan', compact(
            'totalPendapatan',
            'totalTransaksi',
            'totalBatal',
            'rataRata',
            'laporanSewa',
            'laporanBulanan',
            'availableMonths',
            'bulanFilter',
            'filterLabel',
            'pendapatanFilter',
            'transaksiFilter',
            'batalFilter'
        ));
    }
}
