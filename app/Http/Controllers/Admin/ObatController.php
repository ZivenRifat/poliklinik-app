<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Obat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dimana role adalah obat
        $obats = Obat::all();
        return view('admin.obat.index', compact('obats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.obat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //1. membuat validasi
        $data = $request->validate([
            'nama_obat' => 'required|string',
            'kemasan' => 'required|string',
            'harga' => 'required|integer',
        ]);
        // dd($data);

        Obat::create([
            'nama_obat' => $request->nama_obat,
            'kemasan' => $request->kemasan,
            'harga' => $request->harga,
        ]);

        return redirect()->route('obat.index')
            ->with('message', 'Data obat Berhasil di tambahkan')
            ->with('type', 'success');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $obat = Obat::findOrFail($id);

        return view('admin.obat.edit', compact('obat'));
    }

    /**
     * Update the specified resource in storage.
     * $obat adalah route model binding jadi yang harus nya kita buat
     * $obat = User::findOrFail($id); kita bisa membuat menjadi parameter,
     * namun jika menggunakan cara tersebut kita route nya tidak bisa admin/obat{id}/edit namun
     * seperi admin/obat/{obat}/edit
     */

    public function update(Request $request, string $id)
    {
        // Validasi data
        $request->validate([
            'nama_obat' => 'required|string',
            'kemasan' => 'required|string',
            'harga' => 'required|integer',
        ]);

        $obat = Obat::findOrFail($id);
        // Data yang akan diupdate
        $obat->update ([
            'nama_obat' => $request->nama_obat,
            'kemasan' => $request->kemasan,
            'harga' => $request->harga,
        ]);

        return redirect()->route('obat.index')
            ->with('message', 'Data obat Berhasil diubah')
            ->with('type', 'success');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();
        return redirect()->route('obat.index')
            ->with('message', 'Data obat Berhasil dihapus')
            ->with('type', 'success');
    }
}
