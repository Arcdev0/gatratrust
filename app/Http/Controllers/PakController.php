<?php

namespace App\Http\Controllers;

use App\Http\Requests\PakStoreRequest;
use App\Http\Requests\PakUpdateRequest;
use App\Models\KaryawanData;
use App\Models\Pak;
use App\Models\PakItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PakController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $manageActions = ['create', 'store', 'edit', 'update', 'destroy'];

            if (in_array($request->route()->getActionMethod(), $manageActions, true) && auth()->user()?->role_id !== 3) {
                abort(403, 'Hanya Superadmin yang boleh mengelola PAK.');
            }

            return $next($request);
        });
    }

    public function index()
    {

        return view('pak.index', ['canManagePak' => auth()->user()?->role_id === 3]);
    }

    public function getDataTable(Request $request)
    {
        if ($request->ajax()) {

            $data = Pak::with('karyawans')
                ->select(
                    'id',
                    'pak_number as project_number',
                    'pak_name as project_name',
                    'pak_value as project_value',
                    'location',
                    'date',
                    'total_pak_cost',
                    'estimated_profit',
                    'created_at'
                )
                ->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()

                // format project_value
                ->editColumn('project_value', function ($row) {
                    return 'Rp '.number_format($row->project_value ?? 0, 0, ',', '.');
                })

                ->editColumn('total_pak_cost', function ($row) {
                    return 'Rp '.number_format($row->total_pak_cost ?? 0, 0, ',', '.');
                })

                ->editColumn('estimated_profit', function ($row) {
                    return 'Rp '.number_format($row->estimated_profit ?? 0, 0, ',', '.');
                })

                ->editColumn('date', function ($row) {
                    return $row->date
                        ? \Carbon\Carbon::parse($row->date)->format('d-m-Y')
                        : '-';
                })

                ->editColumn('location', function ($row) {
                    return $row->location === 'dalam_kota' ? 'Dalam Kota' : 'Luar Kota';
                })

                // kembalikan employees sebagai array of objects (client akan meng-render)
                ->addColumn('employees', function ($row) {
                    // pastikan relasi bernama 'karyawans' dan model Karyawan punya attribute nama_lengkap
                    return $row->karyawans ? $row->karyawans->toArray() : [];
                })

                ->addColumn('action', function ($row) {
                    $buttons = '
                        <button class="btn btn-sm btn-info showBtn" data-id="'.$row->id.'">
                            <i class="fas fa-eye"></i>
                        </button>

                        <a href="'.route('pak.printPDF', $row->id).'" target="_blank" class="btn btn-sm btn-secondary">
                            <i class="fas fa-file-pdf"></i>
                        </a>

                    ';

                    if (auth()->user()?->role_id === 3) {
                        $buttons .= '
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">
                                <i class="fas fa-trash"></i>
                            </button>
                        ';
                    }

                    return $buttons;
                })

                // <a href="' . route('pak.edit', $row->id) . '" class="btn btn-sm btn-secondary">
                //         <i class="fas fa-edit"></i>
                //     </a>

                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        $employees = KaryawanData::orderBy('nama_lengkap', 'asc')->get();

        $year = date('Y');
        $month = date('m');
        $monthYear = "{$month}-{$year}"; // 01-2026

        // Ambil PAK terakhir khusus tahun berjalan (berdasarkan pak_number)
        $lastPakThisYear = Pak::where('pak_number', 'like', "%/GPT-PAK/%-{$year}")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPakThisYear) {
            $lastNumber = (int) explode('/', $lastPakThisYear->pak_number)[0];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $runningNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        $newPakNo = "{$runningNumber}/GPT-PAK/{$monthYear}";

        $paks = Pak::orderBy('created_at', 'desc')->get();
        $categories = DB::table('categories')->get();

        return view('pak.create', compact('employees', 'newPakNo', 'paks', 'categories'));
    }

    public function copy($id)
    {
        $pak = Pak::with('items')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $pak,
        ]);
    }

    public function store(PakStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $parseNumber = function ($v) {
                if ($v === null) {
                    return 0;
                }
                if (is_numeric($v)) {
                    return $v + 0;
                }
                // remove all non-digit
                $s = preg_replace('/[^\d\-]/', '', (string) $v);

                return $s === '' ? 0 : (int) $s;
            };

            $pak = Pak::create([
                'pak_name' => $validated['project_name'],
                'pak_number' => $validated['project_number'],
                'pak_value' => $parseNumber($validated['project_value']),
                'location' => $validated['location_project'],
                'date' => $validated['date'],
                'customer_name' => $validated['customer_name'],
                'customer_address' => $validated['customer_address'] ?? null,
                'attention' => $validated['attention'] ?? null,
                'your_reference' => $validated['your_reference'] ?? null,
                'terms_text' => $validated['terms_text'] ?? null,
                'pph_23' => $parseNumber($validated['pph23'] ?? 0),
                'ppn' => $parseNumber($validated['ppn11'] ?? 0),
                'total_pak_cost' => $parseNumber($validated['project_cost'] ?? 0),
            ]);

            if ($request->filled('employee')) {
                $pak->karyawans()->sync($request->input('employee', []));
            }

            foreach (($validated['scopes_master'] ?? []) as $index => $scope) {
                $pak->scopesMaster()->create([
                    'description' => $scope['description'],
                    'responsible_pt_gpt' => ! empty($scope['responsible_pt_gpt']),
                    'responsible_client' => ! empty($scope['responsible_client']),
                    'sort_order' => $index,
                ]);
            }

            foreach (($validated['terms_master'] ?? []) as $index => $term) {
                $pak->termsMaster()->create([
                    'description' => $term['description'],
                    'sort_order' => $index,
                ]);
            }

            $itemsInput = $request->input('items', []);

            // dd($itemsInput);
            foreach ($itemsInput as $categoryId => $rows) {
                // pastikan categoryId numeric (safety)
                $catId = is_numeric($categoryId) ? (int) $categoryId : null;

                foreach ($rows as $row) {
                    // fallback/defaults
                    $operational = $row['operational_needs'] ?? null;
                    if (empty($operational)) {
                        continue;
                    } // skip empty row

                    $description = $row['description'] ?? null;
                    $qty = $parseNumber($row['qty'] ?? 0);
                    $unitCost = $parseNumber($row['unit_cost'] ?? 0);
                    $totalCost = $parseNumber($row['total_cost'] ?? ($qty * $unitCost));
                    $maxCost = $parseNumber($row['max_cost'] ?? 0);
                    $percent = $row['percent'] ?? null;
                    $status = $row['status'] ?? 'OK';

                    PakItem::create([
                        'pak_id' => $pak->id,
                        'category_id' => $catId,
                        'name' => $operational,
                        'description' => $description,
                        'quantity' => $qty,
                        'unit_cost' => $unitCost,
                        'total_cost' => $totalCost,
                        // 'max_cost' => $maxCost,
                        // 'percent' => $percent,
                        // 'status' => $status,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 201,
                'message' => 'PAK berhasil ditambahkan!',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Validation Error',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Eager load items dan karyawans (jika relasi ada)
            $pak = Pak::with(['items', 'karyawans', 'scopesMaster', 'termsMaster'])->findOrFail($id);

            // Ambil employees:
            // - jika relasi karyawans ada dan terisi, pakai itu
            // - jika relasi kosong tapi ada kolom legacy 'employee' yang menyimpan JSON, fallback ke sana
            $employees = collect([]);

            if ($pak->relationLoaded('karyawans') && $pak->karyawans->isNotEmpty()) {
                $employees = $pak->karyawans;
            } else {
                // fallback: coba decode kolom employee (legacy)
                $employeeIds = null;
                if (! empty($pak->employee)) {
                    // Kalau sudah array, gunakan langsung; kalau string JSON, decode
                    if (is_array($pak->employee)) {
                        $employeeIds = $pak->employee;
                    } else {
                        $employeeIds = @json_decode($pak->employee, true);
                    }
                }

                if (is_array($employeeIds) && count($employeeIds) > 0) {
                    $employees = KaryawanData::whereIn('id', $employeeIds)->get();
                }
            }

            $categories = DB::table('categories')->get();

            // Optional: jika ingin memformat angka/tanggal di response, lakukan mapping di sini.
            // Contoh sederhana mengembalikan raw model + employees
            return response()->json([
                'success' => true,
                'data' => [
                    'pak' => $pak,
                    'employees' => $employees,
                    'items' => $pak->items ?? collect([]),
                    'categories' => $categories,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'PAK tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            // Log error jika perlu: \Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $pak = Pak::with('items')->findOrFail($id);
        $employees = KaryawanData::orderBy('nama_lengkap', 'asc')->get();

        // Decode employee JSON
        $selectedEmployees = json_decode($pak->employee, true);

        return view('pak.edit', compact('pak', 'employees', 'selectedEmployees'));
    }

    public function update(PakUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $parseNumber = function ($v) {
                if ($v === null) {
                    return 0;
                }
                if (is_numeric($v)) {
                    return $v + 0;
                }
                $s = preg_replace('/[^\d\-]/', '', (string) $v);

                return $s === '' ? 0 : (int) $s;
            };

            $pak = Pak::findOrFail($id);
            $pak->update([
                'pak_name' => $validated['project_name'],
                'pak_number' => $validated['project_number'],
                'pak_value' => $parseNumber($validated['project_value']),
                'location' => $validated['location_project'],
                'date' => $validated['date'],
                'customer_name' => $validated['customer_name'],
                'customer_address' => $validated['customer_address'] ?? null,
                'attention' => $validated['attention'] ?? null,
                'your_reference' => $validated['your_reference'] ?? null,
                'terms_text' => $validated['terms_text'] ?? null,
                'pph_23' => $parseNumber($validated['pph23'] ?? 0),
                'ppn' => $parseNumber($validated['ppn11'] ?? 0),
                'total_pak_cost' => $parseNumber($validated['project_cost'] ?? 0),
            ]);

            $pak->karyawans()->sync($validated['employee'] ?? []);

            $pak->items()->delete();
            foreach (($validated['items'] ?? []) as $categoryId => $rows) {
                $catId = is_numeric($categoryId) ? (int) $categoryId : null;

                foreach ($rows as $row) {
                    $operational = $row['operational_needs'] ?? null;
                    if (empty($operational)) {
                        continue;
                    }

                    PakItem::create([
                        'pak_id' => $pak->id,
                        'category_id' => $catId,
                        'name' => $operational,
                        'description' => $row['description'] ?? null,
                        'quantity' => $parseNumber($row['qty'] ?? 0),
                        'unit_cost' => $parseNumber($row['unit_cost'] ?? 0),
                        'total_cost' => $parseNumber($row['total_cost'] ?? 0),
                    ]);
                }
            }

            $pak->scopesMaster()->delete();
            foreach (($validated['scopes_master'] ?? []) as $index => $scope) {
                $pak->scopesMaster()->create([
                    'description' => $scope['description'],
                    'responsible_pt_gpt' => ! empty($scope['responsible_pt_gpt']),
                    'responsible_client' => ! empty($scope['responsible_client']),
                    'sort_order' => $index,
                ]);
            }

            $pak->termsMaster()->delete();
            foreach (($validated['terms_master'] ?? []) as $index => $term) {
                $pak->termsMaster()->create([
                    'description' => $term['description'],
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PAK berhasil diupdate!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pak = Pak::findOrFail($id);
            $pak->items()->delete();
            $pak->delete();

            return response()->json([
                'success' => true,
                'message' => 'PAK berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper function to determine category based on item index
     */
    private function determineCategoryFromIndex($index, $total)
    {
        // Logika sederhana: bisa disesuaikan dengan kebutuhan

        $third = ceil($total / 3);

        if ($index < $third) {
            return 'honorarium';
        } elseif ($index < $third * 2) {
            return 'operational';
        } else {
            return 'consumable';
        }
    }

    public function printPDF($id)
    {
        $pak = Pak::with('items')->findOrFail($id);

        // Ambil categories manual tanpa model
        $categories = DB::table('categories')->get()->keyBy('id');
        $prefix = substr($pak->pak_number, 0, 3);
        $time = now()->format('His');
        $generatedNumber = $prefix.'-'.$time;

        $pdf = PDF::loadView('pak.pdf', [
            'pak' => $pak,
            'categories' => $categories,
            'generatedNumber' => $generatedNumber,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("PAK-{$generatedNumber}.pdf");
    }
}
