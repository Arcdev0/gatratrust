<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function index()
    {

        return view('assets.index');
    }

    public function datatable(Request $request)
    {
        $query = Asset::query()->select([
            'id',
            'no_asset',
            'nama',
            'merek',
            'no_seri',
            'lokasi',
            'jumlah',
            'harga',
            'total',
            'url_gambar',
            'url_barcode',
            'kode_barcode',
            'created_at',
        ]);

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('harga', function ($row) {
                return number_format((float) $row->harga, 0, ',', '.');
            })
            ->editColumn('total', function ($row) {
                return number_format((float) $row->total, 0, ',', '.');
            })

            ->editColumn('url_gambar', function ($row) {
                if (empty($row->url_gambar)) {
                    return '<span class="text-muted">-</span>';
                }

                $url = e($row->url_gambar);

                return '
        <a href="' . $url . '" target="_blank" title="Lihat gambar">
            <img src="' . $url . '"
                 style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
        </a>
    ';
            })


            ->addColumn('action', function ($row) {

                $scanUrl    = route('assets.scan', $row->kode_barcode);
                $barcodeUrl = $row->url_barcode ?? '';

                // QR (MODAL)
                $btnBarcode = '
        <button type="button"
            class="btn btn-sm  btn-dark btn-barcode-asset"
            title="QR Code"
            data-toggle="modal"
            data-target="#modalBarcodeAsset"
            data-id="' . $row->id . '"
            data-no_asset="' . e($row->no_asset) . '"
            data-nama="' . e($row->nama) . '"
            data-kode_barcode="' . e($row->kode_barcode) . '"
            data-scan_url="' . e($scanUrl) . '"
            data-url_barcode="' . e($barcodeUrl) . '"
        >
            <i class="fas fa-qrcode"></i>
        </button>
    ';

                // VIEW
                //             $btnView = '
                //     <a href="' . route('assets.show', $row->id) . '"
                //        class="btn btn-sm btn-info"
                //        title="Detail Asset">
                //         <i class="fas fa-eye"></i>
                //     </a>
                // ';

                // EDIT (MODAL)
                $btnEdit = '
        <button type="button"
            class="btn btn-sm btn-secondary btn-edit-asset"
            title="Edit Asset"
            data-toggle="modal"
            data-target="#modalEditAsset"
            data-id="' . $row->id . '"
            data-no_asset="' . e($row->no_asset) . '"
            data-nama="' . e($row->nama) . '"
            data-merek="' . e($row->merek ?? '') . '"
            data-no_seri="' . e($row->no_seri ?? '') . '"
            data-lokasi="' . e($row->lokasi) . '"
            data-jumlah="' . (int)$row->jumlah . '"
            data-harga="' . (float)$row->harga . '"
            data-url_gambar="' . e($row->url_gambar ?? '') . '"
        >
            <i class="fas fa-edit"></i>
        </button>
    ';

                // DELETE (MODAL)
                $btnDelete = '
        <button type="button"
            class="btn btn-sm btn-danger btn-delete-asset"
            title="Hapus Asset"
            data-toggle="modal"
            data-target="#modalDeleteAsset"
            data-id="' . $row->id . '"
            data-label="' . e($row->nama) . ' (' . e($row->no_asset) . ')"
        >
            <i class="fas fa-trash-alt"></i>
        </button>
    ';

                return '
        <div class="d-flex gap-1 justify-content-center flex-wrap">
            ' . $btnBarcode . $btnEdit . $btnDelete . '
        </div>
    ';
            })


            ->rawColumns(['url_gambar', 'action'])
            ->make(true);
    }



    public function create()
    {
        return view('assets.create');
    }

    public function nextNo()
    {
        $last = Asset::orderBy('id', 'desc')->first();
        $prefix = 'GPT-';

        $nextNumber = 1;

        if ($last && $last->no_asset) {
            // ambil angka terakhir dari no_asset
            // contoh: AST-0007 -> 7
            if (preg_match('/(\d+)$/', $last->no_asset, $m)) {
                $nextNumber = ((int)$m[1]) + 1;
            }
        }

        $nextNoAsset = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => true,
            'no_asset' => $nextNoAsset,
        ]);
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'no_asset'   => ['required', 'string', 'max:50', 'unique:assets,no_asset'],
            'nama'       => ['required', 'string', 'max:150'],
            'merek'      => ['nullable', 'string', 'max:100'],
            'no_seri'    => ['nullable', 'string', 'max:100'],
            'lokasi'     => ['required', 'string', 'max:150'],
            'jumlah'     => ['required', 'integer', 'min:1'],
            'harga'      => ['required', 'numeric', 'min:0'],
            'url_gambar' => ['nullable', 'string', 'max:500'],
            'gambar'     => ['nullable', 'image'],
        ]);

        // upload gambar asset (optional)
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('assets', 'public');
            $data['url_gambar'] = Storage::url($path);
        }

        // ✅ generate kode_barcode random & unik (untuk URL publik)
        do {
            $kode = Str::random(12); // 12 karakter (boleh 10-16)
        } while (Asset::where('kode_barcode', $kode)->exists());

        $data['kode_barcode'] = $kode;

        // ✅ QR content: URL scan (pakai kode random)
        $qrContent = route('assets.scan', $data['kode_barcode']);

        // ✅ simpan file QR png ke storage
        $fileName = "qrcodes/asset-{$data['kode_barcode']}.png";

        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($qrContent);

        $fileName = "qrcodes/asset-{$data['kode_barcode']}.svg";
        Storage::disk('public')->put($fileName, $qrSvg);
        $data['url_barcode'] = Storage::url($fileName);

        // simpan ke DB (total dihitung otomatis di Model booted saving)
        $asset = Asset::create($data);

        // response untuk AJAX
        return response()->json([
            'status'  => true,
            'message' => 'Asset berhasil dibuat.',
            'data'    => [
                'id'           => $asset->id,
                'no_asset'     => $asset->no_asset,
                'nama'         => $asset->nama,
                'kode_barcode' => $asset->kode_barcode,
                'scan_url'     => $qrContent,
                'url_barcode'  => $asset->url_barcode,
            ],
        ]);
    }

    public function show($id)
    {
        $asset = Asset::findOrFail($id);
        return view('assets.show', compact('asset'));
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $data = $request->validate([
            'no_asset'   => ['required', 'string', 'max:50', Rule::unique('assets', 'no_asset')->ignore($asset->id)],
            'nama'       => ['required', 'string', 'max:150'],
            'merek'      => ['nullable', 'string', 'max:100'],
            'no_seri'    => ['nullable', 'string', 'max:100'],
            'lokasi'     => ['required', 'string', 'max:150'],
            'jumlah'     => ['required', 'integer', 'min:1'],
            'harga'      => ['required', 'numeric', 'min:0'],
            'url_gambar' => ['nullable', 'string', 'max:500'],
            'gambar'     => ['nullable', 'image'],
        ]);

        if ($request->hasFile('gambar')) {

            if ($asset->url_gambar && str_contains($asset->url_gambar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', parse_url($asset->url_gambar, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('gambar')->store('assets', 'public');
            $data['url_gambar'] = Storage::url($path);
        }

        $asset->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Asset berhasil diupdate.',
                'data'    => $asset->fresh(),
            ]);
        }

        return redirect()
            ->route('assets.show', $asset->id)
            ->with('success', 'Asset berhasil diupdate.');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset berhasil dihapus.');
    }

    /**
     * Halaman publik / scan barcode:
     * contoh URL: /asset-scan/{kode_barcode}
     */
    public function scan($kode_barcode)
    {
        $asset = Asset::where('kode_barcode', $kode_barcode)->firstOrFail();

        return view('assets.scan', compact('asset'));
    }
}
