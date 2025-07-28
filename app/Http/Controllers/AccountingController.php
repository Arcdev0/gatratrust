<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\AccountingFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class AccountingController extends Controller
{

    public function index()
    {
        return view('accounting.index');
    }

    public function data(Request $request)
    {
        $query = Accounting::select('id', 'no_jurnal', 'tipe_jurnal', 'tanggal', 'deskripsi', 'debit', 'credit', 'total')
            ->orderBy('id', 'desc');

        // Filter bulan
        if ($request->month) {
            $query->whereMonth('tanggal', '=', date('m', strtotime($request->month)))
                ->whereYear('tanggal', '=', date('Y', strtotime($request->month)));
        }

        // Filter range tanggal
        if ($request->range) {
            $dates = explode(' s/d ', $request->range);
            if (count($dates) === 2) {
                $query->whereBetween('tanggal', [$dates[0], $dates[1]]);
            }
        }

        return DataTables::of($query)
            ->addColumn('tanggal_format', fn($row) => \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y'))
            ->addColumn('action', function ($row) {
                return '
                <button type="button" class="btn btn-sm btn-info btnShow me-1" data-id="' . $row->id . '">Show</button>
                <button data-id="' . $row->id . '" class="btn btn-danger btn-sm btnDelete">Hapus</button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);

        //  <a href="' . route('accounting.edit', $row->id) . '" class="btn btn-primary btn-sm">Edit</a>
    }

    public function generateNoJurnal(Request $request)
    {
        $tipe = $request->tipe_jurnal;

        if (!$tipe) {
            return response()->json(['error' => 'Tipe jurnal tidak boleh kosong'], 400);
        }

        $lastJurnal = Accounting::where('tipe_jurnal', $tipe)
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastJurnal) {
            // Misalnya format: JU-0005
            preg_match('/\d+$/', $lastJurnal->no_jurnal, $matches);
            $lastNumber = isset($matches[0]) ? (int) $matches[0] : 0;
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $newJurnal = $tipe . '-' . $newNumber;

        return response()->json(['no_jurnal' => $newJurnal]);
    }

    public function show($id)
    {
        $accounting = Accounting::with('files')->findOrFail($id);

        return response()->json([
            'no_jurnal'   => $accounting->no_jurnal,
            'tipe_jurnal' => $accounting->tipe_jurnal,
            'deskripsi'   => $accounting->deskripsi,
            'total'       => number_format($accounting->total, 2),
            'files'       => $accounting->files->map(function ($f) {
                $fileUrl = asset('storage/' . $f->file_path);

                // Cek ekstensi file
                $ext = strtolower(pathinfo($f->file_path, PATHINFO_EXTENSION));

                // Jika PDF, gunakan gambar default
                if ($ext === 'pdf') {
                    $previewUrl = asset('images/default-pdf.png');
                } else {
                    $previewUrl = $fileUrl;
                }

                return [
                    'name' => $f->file_name,
                    'path' => $fileUrl,     // Link asli file
                    'preview' => $previewUrl // Link preview (gambar/pdf icon)
                ];
            })
        ]);
    }


    public function create()
    {
        return view('accounting.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'tipe_jurnal'  => 'required|in:M,P,JU,JP',
            'tanggal'      => 'required|date',
            'total'        => 'required|numeric',
            'deskripsi'    => 'nullable|string',
            'files.*'      => 'nullable|file|mimes:pdf,jpg,png',
            'file_names.*' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            // Lock tipe jurnal untuk mencegah nomor duplikat
            $lastJurnal = Accounting::where('tipe_jurnal', $request->tipe_jurnal)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $lastNumber = 0;
            if ($lastJurnal) {
                preg_match('/\d+$/', $lastJurnal->no_jurnal, $matches);
                $lastNumber = isset($matches[0]) ? (int) $matches[0] : 0;
            }

            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $noJurnal = $request->tipe_jurnal . '-' . $newNumber;

            // Debit/Credit otomatis
            $debit = 0;
            $credit = 0;
            if (in_array($request->tipe_jurnal, ['M', 'P'])) {
                $debit = $request->total; // uang masuk
            } else {
                $credit = $request->total; // uang keluar
            }

            // Simpan jurnal
            $accounting = Accounting::create([
                'no_jurnal'   => $noJurnal,
                'tipe_jurnal' => $request->tipe_jurnal,
                'tanggal'     => $request->tanggal,
                'deskripsi'   => $request->deskripsi,
                'total'       => $request->total,
                'debit'       => $debit,
                'credit'      => $credit
            ]);

            // Simpan file
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    $path = $file->store('accounting_files', 'public');
                    $fileNameInput = $request->file_names[$index] ?? $file->getClientOriginalName();

                    $accounting->files()->create([
                        'file_name' => $fileNameInput,
                        'file_path' => $path
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('accounting.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }




    public function edit(Accounting $accounting)
    {
        $accounting->load('files');
        return view('accounting.edit', compact('accounting'));
    }

    public function update(Request $request, Accounting $accounting)
    {
        $request->validate([
            'no_jurnal'   => 'required|unique:accountings,no_jurnal,' . $accounting->id,
            'tipe_jurnal' => 'required',
            'total'       => 'required|numeric',
            'deskripsi'   => 'nullable',
            'files.*'     => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $accounting->update($request->only([
            'no_jurnal',
            'tipe_jurnal',
            'deskripsi',
            'total'
        ]));

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('accounting_files', 'public');
                $fileName = $file->getClientOriginalName();
                $accounting->files()->create([
                    'file_name' => $fileName,
                    'file_path' => $path
                ]);
            }
        }

        return redirect()->route('accounting.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy(Request $request, Accounting $accounting)
    {
        // Hapus semua file terkait dari storage
        foreach ($accounting->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $accounting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }


    public function deleteFile(AccountingFile $file)
    {
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'File berhasil dihapus');
    }
}
