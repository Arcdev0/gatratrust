<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class SpkController extends Controller
{
    public function index(): View
    {
        return view('spk.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Spk::query()
            ->select(['id', 'nomor', 'tanggal', 'pegawai_nama', 'tujuan_dinas'])
            ->orderByDesc('id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal', function (Spk $spk) {
                return optional($spk->tanggal)->format('d-m-Y') ?: '-';
            })
            ->addColumn('action', function (Spk $spk) {
                return '
                    <a href="'.route('spk.show', $spk).'" class="btn btn-sm btn-info" title="Detail">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="'.route('spk.edit', $spk).'" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="'.route('spk.exportPdf', $spk).'" target="_blank" class="btn btn-sm btn-secondary" title="Export PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$spk->id.'" data-nomor="'.e($spk->nomor).'" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create(): View
    {
        $newSpkNo = $this->generateSpkNumber();

        return view('spk.create', compact('newSpkNo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        try {
            if (empty($validated['nomor'])) {
                $validated['nomor'] = $this->generateSpkNumber();
            }

            Spk::create($validated);

            return redirect()->route('spk.index')->with('success', 'Data SPK berhasil ditambahkan.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan data SPK: '.$e->getMessage());
        }
    }

    public function show(Spk $spk): View
    {
        return view('spk.show', compact('spk'));
    }

    public function edit(Spk $spk): View
    {
        return view('spk.edit', compact('spk'));
    }

    public function update(Request $request, Spk $spk): RedirectResponse
    {
        $validated = $this->validateData($request, $spk->id);

        try {
            $spk->update($validated);

            return redirect()->route('spk.show', $spk)->with('success', 'Data SPK berhasil diperbarui.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data SPK: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, Spk $spk)
    {
        try {
            $spk->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data SPK berhasil dihapus.',
                ]);
            }

            return redirect()->route('spk.index')->with('success', 'Data SPK berhasil dihapus.');
        } catch (Throwable $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data SPK: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->route('spk.index')->with('error', 'Gagal menghapus data SPK: '.$e->getMessage());
        }
    }

    public function exportPdf(Spk $spk)
    {
        $pdf = Pdf::loadView('spk.pdf', [
            'spk' => $spk,
        ])->setPaper('a4', 'portrait');

        $safeNomor = str_replace(['/', '\\'], '-', $spk->nomor);
        $safeNomor = preg_replace('/[^A-Za-z0-9._-]/', '-', $safeNomor ?? 'SPK');
        $safeNomor = trim((string) $safeNomor, '-');
        $safeNomor = $safeNomor !== '' ? $safeNomor : 'SPK';

        return $pdf->stream("SPK-{$safeNomor}.pdf");
    }

    private function generateSpkNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $monthYear = "{$month}-{$year}";

        $lastSpkThisYear = Spk::where('nomor', 'like', "%/GPT-SPK/%-{$year}")
            ->orderByDesc('id')
            ->first();

        if ($lastSpkThisYear) {
            $lastNumber = (int) explode('/', $lastSpkThisYear->nomor)[0];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $runningNumber = str_pad((string) $newNumber, 3, '0', STR_PAD_LEFT);

        return "{$runningNumber}/GPT-SPK/{$monthYear}";
    }

    private function validateData(Request $request, ?int $spkId = null): array
    {
        return $request->validate([
            'nomor' => ['nullable', 'string', 'max:100', 'unique:spks,nomor,'.$spkId],
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
        ], [
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali harus sama atau setelah tanggal berangkat.',
            'nomor.unique' => 'Nomor SPK sudah digunakan.',
            'pegawai_nama.required' => 'Nama pegawai wajib diisi.',
            'tujuan_dinas.required' => 'Tujuan dinas wajib diisi.',
        ]);
    }
}
