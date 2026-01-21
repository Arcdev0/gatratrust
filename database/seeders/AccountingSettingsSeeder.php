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

        // ==== Mapping COA (sesuaikan dengan CoaSeeder kamu)
        $ar        = $coa['1120'] ?? null; // Accounts Receivable / Piutang Usaha (contoh)
        $sales     = $coa['4100'] ?? null; // Sales/Revenue (contoh)
        $taxPay    = $coa['2105'] ?? null; // Tax Payable / PPN Keluaran (contoh)
        $expense   = $coa['5100'] ?? null; // Expense default (contoh)
        $suspense  = $coa['9999'] ?? null; // Suspense (contoh)
        $retained  = $coa['3200'] ?? null; // Laba Ditahan (contoh)

        // Wallet COA (ambil dari COA yang kamu mau jadi sumber dana)
        $walletCash = $coa['1110'] ?? null; // Kas
        $walletBank = $coa['1111'] ?? null; // Bank BCA

        // ==== Pastikan minimal AR & Sales ada (biar seed tidak silent fail)
        // Kalau belum ada, biarkan null (tidak throw) — tapi kamu bisa aktifkan throw jika mau strict.
        // if (!$ar || !$sales) throw new \Exception("COA AR atau Sales belum ada. Cek CoaSeeder.");

        // ==== Accounting Settings: 1 row saja (id=1)
        DB::table('accounting_settings')->updateOrInsert(
            ['id' => 1],
            [
                // cash/bank sudah dihapus dari table ini (diganti wallets)
                'default_ar_coa_id'               => $ar,
                'default_sales_coa_id'            => $sales,
                'default_tax_payable_coa_id'      => $taxPay,
                'default_expense_coa_id'          => $expense,
                'default_suspense_coa_id'         => $suspense,
                'default_retained_earning_coa_id' => $retained,

                'journal_prefix'                  => 'JR',
                'journal_running_number'          => 1,
                'fiscal_year_start_month'         => 1,

                'created_at'                      => $now,
                'updated_at'                      => $now,
            ]
        );

        // ==== Seed wallets (simple pivot): accounting_setting_id + coa_id
        // Bersihkan dulu agar idempotent saat seeder dijalankan berulang
        DB::table('wallets')->where('accounting_setting_id', 1)->delete();

        $walletRows = [];
        if ($walletCash) {
            $walletRows[] = [
                'accounting_setting_id' => 1,
                'coa_id' => $walletCash,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if ($walletBank) {
            $walletRows[] = [
                'accounting_setting_id' => 1,
                'coa_id' => $walletBank,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($walletRows)) {
            DB::table('wallets')->insert($walletRows);
        }
    }
}
