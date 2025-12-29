<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DaftarPoli;
use App\Models\Obat;
use App\Models\Periksa;
use App\Models\DetailPeriksa;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();
        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }
    public function create($id)
    {
        $obats = Obat::all();
        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_json' => 'required',
            'catatan' => 'nullable|string',
            'biaya_periksa' => 'required|integer',
        ]);

        $obatIds = json_decode($request->obat_json, true);

        foreach ($obatIds as $idObat) {
            $obat = Obat::findOrFail($idObat);

            if ($obat->stok <= 0) {
                return back()
                    ->withInput()
                    ->with('type', 'danger')
                    ->with('message', "Stok obat {$obat->nama_obat} habis!");
            }

            // ⚠️ STOK MENIPIS → TIDAK GAGAL, HANYA PERINGATAN
            if ($obat->stok <= 10 && $obat->stok > 0) {
                session()->flash(
                    'warning',
                    "Perhatian: stok obat {$obat->nama_obat} tersisa {$obat->stok}"
                );
            }
        }


        $periksa = Periksa::create([
            'id_daftar_poli' => $request->id_daftar_poli,
            'tgl_periksa' => now(),
            'catatan' => $request->catatan,
            'biaya_periksa' => $request->biaya_periksa + 50000,
        ]);

        foreach ($obatIds as $idObat) {
            DetailPeriksa::create([
                'id_periksa' => $periksa->id,
                'id_obat' => $idObat,
            ]);

            Obat::where('id', $idObat)->decrement('stok', 1);
        }

        return redirect()->route('periksa-pasien.index')
            ->with('message', 'Data periksa berhasil disimpan.');
    }
}
