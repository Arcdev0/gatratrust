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
            ->addColumn('remaining', function ($inv) {
                return number_format($inv->remaining ?? 0, 0, ',', '.');
            })
           ->addColumn('aksi', function ($inv) {
                return '
                    <button data-id="' . $inv->id . '" class="btn btn-sm btn-info btn-view me-1">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button data-id="' . $inv->id . '" class="btn btn-sm btn-secondary btn-edit me-1">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button data-id="' . $inv->id . '" class="btn btn-sm btn-danger btn-delete me-1">
                        <i class="fas fa-trash"></i>
                    </button>
                    <a href="' . route('invoice.print', $inv->id) . '" target="_blank"
                    class="btn btn-sm btn-success me-1">
                        <i class="fas fa-print"></i>
                    </a>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function printInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Format angka rupiah helper
        $formatRupiah = function($angka) {
            return 'Rp ' . number_format($angka ?? 0, 0, ',', '.');
        };

        return view('invoice.print', compact('invoice', 'formatRupiah'));
    }

    public function show($id)
    {
        $invoice = Invoice::with('payments')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
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

        $now = Carbon::now();
        $monthYear = $now->format('m-Y');

        $lastInvoice = Invoice::orderBy('id', 'desc')->first();

        if ($lastInvoice) {
            $lastNumber = intval(explode('/', $lastInvoice->invoice_no)[0]);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        $newInvoiceNo = $newNumber . '/INV/GPT/' . $monthYear;


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

        $invoice->refresh();

        if ($invoice->remaining <= 0) {
            $invoice->status = 'close';
        } elseif ($invoice->remaining == $invoice->net_total) {
            $invoice->status = 'open';
        } else {
            $invoice->status = 'partial';
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
            'status'           => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil dibuat',
            'data'    => $invoice
        ]);
    }

    public function destroy($id)
    {
        // Cari invoice berdasarkan ID
        $invoice = Invoice::find($id);

        // Jika tidak ditemukan
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }

        // Hapus invoice
        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil dihapus'
        ]);
    }
}
