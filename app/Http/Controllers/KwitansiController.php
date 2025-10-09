<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KwitansiController extends Controller
{

     use LogsActivity;
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
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $invoices = Invoice::all();

        // dd($invoices);
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

        DB::beginTransaction();
        try {
            // Generate nomor kwitansi otomatis
            $now = Carbon::now();
            $monthYear = $now->format('m-Y');

            $lastPayment = InvoicePayment::orderBy('id', 'desc')->first();

            if ($lastPayment && $lastPayment->no_payment) {
                $lastNumber = intval(explode('/', $lastPayment->no_payment)[0]);
                $newNumber  = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }

            $newPaymentNo = $newNumber . '/KW/GPT/' . $monthYear;

            // Simpan kwitansi baru
            $kwitansi = InvoicePayment::create([
                'invoice_id'   => $request->invoice_id,
                'payment_date' => $request->payment_date,
                'amount_paid'  => $request->amount_paid,
                'note'         => $request->note,
                'no_payment'   => $newPaymentNo,
            ]);

            // Update status invoice terkait
            $invoice = Invoice::with('payments')->findOrFail($request->invoice_id);
            $invoice->refresh();

            if ($invoice->remaining <= 0) {
                $invoice->status = 'close';
            } elseif ($invoice->total_paid > 0) {
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'open';
            }

            $invoice->save();

            // Simpan log aktivitas (new_data berisi detail kwitansi)
            $this->logActivity(
                "Membuat Kwitansi {$kwitansi->no_payment} untuk Invoice {$invoice->invoice_no} sebesar " . number_format($request->amount_paid, 0, ',', '.'),
                $kwitansi->no_payment,
                null, // old_data
                $kwitansi->toArray() // new_data
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kwitansi berhasil disimpan.',
                'data'    => $kwitansi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
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

        DB::beginTransaction();
        try {
            $kwitansi = InvoicePayment::findOrFail($id);

            // simpan data lama
            $oldData = $kwitansi->toArray();

            // update kwitansi
            $kwitansi->update($request->only(['invoice_id', 'payment_date', 'amount_paid', 'note']));
            $kwitansi->refresh();

            // update status invoice terkait
            $invoice = Invoice::with('payments')->findOrFail($kwitansi->invoice_id);
            $invoice->refresh();

            if ($invoice->remaining <= 0) {
                $invoice->status = 'close';
            } elseif ($invoice->total_paid > 0) {
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'open';
            }
            $invoice->save();

            // log activity
            $this->logActivity(
                "Memperbarui Kwitansi {$kwitansi->no_payment} untuk Invoice {$invoice->invoice_no}",
                $kwitansi->no_payment,
                $oldData,
                $kwitansi->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kwitansi berhasil diperbarui.',
                'data'    => $kwitansi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
