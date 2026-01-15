<?php

namespace App\Http\Controllers;

use App\Models\AccountingSetting;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccountingSettingController extends Controller
{
    public function index()
    {
        $setting = AccountingSetting::query()->first(); // 1 row

        $coaList = Coa::select('id', 'code_account_id', 'name', 'set_as_group')
            ->orderBy('code_account_id')
            ->get();

        // biasanya dropdown pakai akun yang bukan group
        $coaSelectable = $coaList->where('set_as_group', false)->values();

        return view('accounting-settings.index', [
            'setting' => $setting,
            'coaSelectable' => $coaSelectable,
        ]);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'default_cash_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],
                'default_bank_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],
                'default_suspense_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],
                'default_retained_earning_coa_id' => ['nullable', 'integer', Rule::exists('coa', 'id')],

                'journal_prefix' => ['required', 'string', 'max:10'],
                'journal_running_number' => ['required', 'integer', 'min:1'],
                'fiscal_year_start_month' => ['required', 'integer', 'min:1', 'max:12'],
            ]);

            // paksa 1 row saja id=1
            $setting = AccountingSetting::updateOrCreate(
                ['id' => 1],
                $validated
            );

            DB::commit();
            return response()->json(['success' => true, 'data' => $setting]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
