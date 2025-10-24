<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dimana role adalah pasien
        $pasiens = User::where('role', 'pasien')->with('poli')->get();
        return view('admin.pasien.index', compact('pasiens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pasien.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //1. membuat validasi
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_ktp' => 'required|string|max:16|unique:users,no_ktp',
            'no_hp' => 'required|string|max:15',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        // dd($data);

        User::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_ktp' => $request->no_ktp,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
        ]);

        return redirect()->route('pasien.index')
            ->with('message', 'Data pasien Berhasil di tambahkan')
            ->with('type', 'success');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $pasien)
    {
        return view('admin.pasien.edit', compact('pasien'));
    }

    /**
     * Update the specified resource in storage.
     * $pasien adalah route model binding jadi yang harus nya kita buat
     * $pasien = User::findOrFail($id); kita bisa membuat menjadi parameter,
     * namun jika menggunakan cara tersebut kita route nya tidak bisa admin/pasien{id}/edit namun
     * seperi admin/pasien/{pasien}/edit
     */

    public function update(Request $request, User $pasien)
    {
        // Validasi data
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_ktp' => 'required|string|max:16|unique:users,no_ktp,' . $pasien->id,
            'no_hp' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:users,email,' . $pasien->id,
            'password' => 'nullable|string|min:6', // opsional
        ]);

        // Data yang akan diupdate
        $updateData = [
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_ktp' => $request->no_ktp,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
        ];

        // Jika password diisi, update juga
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Jalankan update
        $pasien->update($updateData);

        return redirect()->route('pasien.index')
            ->with('message', 'Data pasien Berhasil diubah')
            ->with('type', 'success');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $pasien)
    {
        $pasien->delete();
        return redirect()->route('pasien.index')
            ->with('message', 'Data pasien Berhasil dihapus')
            ->with('type', 'success');
    }
}
