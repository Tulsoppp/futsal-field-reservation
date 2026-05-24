<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CancelUnpaidReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservasi:cancel-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan reservasi yang belum dibayar (status "menunggu") lebih dari 1 jam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limitTime = Carbon::now()->subHour();

        $expiredReservations = Reservasi::where('status', 'menunggu')
            ->where('created_at', '<', $limitTime)
            ->get();

        $count = 0;
        foreach ($expiredReservations as $reservasi) {
            $reservasi->update([
                'status' => 'dibatalkan',
                'catatan' => 'Dibatalkan otomatis oleh sistem (melebihi batas waktu 1 jam)'
            ]);
            $count++;
        }

        if ($count > 0) {
            $this->info("Berhasil membatalkan {$count} reservasi yang kadaluarsa.");
            Log::info("CronJob: Membatalkan {$count} reservasi yang kadaluarsa (lebih dari 1 jam).");
        } else {
            $this->info("Tidak ada reservasi kadaluarsa yang perlu dibatalkan.");
        }
    }
}
