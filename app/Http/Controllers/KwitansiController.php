<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\JournalTrait;

class KwitansiController extends Controller
{

    use LogsActivity;
    use JournalTrait;
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
        return view('kwitansi.create', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'    => 'required|exists:invoices,id',
            'payment_date'  => 'required|date',
            'amount_paid'   => 'required|numeric|min:1',
            'wallet_coa_id' => 'required|integer|exists:coa,id', // ✅ pilih wallet saat bayar
            'note'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1) Load invoice dulu (buat validasi sisa tagihan, dll)
            $invoice = Invoice::with('payments')->lockForUpdate()->findOrFail($request->invoice_id);
            $invoice->refresh();

            $amount = (float) $request->amount_paid;

            // (opsional tapi sangat disarankan) cegah bayar > sisa
            if ((float) $invoice->remaining > 0 && $amount > (float) $invoice->remaining) {
                throw new \Exception("Jumlah bayar melebihi sisa tagihan invoice.");
            }

            // 2) Generate nomor kwitansi otomatis
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

            // 3) Simpan kwitansi baru
            $kwitansi = InvoicePayment::create([
                'invoice_id'    => $request->invoice_id,
                'payment_date'  => $request->payment_date,
                'amount_paid'   => $amount,
                'wallet_coa_id' => (int) $request->wallet_coa_id, // ✅ simpan wallet yang dipilih
                'note'          => $request->note,
                'no_payment'    => $newPaymentNo,
            ]);

            // 4) Buat jurnal penerimaan pembayaran (cash_in)
            $d = $this->journalDefaults();

            if (empty($d['ar'])) {
                throw new \Exception("Accounting Settings belum lengkap. Isi default AR terlebih dahulu.");
            }

            // OPTIONAL: Batasi wallet yang boleh dipilih.
            // Kalau kamu punya table settings berisi wallet_coa_ids (multi select),
            // kamu bisa validasi di sini. Kalau belum ada, lewati saja.
            //
            // $allowedWallets = AccountingSetting::first()?->wallet_coa_ids ?? [];
            // if (!in_array((int)$request->wallet_coa_id, $allowedWallets)) {
            //     throw new \Exception("Wallet yang dipilih tidak termasuk daftar wallet yang diizinkan.");
            // }

            $journal = $this->createJournal(
                [
                    'journal_date' => $request->payment_date,
                    'type'         => 'cash_in', // atau 'general' jika sistemmu pakai itu
                    'category'     => 'invoice_payment',
                    'reference_no' => $kwitansi->no_payment,
                    'memo'         => "Penerimaan pembayaran Invoice {$invoice->invoice_no} ({$kwitansi->no_payment})",
                    'status'       => 'posted',
                ],
                [
                    [
                        'coa_id'      => (int) $request->wallet_coa_id,
                        'debit'       => $amount,
                        'credit'      => 0,
                        'description' => 'Kas/Bank - Penerimaan',
                    ],
                    [
                        'coa_id'      => (int) $d['ar'],
                        'debit'       => 0,
                        'credit'      => $amount,
                        'description' => 'Piutang Usaha - Pelunasan',
                    ],
                ]
            );

            // OPTIONAL: kalau kamu mau link journal ke kwitansi biar audit trail rapi
            // pastikan kolomnya ada di table invoice_payments
            // $kwitansi->journal_id = $journal->id;
            // $kwitansi->save();

            // 5) Update status invoice
            $invoice->refresh(); // biar remaining/total_paid up-to-date
            if ($invoice->remaining <= 0) {
                $invoice->status = 'close';
            } elseif ($invoice->total_paid > 0) {
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'open';
            }
            $invoice->save();

            // 6) Log aktivitas
            $this->logActivity(
                "Membuat Kwitansi {$kwitansi->no_payment} untuk Invoice {$invoice->invoice_no} sebesar " . number_format($amount, 0, ',', '.'),
                $kwitansi->no_payment,
                null,
                $kwitansi->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kwitansi berhasil disimpan & jurnal penerimaan otomatis dibuat.',
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
