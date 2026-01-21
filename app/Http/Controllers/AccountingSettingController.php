<?php

namespace App\Http\Controllers;

use App\Models\AccountingSetting;
use App\Models\Coa;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccountingSettingController extends Controller
{
    public function index()
    {
        $setting = AccountingSetting::first(); // 1 row saja

        // COA non-group
        $coaSelectable = Coa::where('set_as_group', false)
            ->orderBy('code_account_id')
            ->get(['id', 'code_account_id', 'name']);

        // Wallet yang sudah dipilih (untuk setting id=1)
        $walletSelectedIds = Wallet::where('accounting_setting_id', 1)
            ->pluck('coa_id')
            ->toArray();

        return view('accounting-settings.index', [
            'setting'           => $setting,
            'coaSelectable'     => $coaSelectable,
            'walletSelectedIds' => $walletSelectedIds,
        ]);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {

            $validated = $request->validate([
                // wallets: array of coa ids (multi select)
                'wallet_coa_ids' => ['nullable', 'array'],
                'wallet_coa_ids.*' => ['integer', Rule::exists('coa', 'id')],

                // COA mapping
                'default_ar_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],
                'default_sales_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],
                'default_tax_payable_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],
                'default_expense_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],
                'default_suspense_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],
                'default_retained_earning_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],

                'journal_prefix' => ['required', 'string', 'max:10'],
                'journal_running_number' => ['required', 'integer', 'min:1'],
                'fiscal_year_start_month' => ['required', 'integer', 'min:1', 'max:12'],
            ]);

            // 1) Save accounting_settings (exclude wallets)
            $settingPayload = collect($validated)->except(['wallet_coa_ids'])->toArray();

            $setting = AccountingSetting::updateOrCreate(
                ['id' => 1],
                $settingPayload
            );

            // 2) Sync wallets (table wallets)
            $walletCoaIds = collect($validated['wallet_coa_ids'] ?? [])
                ->filter(fn($v) => !is_null($v) && $v !== '')
                ->map(fn($v) => (int) $v)
                ->unique()
                ->values();

            // Ambil existing wallet coa ids
            $existing = Wallet::where('accounting_setting_id', 1)
                ->pluck('coa_id')
                ->map(fn($v) => (int) $v);

            // Yang harus ditambah
            $toInsert = $walletCoaIds->diff($existing)->values();

            // Yang harus dihapus
            $toDelete = $existing->diff($walletCoaIds)->values();

            if ($toDelete->isNotEmpty()) {
                Wallet::where('accounting_setting_id', 1)
                    ->whereIn('coa_id', $toDelete->all())
                    ->delete();
            }

            if ($toInsert->isNotEmpty()) {
                $rows = $toInsert->map(fn($coaId) => [
                    'accounting_setting_id' => 1,
                    'coa_id' => $coaId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();

                Wallet::insert($rows);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $setting,
                'wallet_coa_ids' => $walletCoaIds,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
