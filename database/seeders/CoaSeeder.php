<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /**
         * NOTE:
         * - parent_code dipakai untuk mapping parent_id berdasarkan code_account_id
         * - set_as_group = true untuk heading/group (tidak boleh dipakai transaksi)
         * - default_posisi: 'debit' / 'credit'
         */
        $rows = [
            // ====== ASET ======
            ['code' => '1000', 'parent_code' => null,   'name' => 'ASET',                 'desc' => null, 'group' => true,  'pos' => 'debit'],
            ['code' => '1100', 'parent_code' => '1000', 'name' => 'Aset Lancar',          'desc' => null, 'group' => true,  'pos' => 'debit'],
            ['code' => '1110', 'parent_code' => '1100', 'name' => 'Kas',                  'desc' => 'Kas kecil / cash', 'group' => false, 'pos' => 'debit'],
            ['code' => '1111', 'parent_code' => '1100', 'name' => 'Bank BCA',             'desc' => null, 'group' => false, 'pos' => 'debit'],
            ['code' => '1112', 'parent_code' => '1100', 'name' => 'Bank BNI',             'desc' => null, 'group' => false, 'pos' => 'debit'],
            ['code' => '1120', 'parent_code' => '1100', 'name' => 'Piutang Usaha',        'desc' => null, 'group' => false, 'pos' => 'debit'],

            ['code' => '1200', 'parent_code' => '1000', 'name' => 'Aset Tetap',           'desc' => null, 'group' => true,  'pos' => 'debit'],
            ['code' => '1210', 'parent_code' => '1200', 'name' => 'Peralatan Lab',        'desc' => null, 'group' => false, 'pos' => 'debit'],
            ['code' => '1211', 'parent_code' => '1200', 'name' => 'Peralatan IT',         'desc' => null, 'group' => false, 'pos' => 'debit'],
            ['code' => '1220', 'parent_code' => '1200', 'name' => 'Akumulasi Penyusutan', 'desc' => null, 'group' => false, 'pos' => 'credit'],

            // ====== KEWAJIBAN ======
            ['code' => '2000', 'parent_code' => null,   'name' => 'KEWAJIBAN',            'desc' => null, 'group' => true,  'pos' => 'credit'],
            ['code' => '2100', 'parent_code' => '2000', 'name' => 'Hutang Usaha',         'desc' => null, 'group' => false, 'pos' => 'credit'],
            ['code' => '2200', 'parent_code' => '2000', 'name' => 'Hutang Pajak',         'desc' => null, 'group' => false, 'pos' => 'credit'],

            // ====== MODAL ======
            ['code' => '3000', 'parent_code' => null,   'name' => 'MODAL',                'desc' => null, 'group' => true,  'pos' => 'credit'],
            ['code' => '3100', 'parent_code' => '3000', 'name' => 'Modal Disetor',        'desc' => null, 'group' => false, 'pos' => 'credit'],
            ['code' => '3200', 'parent_code' => '3000', 'name' => 'Laba Ditahan',         'desc' => null, 'group' => false, 'pos' => 'credit'],

            // ====== PENDAPATAN ======
            ['code' => '4000', 'parent_code' => null,   'name' => 'PENDAPATAN',           'desc' => null, 'group' => true,  'pos' => 'credit'],
            ['code' => '4100', 'parent_code' => '4000', 'name' => 'Jasa Kalibrasi',       'desc' => null, 'group' => false, 'pos' => 'credit'],
            ['code' => '4200', 'parent_code' => '4000', 'name' => 'Jasa Welding Consultant','desc' => null, 'group' => false,'pos' => 'credit'],
            ['code' => '4300', 'parent_code' => '4000', 'name' => 'Jasa ISO Consultant',  'desc' => null, 'group' => false, 'pos' => 'credit'],
            ['code' => '4400', 'parent_code' => '4000', 'name' => 'Jasa IT Consultant',   'desc' => null, 'group' => false, 'pos' => 'credit'],

            // ====== BEBAN ======
            ['code' => '5000', 'parent_code' => null,   'name' => 'BEBAN',                'desc' => null, 'group' => true,  'pos' => 'debit'],
            ['code' => '5100', 'parent_code' => '5000', 'name' => 'Biaya Operasional',    'desc' => null, 'group' => true,  'pos' => 'debit'],
            ['code' => '5110', 'parent_code' => '5100', 'name' => 'Biaya Transport',      'desc' => null, 'group' => false, 'pos' => 'debit'],
            ['code' => '5120', 'parent_code' => '5100', 'name' => 'Biaya Konsumsi',       'desc' => null, 'group' => false, 'pos' => 'debit'],
            ['code' => '5130', 'parent_code' => '5100', 'name' => 'Biaya Listrik & Internet','desc'=> null,'group'=> false,'pos' => 'debit'],
            ['code' => '5140', 'parent_code' => '5100', 'name' => 'Biaya Maintenance Alat','desc'=> null,'group'=> false,'pos' => 'debit'],
            ['code' => '5200', 'parent_code' => '5000', 'name' => 'Biaya Gaji & Honor',   'desc' => null, 'group' => false, 'pos' => 'debit'],
        ];

        // 1) Upsert semua COA tanpa parent dulu (parent_id null)
        foreach ($rows as $r) {
            DB::table('coa')->updateOrInsert(
                ['code_account_id' => $r['code']],
                [
                    'parent_id'      => null,
                    'name'           => $r['name'],
                    'description'    => $r['desc'],
                    'set_as_group'   => $r['group'],
                    'default_posisi' => $r['pos'],
                    // mengikuti migration Boss: created_at & updated_at default current_timestamp
                    // tapi aman kita set juga:
                    'updated_at'     => $now,
                    'created_at'     => $now,
                ]
            );
        }

        // 2) Ambil mapping code => id
        $map = DB::table('coa')
            ->select('id', 'code_account_id')
            ->get()
            ->pluck('id', 'code_account_id'); // [code => id]

        // 3) Update parent_id berdasarkan parent_code
        foreach ($rows as $r) {
            if (!$r['parent_code']) continue;

            $childId  = $map[$r['code']] ?? null;
            $parentId = $map[$r['parent_code']] ?? null;

            if ($childId && $parentId) {
                DB::table('coa')->where('id', $childId)->update([
                    'parent_id'  => $parentId,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
