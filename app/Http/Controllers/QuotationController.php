<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationScope;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    // 1. Tampilkan halaman index
    public function index()
    {
        return view('quotations.index');
    }

    // 2. Ambil data untuk DataTables
    public function getDataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Quotation::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary editBtn" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                        <a href="'.route('quotations.exportPdf', $row->id).'" class="btn btn-sm btn-secondary" target="_blank">PDF</a>
                    ';
                })
               ->editColumn('date', function ($row) {
                    return $row->date ? Carbon::parse($row->date)->format('d-m-Y') : '';
                })
                ->make(true);
        }
    }

    public function create()
    {
        return view('quotations.create');
    }

    // 3. Simpan quotation baru
     public function store(Request $request)
        {
            DB::beginTransaction();


            // dd($request->all());


            try {
                // Validasi data
                $validated = $request->validate([
                    'quo_no' => 'required|string|max:50|unique:quotation,quo_no',
                    'date' => 'required|date',
                    'customer_name' => 'required|string|max:255',
                    'customer_address' => 'nullable|string',
                    'attention' => 'nullable|string|max:255',
                    'your_reference' => 'nullable|string|max:255',
                    'terms' => 'nullable|string',
                    'job_no' => 'nullable|string|max:50',
                    'rev' => 'nullable|string|max:10',
                    'discount' => 'nullable|numeric|min:0',
                    'payment_terms' => 'nullable|string',
                    'bank_account' => 'nullable|string',
                    'tax_included' => 'nullable|boolean',
                    'items' => 'required|array|min:1',
                    'items.*.description' => 'required|string',
                    'items.*.qty' => 'required|numeric|min:1',
                    'items.*.unit_price' => 'required|numeric|min:0',
                    'scopes' => 'nullable|array',
                    'scopes.*.description' => 'required_with:scopes|string',
                ]);

                // Hitung total amount
                $total_amount = collect($validated['items'])->sum(function ($item) {
                    return $item['qty'] * $item['unit_price'];
                });

                $sub_total = $total_amount - ($validated['discount'] ?? 0);

                // Simpan quotation utama
                $quotation = Quotation::create([
                    'quo_no' => $validated['quo_no'],
                    'date' => $validated['date'],
                    'customer_name' => $validated['customer_name'],
                    'customer_address' => $validated['customer_address'] ?? null,
                    'attention' => $validated['attention'] ?? null,
                    'your_reference' => $validated['your_reference'] ?? null,
                    'terms' => $validated['terms'] ?? null,
                    'job_no' => $validated['job_no'] ?? null,
                    'rev' => $validated['rev'] ?? null,
                    'total_amount' => $total_amount,
                    'discount' => $validated['discount'] ?? 0,
                    'sub_total' => $sub_total,
                    'payment_terms' => $validated['payment_terms'] ?? null,
                    'bank_account' => $validated['bank_account'] ?? null,
                    'tax_included' => $validated['tax_included'] ?? false,
                    // 'status' => 'draft',
                ]);

                // Simpan items
                foreach ($validated['items'] as $item) {
                    $quotation->items()->create([
                        'description' => $item['description'],
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['qty'] * $item['unit_price'],
                    ]);
                }

                // Simpan scopes jika ada
                if (!empty($validated['scopes'])) {
                    foreach ($validated['scopes'] as $scope) {
                        $quotation->scopes()->create([
                            'description' => $scope['description'],
                            'responsible_pt_gpt' => !empty($scope['responsible_pt_gpt']) ? 1 : 0,
                            'responsible_client' => !empty($scope['responsible_client']) ? 1 : 0,
                        ]);
                    }
                }

                DB::commit();

             return response()->json([
                    'success' => true,
                    'status'  => 201,
                    'message' => 'Quotation berhasil dibuat'
                ], 201);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'status'  => 500,
                    'message' => 'Terjadi kesalahan saat menyimpan quotation'
                ], 500);
            }
        }

    // 4. Ambil data untuk form edit
   public function edit($id)
    {
        $quotation = Quotation::with(['items', 'scopes'])->findOrFail($id);
        return view('quotations.edit', compact('quotation'));
    }

    // 5. Update quotation
    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        $quotation->update($request->only([
            'quo_no', 'date', 'customer_name', 'customer_address',
            'attention', 'your_reference', 'terms', 'job_no',
            'rev', 'total_amount', 'discount', 'sub_total',
            'payment_terms', 'bank_account', 'tax_included'
        ]));

        // Hapus item lama & buat baru
        $quotation->items()->delete();
        if ($request->items) {
            foreach ($request->items as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }
        }

        // Hapus scope lama & buat baru
        $quotation->scopes()->delete();
        if ($request->scopes) {
            foreach ($request->scopes as $scope) {
                QuotationScope::create([
                    'quotation_id' => $quotation->id,
                    'description' => $scope['description'],
                    'responsible_pt_gpt' => $scope['responsible_pt_gpt'] ?? false,
                    'responsible_client' => $scope['responsible_client'] ?? false,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    // 6. Hapus quotation
    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete();
        return response()->json(['success' => true]);
    }

    // 7. Export PDF
    public function exportPdf($id)
    {
        $quotation = Quotation::with(['items', 'scopes'])->findOrFail($id);
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('quotation_'.$quotation->quo_no.'.pdf');
    }
}
