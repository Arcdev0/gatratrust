<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $spks = Spk::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('nomor', 'like', "%{$search}%")
                        ->orWhere('pegawai_nama', 'like', "%{$search}%")
                        ->orWhere('tujuan_dinas', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('spk.index', compact('spks', 'search'));
    }

    public function create()
    {
        return view('spk.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Spk::create($validated);

        return redirect()->route('spk.index')->with('success', 'Data SPK berhasil ditambahkan.');
    }

    public function show(Spk $spk)
    {
        return view('spk.show', compact('spk'));
    }

    public function edit(Spk $spk)
    {
        return view('spk.edit', compact('spk'));
    }

    public function update(Request $request, Spk $spk)
    {
        $validated = $this->validateData($request, $spk->id);

        $spk->update($validated);

        return redirect()->route('spk.show', $spk)->with('success', 'Data SPK berhasil diperbarui.');
    }

    public function destroy(Spk $spk)
    {
        $spk->delete();

        return redirect()->route('spk.index')->with('success', 'Data SPK berhasil dihapus.');
    }

    public function exportPdf(Spk $spk)
    {
        $pdf = Pdf::loadView('spk.pdf', [
            'spk' => $spk,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("SPK-{$spk->nomor}.pdf");
    }

    private function validateData(Request $request, ?int $spkId = null): array
    {
        return $request->validate([
            'nomor' => ['required', 'string', 'max:100', 'unique:spks,nomor,'.$spkId],
            'tanggal' => ['required', 'date'],

            'pegawai_nama' => ['required', 'string', 'max:255'],
            'pegawai_jabatan' => ['required', 'string', 'max:255'],
            'pegawai_divisi' => ['nullable', 'string', 'max:255'],
            'pegawai_nik_id' => ['nullable', 'string', 'max:255'],

            'tujuan_dinas' => ['required', 'string', 'max:255'],
            'lokasi_perusahaan_tujuan' => ['nullable', 'string', 'max:255'],
            'alamat_lokasi' => ['nullable', 'string'],
            'maksud_ruang_lingkup' => ['nullable', 'string'],

            'tanggal_berangkat' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_berangkat'],
            'lama_perjalanan' => ['required', 'integer', 'min:1', 'max:365'],

            'sumber_biaya' => ['nullable', 'string', 'max:255'],
            'moda_transportasi' => ['required', 'in:darat,laut,udara'],
            'sumber_biaya_opsi' => ['required', 'in:perusahaan,project,lainnya'],

            'ditugaskan_oleh_nama' => ['required', 'string', 'max:255'],
            'ditugaskan_oleh_jabatan' => ['required', 'string', 'max:255'],
        ]);
    }
}
