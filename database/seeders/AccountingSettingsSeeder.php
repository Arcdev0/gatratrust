<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountingSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Ambil id COA berdasarkan code_account_id
        $coa = DB::table('coa')
            ->select('id', 'code_account_id')
            ->get()
            ->pluck('id', 'code_account_id');

        // Pastikan code ini ada di CoaSeeder:
        $defaultCash = $coa['1110'] ?? null; // Kas
        $defaultBank = $coa['1111'] ?? null; // Bank BCA
        $suspense    = $coa['2100'] ?? null; // Hutang Usaha (sementara)
        $retained    = $coa['3200'] ?? null; // Laba Ditahan

        // Hanya 1 row (id=1) biar konsisten
        DB::table('accounting_settings')->updateOrInsert(
            ['id' => 1],
            [
                'default_cash_coa_id'           => $defaultCash,
                'default_bank_coa_id'           => $defaultBank,
                'default_suspense_coa_id'       => $suspense,
                'default_retained_earning_coa_id' => $retained,

                'journal_prefix'                => 'JR',
                'journal_running_number'        => 1,
                'fiscal_year_start_month'       => 1,

                'created_at'                    => $now,
                'updated_at'                    => $now,
            ]
        );
    }
}
