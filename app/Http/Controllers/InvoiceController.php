<?php

namespace App\Http\Controllers;

use App\Models\InvoicePayment;
use App\Models\Invoice;
use App\Models\ProjectTbl;
use App\Models\Quotation;
use Carbon\Carbon;
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
        $query = Invoice::select([
            'id',
            'invoice_no',
            'date',
            'customer_name',
            'customer_address',
            'down_payment',
            'net_total',
            'status',
        ]);

        return DataTables::of($query)
            ->addColumn('tanggal', function ($inv) {
                return Carbon::parse($inv->date)->format('d-m-Y');
            })
            ->addColumn('down_payment', function ($inv) {
                return number_format($inv->down_payment ?? 0, 0, ',', '.');
            })
            ->addColumn('net_total', function ($inv) {
                return number_format($inv->net_total ?? 0, 0, ',', '.');
            })
            ->addColumn('aksi', function ($inv) {
                return '
                    <button data-id="' . $inv->id . '" class="btn btn-sm btn-info btn-edit me-1">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button data-id="' . $inv->id . '" class="btn btn-sm btn-secondary btn-edit me-1">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button data-id="' . $inv->id . '" class="btn btn-sm btn-danger btn-delete">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $projects = ProjectTbl::orderBy('created_at', 'desc')
            ->get();

        $quotation = null;

        if ($request->has('quotation_id')) {
            $quotation = Quotation::with(['items', 'status'])
                ->findOrFail($request->quotation_id);
        }

        $newInvoiceNo = 'INV-' . str_pad(rand(1, 9999), 5, '0', STR_PAD_LEFT);
        return view('invoice.create', compact('newInvoiceNo', 'projects', 'quotation'));
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $projects = ProjectTbl::orderBy('created_at', 'desc')->get();

        return view('invoice.edit', compact('invoice', 'projects'));
    }

    public function update(Request $request, $id)
{
    $invoice = Invoice::findOrFail($id);

    $validated = $request->validate([
        'invoice_no'       => 'required|string|unique:invoices,invoice_no,' . $invoice->id,
        'date'             => 'required|date',
        'customer_name'    => 'required|string|max:255',
        'project_id'       => 'required|integer|exists:projects,id',
        'customer_address' => 'required|string',
        'inputDesc'        => 'required|string',
        'gross_total'      => 'required|numeric',
        'discount'         => 'nullable|numeric',
        'down_payment'     => 'nullable|numeric',
        'tax'              => 'nullable|numeric',
        'net_total'        => 'required|numeric',
    ]);

    $invoice->update([
        'invoice_no'       => $validated['invoice_no'],
        'date'             => $validated['date'],
        'project_id'       => $validated['project_id'] ?? null,
        'customer_name'    => $validated['customer_name'],
        'customer_address' => $validated['customer_address'],
        'description'      => $validated['inputDesc'],
        'gross_total'      => $validated['gross_total'],
        'discount'         => $validated['discount'] ?? 0,
        'down_payment'     => $validated['down_payment'] ?? 0,
        'tax'              => $validated['tax'] ?? 0,
        'net_total'        => $validated['net_total'],
        // status sengaja tidak diubah disini, biar update fokus ke data utama
    ]);


     // Hitung ulang status berdasarkan pembayaran
    $invoice->refresh(); // supaya data relasi dan accessor terupdateg
    if ($invoice->remaining <= 0) {
        $invoice->status = 'paid';
    } else {
        $invoice->status = 'unpaid';
    }

    $invoice->save();

    return response()->json([
        'success' => true,
        'message' => 'Invoice berhasil diperbarui',
        'data'    => $invoice
    ]);
}

    public function store(Request $request)
    {

        // dd($request->all());
        // Validasi data dulu
        $validated = $request->validate([
            'invoice_no'       => 'required|string|unique:invoices,invoice_no',
            'date'             => 'required|date',
            'customer_name'    => 'required|string|max:255',
            'project_id'       => 'required|integer|exists:projects,id',
            'customer_address' => 'required|string',
            'inputDesc'        => 'required|string',
            'gross_total'      => 'required|numeric',
            'discount'         => 'nullable|numeric',
            'down_payment'     => 'nullable|numeric',
            'tax'              => 'nullable|numeric',
            'net_total'        => 'required|numeric',
        ]);

        // Simpan invoice
        $invoice = Invoice::create([
            'invoice_no'       => $validated['invoice_no'],
            'date'             => $validated['date'],
            'project_id'       => $validated['project_id'] ?? null,
            'customer_name'    => $validated['customer_name'],
            'customer_address' => $validated['customer_address'],
            'description'      => $validated['inputDesc'],
            'gross_total'      => $validated['gross_total'],
            'discount'         => $validated['discount'] ?? 0,
            'down_payment'     => $validated['down_payment'] ?? 0,
            'tax'              => $validated['tax'] ?? 0,
            'net_total'        => $validated['net_total'],
            'status'           => 'unpaid',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil dibuat',
            'data'    => $invoice
        ]);
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
