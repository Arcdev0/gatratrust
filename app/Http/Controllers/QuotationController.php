<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
            $data = Quotation::with('status')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return $row->date
                        ? \Carbon\Carbon::parse($row->date)->format('d-m-Y')
                        : '';
                })
                ->addColumn('status_name', function ($row) {
                    if (!$row->status) {
                        return '<span class="badge bg-secondary">-</span>';
                    }

                    return match (strtolower($row->status->name)) {
                        'pending'  => '<span class="badge bg-yellow text-white">Pending</span>',
                        'approve'  => '<span class="badge bg-success text-white">Approve</span>',
                        'rejected' => '<span class="badge bg-danger text-white">Rejected</span>',
                        default    => '<span class="badge bg-secondary text-white">'.$row->status->name.'</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    $btns = '
                        <button class="btn btn-sm btn-info showBtn"
                                data-id="'.$row->id.'">Show</button>
                    ';

                    $user = auth()->user();
                    $roleName = strtolower($user->role->name ?? ''); // handle kalau role null

                    if ((int) $row->status_id === 2) {
                        // Approved → hanya PDF
                        $btns .= ' <a href="'.route('quotations.exportPdf', $row->id).'"
                                    class="btn btn-sm btn-secondary" target="_blank">PDF</a>';
                    } elseif ((int) $row->status_id === 3) {
                        // Rejected → hanya Delete
                        $btns .= ' <button class="btn btn-sm btn-danger deleteBtn"
                                            data-id="'.$row->id.'">Delete</button>';
                    } else {
                        // Pending
                        if (in_array($roleName, ['superadmin', 'keuangan'])) {
                            $btns .= '
                                <button class="btn btn-sm btn-success approveBtn"
                                        data-id="'.$row->id.'">Approve</button>
                                <button class="btn btn-sm btn-warning rejectBtn"
                                        data-id="'.$row->id.'">Reject</button>
                            ';
                        }

                        // semua role tetap bisa Delete kalau pending
                        $btns .= '
                            <button class="btn btn-sm btn-danger deleteBtn"
                                    data-id="'.$row->id.'">Delete</button>
                        ';
                    }

                    return $btns;
                })
                ->rawColumns(['action', 'status_name'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('quotations.create');
    }

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
                    'scopes.*.description' => 'required_with:scopes|string',
                    'scopes.*.responsible_pt_gpt' => 'nullable|boolean',
                    'scopes.*.responsible_client' => 'nullable|boolean',
                    'terms_conditions' => 'nullable|array',
                    'terms_conditions.*.description' => 'required_with:terms_conditions|string|max:500',

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
                    'status_id' => 1,
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

                if (!empty($validated['scopes'])) {
                    foreach ($validated['scopes'] as $scope) {
                        $quotation->scopes()->create([
                            'description' => $scope['description'],
                            'responsible_pt_gpt' => !empty($scope['responsible_pt_gpt']) ? 1 : 0,
                            'responsible_client' => !empty($scope['responsible_client']) ? 1 : 0,
                        ]);
                    }
                }

                if (!empty($validated['terms_conditions'])) {
                    foreach ($validated['terms_conditions'] as $term) {
                        $quotation->terms()->create([
                            'description' => $term['description'],
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

        // dd( $quotation->approved_qr);

        // Konversi gambar QR code ke base64
         $qrCodeBase64 = null;
            if ($quotation->approved_qr) {
                try {
                    if (Storage::disk('public')->exists($quotation->approved_qr)) {
                        $qrCodeData = Storage::disk('public')->get($quotation->approved_qr);
                        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeData);
                    }
                } catch (\Exception $e) {
                    \Log::error('QR Code Error: '.$e->getMessage());
                }
            }

        $pdf = Pdf::loadView('quotations.pdf', [
            'quotation' => $quotation,
            'qrCodeBase64' => $qrCodeBase64
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('quotation.pdf');
    }

    public function show($id)
    {
        $quotation = Quotation::with(['items', 'scopes', 'status', 'terms'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $quotation
        ]);
    }


    public function approve($id)
    {
        $quotation = Quotation::findOrFail($id);

        // Cegah double approval
        if ($quotation->status_id == 2) {
            return back()->with('warning', 'Quotation already approved!');
        }

        // User yang approve
        $user = auth()->user();

        // Data approval
        $approvalData = [
            'quotation_id'      => $quotation->id,
            'approver_id'       => $user->id,
            'approver_name'     => $user->name,
            'approver_position' => $user->role->name ?? 'Keuangan',
            'quotation_no'      => $quotation->quo_no,
            'approval_date'     => now()->format('d-m-Y H:i'),
            'signature_token'   => Str::random(32),
        ];

        // Enkripsi data
        $encryptedData = Crypt::encryptString(json_encode($approvalData));

        // URL yang akan dimasukkan ke QR
        $qrUrl = route('quotation.approval', ['encryptedData' => $encryptedData]);

        // Generate QR (SVG)
        $qrSvg = QrCode::format('svg')->size(200)->generate($qrUrl);

        // Simpan di storage
        $fileName = 'qrcodes/quotation_'.$quotation->id.'_approved.svg';
        Storage::disk('public')->put($fileName, $qrSvg);

        // Ubah ke base64 supaya tetap kompatibel dengan PDF
        $qrImage = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        // Update quotation
        $quotation->status_id       = 2; // approved
        $quotation->approved_by     = $user->id;
        $quotation->approved_qr     = $fileName;
        $quotation->approved_at     = now();
        $quotation->signature_token = $approvalData['signature_token']; // SIMPAN TOKEN
        $quotation->save();

        return back()->with('success', 'Quotation approved & QR generated!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $quotation = Quotation::findOrFail($id);
        $quotation->update([
            'rejected_reason' => $request->reason,
            'status_id' => 3
        ]);

        return response()->json(['message' => 'Quotation rejected with reason saved']);
    }


     public function showApproval($encryptedData)
    {
        try {
            $decrypted = Crypt::decryptString($encryptedData);
            $approvalData = json_decode($decrypted, true);

            $quotation = Quotation::findOrFail($approvalData['quotation_id']);

            if ($quotation->signature_token !== $approvalData['signature_token']) {
                abort(403, 'Invalid approval token');
            }

            return view('quotations.approval', [
                'approval'  => $approvalData,
                'quotation' => $quotation,
            ]);
        } catch (\Exception $e) {
            abort(404, 'Approval data not found or expired');
        }
    }


}
