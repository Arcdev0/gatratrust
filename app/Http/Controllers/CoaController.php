<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CoaController extends Controller
{
    public function index()
    {
        $groupAccounts = Coa::select('id', 'code_account_id', 'name')
            ->where('set_as_group', true)
            ->orderBy('code_account_id')
            ->get();

        // load tree 3 level (bisa ditambah kalau mau)
        $coaList = Coa::whereNull('parent_id')
            ->with(['children.children'])
            ->orderBy('code_account_id')
            ->get();

        return view('coa.index', [
            'groupAccounts' => $groupAccounts,
            'coaList'       => $coaList,
        ]);
    }

    /**
     * AJAX: ambil kode akun berikutnya berdasarkan parent_id
     * input: accountId (nullable)
     */
    public function getNextAccountCode(Request $request)
    {
        $request->validate([
            'accountId' => ['nullable', 'integer', Rule::exists('coa', 'id')],
        ]);

        $parentId = $request->accountId;

        $lastAccount = Coa::when($parentId, fn($q) => $q->where('parent_id', $parentId))
            ->when(!$parentId, fn($q) => $q->whereNull('parent_id'))
            ->orderBy('code_account_id', 'desc')
            ->first();

        $nextAccountCode = $this->generateNextAccountCode($parentId, $lastAccount?->code_account_id);

        return response()->json(['next_account_code' => $nextAccountCode]);
    }

    /**
     * Generator kode akun:
     * - Kalau ada lastCode: increment segmen terakhir (support format "1110" atau "1110.01")
     * - Kalau belum ada anak di parent tsb:
     *    - jika parent ada: parentCode + ".01"
     *    - jika parent null: ambil max root lalu +10 (contoh 1000 -> 1010) (silakan ubah rule)
     */
    private function generateNextAccountCode(?int $parentId, ?string $lastCode): string
    {
        // Ambil kode parent kalau ada
        $parentCode = null;
        if ($parentId) {
            $parentCode = Coa::where('id', $parentId)->value('code_account_id');
        }

        // CASE 1: sudah ada lastCode -> increment
        if ($lastCode) {
            // Support "1110" dan "1110.01"
            if (str_contains($lastCode, '.')) {
                $parts = explode('.', $lastCode);
                $lastPart = array_pop($parts);
                $next = (int)$lastPart + 1;
                return implode('.', $parts) . '.' . str_pad((string)$next, 2, '0', STR_PAD_LEFT);
            }

            // kalau format pure number, increment +10 (rule sederhana)
            // contoh 1110 -> 1120
            $num = (int)$lastCode;
            return (string)($num + 10);
        }

        // CASE 2: belum ada lastCode
        if ($parentCode) {
            // pertama kali buat anak -> parent.01
            // contoh 1100 -> 1100.01
            return $parentCode . '.01';
        }

        // CASE 3: root pertama kali (kalau tabel kosong)
        // atau kalau belum ada root sama sekali
        $maxRoot = Coa::whereNull('parent_id')->max('code_account_id');
        if (!$maxRoot) return '1000';

        // root increment +10
        $num = (int)$maxRoot;
        return (string)($num + 10);
    }

    /**
     * AJAX: Render HTML table tree COA (rekursif)
     */
    public function getlistcoa()
    {
        $data = Coa::with('children')->whereNull('parent_id')
            ->orderBy('code_account_id')
            ->get();

        $output = '<table class="table align-items-center table-flush table-hover" id="tableCOA">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 200px;">Account Code</th>
                            <th>Nama</th>
                            <th style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($data as $item) {
            $output .= $this->renderRow($item);
        }

        $output .= '</tbody></table>';

        return response()->json(['html' => $output]);
    }

    private function renderRow($item, int $level = 0): string
    {
        $padding = $level * 18;

        $badge = $item->set_as_group
            ? '<span class="badge bg-primary ms-2">GROUP</span>'
            : '<span class="badge bg-secondary ms-2">' . e(strtoupper($item->default_posisi ?? '-')) . '</span>';

        $row = '
        <tr>
            <td style="padding-left: ' . $padding . 'px;">' . e($item->code_account_id ?? '-') . '</td>
            <td>' . e($item->name ?? '-') . ' ' . $badge . '</td>
            <td>
                <button type="button" class="btn btndeleteCOA btn-sm btn-danger text-white" data-id="' . e($item->id) . '">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button type="button" class="btn editCOA btn-sm btn-secondary text-white" data-id="' . e($item->id) . '">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        </tr>';

        if ($item->children && $item->children->count() > 0) {
            foreach ($item->children as $child) {
                $row .= $this->renderRow($child, $level + 1);
            }
        }

        return $row;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'code_account_id'   => ['required', 'string', 'max:50', Rule::unique('coa', 'code_account_id')],
                'name'              => ['required', 'string', 'max:255'],
                'description'       => ['nullable', 'string'],
                'set_group'         => ['nullable'], // dari UI (1/0)
                'default_position'  => ['required', Rule::in(['debit', 'credit'])],
                'group_account'     => ['nullable', 'integer', Rule::exists('coa', 'id')],
            ]);

            $coa = new Coa();
            $coa->code_account_id = $validated['code_account_id'];
            $coa->name            = $validated['name'];
            $coa->description     = $validated['description'] ?? null;
            $coa->set_as_group    = (int)($validated['set_group'] ?? 0) === 1;
            $coa->default_posisi  = $validated['default_position'];
            $coa->parent_id       = $validated['group_account'] ?? null;

            $coa->save();

            DB::commit();
            return response()->json(['success' => 'COA berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $coa = Coa::findOrFail($id);
        return response()->json($coa);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'code_account_id' => ['required', 'string', 'max:50', Rule::unique('coa', 'code_account_id')->ignore($id)],
                'name'            => ['required', 'string', 'max:255'],
                'description'     => ['nullable', 'string'],
                'set_as_group'    => ['required', 'boolean'],
                'default_posisi'  => ['required', Rule::in(['debit', 'credit'])],
                'group_account'   => ['nullable', 'integer', Rule::exists('coa', 'id')],
            ]);

            $coa = Coa::findOrFail($id);

            // optional: cegah parent_id = diri sendiri
            if (!empty($validated['group_account']) && (int)$validated['group_account'] === (int)$coa->id) {
                return response()->json(['status' => 'error', 'message' => 'Parent tidak boleh dirinya sendiri'], 422);
            }

            $coa->code_account_id = $validated['code_account_id'];
            $coa->name            = $validated['name'];
            $coa->description     = $validated['description'] ?? null;
            $coa->set_as_group    = (bool)$validated['set_as_group'];
            $coa->default_posisi  = $validated['default_posisi'];
            $coa->parent_id       = $validated['group_account'] ?? null;

            $coa->save();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $coa = Coa::findOrFail($id);
            $coa->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // foreign key constraint (umumnya 23000)
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Data ini tidak bisa dihapus karena sudah dipakai pada data lain'
                ], 400);
            }

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
