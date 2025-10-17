<?php

namespace App\Http\Controllers\Admin;

use App\Models\Poli;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;



class PoliController extends Controller
{
    public function index()
    {
        $polis = Poli::all();
        return view('polis.index', compact('polis'));
    }

    public function create()
    {
        return view('polis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_poli' => 'required',
            'keterangan' => 'nullable',
        ]);

        Poli::create($validated);
        return redirect()->route('polis.index')->with('success', 'Poli berhasil di tambahkan')->with('type', 'success');
    }

    public function edit($id)
    {
        $poli = Poli::findOrFail($id);
        return view('polis.edit', compact('poli'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_poli' => 'required',
            'keterangan' => 'nullable',
        ]);

        $poli = Poli::findOrFail($id);
        $poli->update($validated);
        return redirect()->route('polis.index')->with('success', 'Polis berhasil di update');
    }

    public function destroy($id)
    {
        $poli = Poli::findOrFail($id);
        $poli->delete();
        return redirect()->route('polis.index')->with('success', 'Polis Berhasil di hapus !');
    }
}
