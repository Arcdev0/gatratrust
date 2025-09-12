<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoicePayment;

class KwitansiController extends Controller
{
    public function index()
    {
        $kwitansi = InvoicePayment::with('invoice')->latest()->get();
        return view('kwitansi.index', compact('kwitansi'));
    }

    public function data(Request $request)
    {
        $query = InvoicePayment::with('invoice')->select('invoice_payments.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('payment_date', function ($row) {
                return \Carbon\Carbon::parse($row->payment_date)->format('d-m-Y');
            })
            ->editColumn('amount_paid', function ($row) {
                return $row->amount_paid;
            })
            ->addColumn('invoice_no', function ($row) {
                return $row->invoice->invoice_no ?? '-';
            })
            ->addColumn('action', function ($row) {
                $editUrl = route('kwitansi.edit', $row->id);
                return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-secondary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $invoices = Invoice::all(); // daftar invoice untuk dropdown
        return view('kwitansi.create', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'   => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount_paid'  => 'required|numeric|min:1',
            'note'         => 'nullable|string',
        ]);

        $kwitansi = InvoicePayment::create([
            'invoice_id'   => $request->invoice_id,
            'payment_date' => $request->payment_date,
            'amount_paid'  => $request->amount_paid,
            'note'         => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kwitansi berhasil disimpan.',
            'data'    => $kwitansi
        ]);
    }

    public function edit($id)
    {
        $kwitansi = InvoicePayment::findOrFail($id);
        $invoices = Invoice::all();
        return view('kwitansi.edit', compact('kwitansi', 'invoices'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'invoice_id'   => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount_paid'  => 'required|numeric|min:1',
            'note'         => 'nullable|string',
        ]);

        $kwitansi = InvoicePayment::findOrFail($id);
        $kwitansi->update($request->only(['invoice_id', 'payment_date', 'amount_paid', 'note']));

        return response()->json([
            'success' => true,
            'message' => 'Kwitansi berhasil diperbarui.',
            'data'    => $kwitansi
        ]);
    }
}