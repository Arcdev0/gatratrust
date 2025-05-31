<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListProsesController extends Controller
{
 public function index()
 {
        $listProses = \App\Models\ListProses::all();
        $listKerjaan = \App\Models\Kerjaan::all();
        return view('listproses.index', compact('listProses', 'listKerjaan'));
 }

 public function store(Request $request)
 {
     $request->validate([
         'nama_proses' => 'required|string|max:255',
     ]);

     $listProses = new \App\Models\ListProses();
     $listProses->nama_proses = $request->nama_proses;
     $listProses->save();

     return redirect()->route('listproses.index')->with('success', 'Proses berhasil ditambahkan.');
 }
 public function update(Request $request, $id)
 {
     $request->validate([
         'nama_proses' => 'required|string|max:255',
     ]);

     $listProses = \App\Models\ListProses::findOrFail($id);
     $listProses->nama_proses = $request->nama_proses;
     $listProses->save();

     return redirect()->route('listproses.index')->with('success', 'Proses berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
        $listProses = \App\Models\ListProses::findOrFail($id);
        $listProses->delete();
    
        return redirect()->route('listproses.index')->with('success', 'Proses berhasil dihapus.');
    }
}
