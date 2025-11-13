<?php

namespace App\Http\Controllers\Pasien;

use App\Models\Poli;
use App\Models\JadwalPeriksa;
use App\Models\DaftarPoli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PoliController extends Controller
{
    public function get()
    {
        $user = Auth::user();
        $polis = Poli::all();
        $jadwals = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar', compact('user', 'polis', 'jadwals'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan' => 'nullable|string',
            'id_pasien' => 'required|exists:users,id',
        ]);

        // Cek apakah pasien sudah mendaftar pada jadwal yang sama
        $alreadyExists = DaftarPoli::where('id_pasien', $request->id_pasien)
                                   ->where('id_jadwal', $request->id_jadwal)
                                   ->exists();

        if ($alreadyExists) {
            return redirect()->back()
                ->with('message', 'Anda sudah terdaftar di jadwal ini!')
                ->with('type', 'warning');
        }

        // Hitung antrian
        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count();

        // Simpan data pendaftaran
        DaftarPoli::create([
            'id_jadwal' => $request->id_jadwal,
            'id_pasien' => $request->id_pasien,
            'keluhan' => $request->keluhan,
            'no_antrian' => $jumlahSudahDaftar + 1,
        ]);

        return redirect()->back()->with('message', 'Berhasil daftar poli')->with('type', 'success');
    }
}

