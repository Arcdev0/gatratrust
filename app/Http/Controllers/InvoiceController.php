<?php

namespace App\Http\Controllers;

use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\ProjectTbl;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\LogsActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceController extends Controller
{
    use LogsActivity;

    public function index()
    {
        return view('invoice.index');
    }

    public function getData(Request $request)
    {
        $user = Auth::user();

        $query = Invoice::select([
            'id',
            'invoice_no',
            'date',
            'customer_name',
            'customer_address',
            'down_payment',
            'net_total',
            'status',
            'approval_status',
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

            // ✅ Kolom status approval (badge)
            ->addColumn('approval_status_badge', function ($inv) {
                switch ($inv->approval_status) {
                    case 'approved':
                        return '<span class="badge bg-success text-white">Approved</span>';
                    case 'rejected':
                        return '<span class="badge bg-danger text-white">Rejected</span>';
                    default:
                        return '<span class="badge bg-yellow text-white">Pending</span>';
                }
            })

            ->addColumn('aksi', function ($inv) use ($user) {
                $html = '
            <div class="dropdown-action">
                <button class="dropbtn">Aksi ⮟</button>
                <div class="dropdown-content">

                    <a href="javascript:void(0)" class="btn-view" data-id="' . $inv->id . '">
                        <i class="fas fa-eye"></i> Lihat
                    </a>

                    <a href="javascript:void(0)" class="btn-edit" data-id="' . $inv->id . '">
                        <i class="fas fa-edit"></i> Edit
                    </a>

                    <a href="javascript:void(0)" class="btn-delete" data-id="' . $inv->id . '">
                        <i class="fas fa-trash"></i> Hapus
                    </a>

                    <a href="' . route('invoice.print', $inv->id) . '" target="_blank">
                        <i class="fas fa-print"></i> Print
                    </a>

                    <a href="' . route('invoice.pdf', $inv->id) . '" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
            ';

                if ($user && $user->role_id == 4 && $inv->approval_status === 'pending') {

                    $html .= '
                        <a href="javascript:void(0)" class="btn-approve" data-id="' . $inv->id . '">
                            <i class="fas fa-check"></i> Approve
                        </a>

                        <a href="javascript:void(0)" class="btn-reject" data-id="' . $inv->id . '">
                            <i class="fas fa-times"></i> Reject
                        </a>
                    ';
                }

                $html .= '
                </div>
            </div>
            ';

                return $html;
            })
            ->rawColumns(['aksi', 'approval_status_badge'])
            ->make(true);
    }

    public function printInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Format angka rupiah helper
        $formatRupiah = function ($angka) {
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
        $projects = ProjectTbl::with('invoices', 'client')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($p) {
                return $p->total_invoice < $p->total_biaya_project;
            });


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
        $projects = ProjectTbl::with('invoices', 'client')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($p) {
                return $p->total_invoice < $p->total_biaya_project;
            });

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

        DB::beginTransaction();
        try {
            $oldData = $invoice->toArray();

            // Update data utama
            $invoice->update([
                'invoice_no'       => $validated['invoice_no'],
                'date'             => $validated['date'],
                'project_id'       => $validated['project_id'],
                'customer_name'    => $validated['customer_name'],
                'customer_address' => $validated['customer_address'],
                'description'      => $validated['inputDesc'],
                'gross_total'      => $validated['gross_total'],
                'discount'         => $validated['discount'] ?? 0,
                'down_payment'     => $validated['down_payment'] ?? 0,
                'tax'              => $validated['tax'] ?? 0,
                'net_total'        => $validated['net_total'],
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

            $this->logActivity(
                "Memperbarui Invoice {$invoice->invoice_no}",
                $invoice->invoice_no,
                $oldData,
                $invoice->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil diperbarui',
                'data'    => $invoice
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
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

        DB::beginTransaction();
        try {
            // Simpan invoice
            $invoice = Invoice::create([
                'invoice_no'       => $validated['invoice_no'],
                'date'             => $validated['date'],
                'project_id'       => $validated['project_id'],
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

            $this->logActivity(
                "Membuat Invoice {$invoice->invoice_no}",
                $invoice->invoice_no,
                null,
                $invoice->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dibuat',
                'data'    => $invoice
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateNoRef()
    {
        $lastRef = Invoice::where('invoice_type', 'dp')
            ->orderBy('id', 'desc')
            ->value('no_ref');

        if ($lastRef) {
            $number = intval($lastRef) + 1;
        } else {
            $number = 1;
        }

        return str_pad($number, 4, '0', STR_PAD_LEFT); // 0001, 0002, dst
    }


    public function getDpInvoices($projectId)
    {
        $dpInvoices = Invoice::where('project_id', $projectId)
            ->where('invoice_type', 'dp')
            ->get(['id', 'invoice_no', 'net_total']);

        return response()->json($dpInvoices);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Cari invoice berdasarkan ID
            $invoice = Invoice::find($id);

            // Jika tidak ditemukan
            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                ], 404);
            }

            $invoiceNo = $invoice->invoice_no;
            $oldData   = $invoice->toArray();

            // Hapus invoice
            $invoice->delete();

            // Simpan log aktivitas
            $this->logActivity(
                "Menghapus Invoice {$invoiceNo}",
                $invoiceNo,
                $oldData,
                null
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printInvoicePDF($id)
    {
        $invoice = Invoice::with('approver')->findOrFail($id); // pastikan relasi approver ke-load

        $user = auth()->user(); // user yang sedang login

        $pdf = PDF::loadView('invoice.pdf', compact('invoice', 'user'))
            ->setPaper('a4', 'portrait');

        // sanitize filename: ganti slash/backslash dengan dash
        $safeNo   = preg_replace('/[\/\\\\]+/', '-', $invoice->invoice_no);
        $filename = 'Invoice-' . $safeNo . '.pdf';

        return $pdf->stream($filename);
    }


    // InvoiceController.php
    public function approve($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Cegah double approval
        if ($invoice->approval_status === 'approved') {
            return response()->json([
                'message' => 'Invoice already approved!'
            ], 400);
        }

        $user = auth()->user();

        // Generate token pendek (bisa pakai yang lama kalau mau)
        $signatureToken = Str::random(32);

        // Data approval (mirip quotation)
        $approvalData = [
            'invoice_id'        => $invoice->id,
            'approver_id'       => $user->id,
            'approver_name'     => $user->name,
            'approver_position' => $user->role->name ?? 'Finance',
            'invoice_no'        => $invoice->invoice_no,
            'approval_date'     => now()->format('d-m-Y H:i'),
            'signature_token'   => $signatureToken,
        ];

        // Enkripsi data → DISIMPAN DI DB, BUKAN DI URL
        $encryptedData = Crypt::encryptString(json_encode($approvalData));

        // URL QR sekarang cuma pakai token (pendek)
        $qrUrl = route('invoice.approval', ['token' => $signatureToken]);

        // Generate QR (SVG)
        $qrSvg = QrCode::format('svg')->size(200)->generate($qrUrl);

        // Simpan QR di storage
        $fileName = 'qrcodes/invoice_' . $invoice->id . '_approved.svg';
        Storage::disk('public')->put($fileName, $qrSvg);

        // Update invoice
        $invoice->approval_status  = 'approved';
        $invoice->user_approve     = $user->id;
        $invoice->approved_at      = now();
        $invoice->approved_qr      = $fileName;
        $invoice->signature_token  = $signatureToken;   // token pendek
        $invoice->approval_payload = $encryptedData;    // payload enkripsi
        $invoice->save();

        return response()->json([
            'message' => 'Invoice approved & QR generated!'
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $invoice = Invoice::findOrFail($id);

        $invoice->update([
            'approval_status' => 'rejected',
            'reject_reason'   => $request->reason,
            'rejected_at'     => now(),
            'user_approve'    => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Invoice rejected with reason saved'
        ]);
    }

    public function showApproval($token)
    {
        // Cari invoice berdasarkan signature_token
        $invoice = Invoice::where('signature_token', $token)->firstOrFail();

        // Pastikan ada payload enkripsi
        if (!$invoice->approval_payload) {
            abort(404, 'Approval data not found');
        }

        try {
            // decrypt payload dari database
            $approvalData = json_decode(
                Crypt::decryptString($invoice->approval_payload),
                true
            );

            // validasi: token di payload harus sama
            if (($approvalData['signature_token'] ?? null) !== $invoice->signature_token) {
                abort(403, 'Invalid approval token');
            }

            return view('invoices.approval', [
                'approval' => $approvalData,
                'invoice'  => $invoice,
            ]);
        } catch (\Exception $e) {
            abort(404, 'Approval data not found or expired');
        }
    }
}
