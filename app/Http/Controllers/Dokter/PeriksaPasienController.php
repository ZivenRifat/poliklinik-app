<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->whereHas('jadwalPeriksa', function ($q) use ($dokterId) {
                $q->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        // 🔒 Cegah pasien diperiksa ulang
        if (Periksa::where('id_daftar_poli', $id)->exists()) {
            return redirect()
                ->route('periksa-pasien.index')
                ->with('type', 'danger')
                ->with('message', 'Pasien sudah diperiksa.');
        }

        $daftarPoli = DaftarPoli::with('pasien')->findOrFail($id);
        $obats = Obat::orderBy('nama_obat')->get();

        return view('dokter.periksa-pasien.create', compact(
            'id',
            'daftarPoli',
            'obats'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'obat_json'      => 'required|json',
            'biaya_periksa'  => 'required|integer|min:0',
            'catatan'        => 'nullable|string',
        ]);

        // ❌ Double submit protection
        if (Periksa::where('id_daftar_poli', $request->id_daftar_poli)->exists()) {
            return redirect()
                ->route('periksa-pasien.index')
                ->with('type', 'danger')
                ->with('message', 'Pasien sudah diperiksa.');
        }

        $obatIds = json_decode($request->obat_json, true);

        if (!is_array($obatIds) || count($obatIds) === 0) {
            return back()->with('type', 'danger')->with('message', 'Minimal pilih 1 obat.');
        }

        DB::beginTransaction();

        try {
            $periksa = Periksa::create([
                'id_daftar_poli' => $request->id_daftar_poli,
                'tgl_periksa'    => now(),
                'catatan'        => $request->catatan,
                'biaya_periksa'  => $request->biaya_periksa + 50000, // biaya dokter
            ]);

            foreach ($obatIds as $idObat) {
                $obat = Obat::lockForUpdate()->findOrFail($idObat);

                if ($obat->stok <= 0) {
                    throw new \Exception("Stok obat {$obat->nama_obat} habis!");
                }

                $obat->decrement('stok');

                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat'    => $obat->id,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('periksa-pasien.index')
                ->with('type', 'success')
                ->with('message', 'Pasien berhasil diperiksa.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('type', 'danger')
                ->with('message', $e->getMessage());
        }
    }
}
