<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    //
    public function index() {
        $jadwals = Jadwal::all();
        // dd($jadwals);
        return view('pages.admin.jadwal', compact('jadwals'));  
    }

    public function tampilForm() {
        return view('pages.admin.jadwal-form');
    }

    public function buatJadwal(Request $request) {
        // validasi input
        $validatedData = $request->validate([
            'nama_lapangan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'harga_per_jam' => 'required|numeric',
            'status' => 'required'
        ]);

        // buat jadwal baru
        $jadwal = Jadwal::create($validatedData);

        return redirect()->route('jadwal')->with('success', 'Jadwal berhasil dibuat.');
    }
}
