<?php

namespace App\Models;

use App\Models\Reservasi;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    //

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_lapangan',
        'harga_per_jam',
        'status',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
    ];

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class, 'id_jadwal', 'id');
    }

    // menyimpan informasi detail lapangaan (cek status lapangan)
    public function cekStatus(): bool {
        return $this->status === 'tersedia';
    }

    // cek ketersediaan slot pada tanggal dan jam tertentu
   public static function cekKetersediaan(string $tanggal, string $jamMulai, string $jamSelesai): bool{
        $adaBentrok = self::where('tanggal', $tanggal)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->whereRaw('jam_mulai < ? AND jam_selesai > ?', [$jamSelesai, $jamMulai]);
            })
            ->where('status', 'tersedia')
            ->exists();

        return !$adaBentrok;
    }
}
