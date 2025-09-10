<?php

namespace App\Http\Controllers;

use App\Models\ProjectTbl;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('invoice.index');
    }

    public function getData(Request $request)
    {
        $invoices = session()->get('invoices', []);

        // Jika belum ada data di session, buat dummy
        if (empty($invoices)) {
            $invoices = [
                [
                    'invoice_no'       => 'INV-00001',
                    'date'             => '2025-09-01',
                    'customer_name'    => 'PT. Nusantara Abadi',
                    'customer_address' => 'Jl. Merdeka No. 1, Jakarta',
                    'gross_total'      => 500000,
                    'discount'         => 50000,
                    'down_payment'     => 100000,
                    'tax'              => 45000,
                    'net_total'        => 395000,
                    'items' => [
                        ['description' => 'Produk A', 'amount' => 200000],
                        ['description' => 'Produk B', 'amount' => 300000],
                    ],
                ],
                [
                    'invoice_no'       => 'INV-00002',
                    'date'             => '2025-09-05',
                    'customer_name'    => 'CV. Maju Jaya',
                    'customer_address' => 'Jl. Melati No. 10, Bandung',
                    'gross_total'      => 250000,
                    'discount'         => 0,
                    'down_payment'     => 50000,
                    'tax'              => 25000,
                    'net_total'        => 225000,
                    'items' => [
                        ['description' => 'Jasa Konsultasi', 'amount' => 250000],
                    ],
                ],
            ];
            session()->put('invoices', $invoices);
        }

       $collection = collect($invoices)->map(function ($inv) {
    return [
        'invoice_no'   => $inv['invoice_no'],
        'tanggal'      => $inv['date'],
        'customer'     => $inv['customer_name'],
        'deskripsi'    => $inv['items'][0]['description'] ?? '-',
        'down_payment' => number_format($inv['down_payment'] ?? 0, 0, ',', '.'),
        'net_total'    => number_format($inv['net_total'] ?? 0, 0, ',', '.'),
        'aksi'         => '
            <button data-id="'.$inv['invoice_no'].'" class="btn btn-sm btn-warning btn-edit me-1"><i class="fas fa-edit"></i></button>
            <button data-id="'.$inv['invoice_no'].'" class="btn btn-sm btn-danger btn-delete"><i class="fas fa-trash"></i></button>
        '
    ];
        });

        return DataTables::of($collection)->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $projects = ProjectTbl::orderBy('created_at', 'desc')
        ->get();// Ambil data proyek dari database jika perlu
        $newInvoiceNo = 'INV-' . str_pad(rand(1, 9999), 5, '0', STR_PAD_LEFT);
        return view('invoice.create', compact('newInvoiceNo', 'projects'));
    }

    public function edit($invoiceNo)
    {
        return response()->json([
            'message' => "Edit invoice $invoiceNo (dummy only)."
        ]);
    }

    public function store(Request $request)
    {


        dd($request->all());
        $request->validate([
            'invoice_no'          => 'required|string|max:50',
            'date'                => 'required|date',
            'customer_name'       => 'required|string|max:255',
            'customer_address'    => 'nullable|string',
            'gross_total'         => 'required|numeric|min:0',
            'discount'            => 'nullable|numeric|min:0',
            'down_payment'        => 'nullable|numeric|min:0',
            'tax'                 => 'nullable|numeric|min:0',
            'net_total'           => 'required|numeric|min:0',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.amount'      => 'required|numeric|min:0',
        ]);

        $invoice = [
            'invoice_no'       => $request->invoice_no,
            'date'             => $request->date,
            'customer_name'    => $request->customer_name,
            'customer_address' => $request->customer_address,
            'gross_total'      => $request->gross_total,
            'discount'         => $request->discount ?? 0,
            'down_payment'     => $request->down_payment ?? 0,
            'tax'              => $request->tax ?? 0,
            'net_total'        => $request->net_total,
            'items'            => $request->items,
        ];



        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil disimpan (dummy).');
    }

    public function destroy($invoiceNo)
    {
        $invoices = session()->get('invoices', []);
        $invoices = array_filter($invoices, fn($inv) => $inv['invoice_no'] !== $invoiceNo);
        session()->put('invoices', $invoices);

        return response()->json([
            'message' => "Invoice $invoiceNo berhasil dihapus (dummy)."
        ]);
    }
}
