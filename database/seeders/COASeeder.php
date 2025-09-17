<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\COA;

class COASeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            // ['code_account_id' => '1.0.00', 'name' => 'ASET', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 1
            // ['code_account_id' => '1.1.00', 'name' => 'ASET LANCAR', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 1 ],  //id = 2

            // ['code_account_id' => '1.1.01', 'name' => 'KAS', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 3
            // ['code_account_id' => '1.1.01.01', 'name' => 'KAS IDR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 3 ], //id = 4
            // ['code_account_id' => '1.1.01.02', 'name' => 'KAS KAS', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 3 ], //id = 5
            // ['code_account_id' => '1.1.01.03', 'name' => 'KAS SGD', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 3 ], //id = 6

            // ['code_account_id' => '1.1.02', 'name' => 'BANK', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 7
            // ['code_account_id' => '1.1.02.01', 'name' => 'BANK BCA IDR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7] , //id = 8
            // ['code_account_id' => '1.1.02.02', 'name' => 'BANK BCA SGD', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 9
            // ['code_account_id' => '1.1.02.03', 'name' => 'BANK MANDIRI IDR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 10
            // ['code_account_id' => '1.1.02.04', 'name' => 'BANK BNI SGD', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 11
            // ['code_account_id' => '1.1.02.05', 'name' => 'BANK NIAGA IDR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 12
            // ['code_account_id' => '1.1.02.06', 'name' => 'BANK NIAGA SGD', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 13
            // ['code_account_id' => '1.1.02.07', 'name' => 'BANK BCA CHANDRA SUSANTO', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 14

            // ['code_account_id' => '1.1.03', 'name' => ' PIUTANG', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 15
            // ['code_account_id' => '1.1.03.01 ', 'name' => 'PIUTANG USAHA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 15 ], //id = 16
            // ['code_account_id' => '1.1.03.02', 'name' => 'PIUTANG LAIN-LAIN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 15 ], //id = 17

            // ['code_account_id' => '1.1.05', 'name' => 'PERLENGKAPAN', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 18
            // ['code_account_id' => '1.1.05.01', 'name' => 'PERLENGKAPAN KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 18 ], //id = 19

            // ['code_account_id' => '1.1.06', 'name' => 'PAJAK DIBAYAR DIMUKA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 20
            // ['code_account_id' => '1.1.06.01', 'name' => 'PPN MASUKAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 20 ], //id = 21
            // ['code_account_id' => '1.1.06.02', 'name' => ' PPH 21 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 20 ], //id = 22
            // ['code_account_id' => '1.1.06.03', 'name' => 'PPH 22 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 20 ], //id = 23
            // ['code_account_id' => '1.1.06.04', 'name' => 'PPH 23 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 20 ], //id = 24
            // ['code_account_id' => '1.1.06.05', 'name' => 'PPH 24 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 20 ], //id = 25
            // ['code_account_id' => '1.1.06.06', 'name' => 'PPH 25 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 20 ], //id = 26
            // ['code_account_id' => '1.1.06.07', 'name' => 'PPH LEBIH BAYAR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 20 ], //id = 27
            // ['code_account_id' => '1.1.06.08', 'name' => 'PPH 15 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 20 ], //id = 28

            // ['code_account_id' => '1.1.07', 'name' => 'BIAYA DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 29
            // ['code_account_id' => '1.1.07.01', 'name' => 'UANG MUKA PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 29 ], //id = 30
            // ['code_account_id' => '1.1.07.02', 'name' => 'SEWA DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 29 ], //id = 31
            // ['code_account_id' => '1.1.07.03', 'name' => 'ASURANSI DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 29 ], //id = 32
            // ['code_account_id' => '1.1.07.04', 'name' => 'UANG MUKA PEMBELIAN KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 29 ], //id = 33

            // ['code_account_id' => '1.2.00', 'name' => 'ASET TETAP', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 1 ], //id = 34
            // ['code_account_id' => '1.2.01', 'name' => 'ASET TETAP BERWUJUD', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 34 ], //id = 35
            // ['code_account_id' => '1.2.01.03', 'name' => 'KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 35 ], //id = 36
            // ['code_account_id' => '1.2.01.05', 'name' => 'PERALATAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 35 ], //id = 37
            // ['code_account_id' => '1.2.01.06', 'name' => 'INVENTARIS KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 35 ], //id = 38
            // ['code_account_id' => '1.2.01.09', 'name' => 'AKUMULASI PENYUSUTAN KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 35 ], //id = 39
            // ['code_account_id' => '1.2.01.11', 'name' => 'AKUMULASI PENYUSUTAN PERALATAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 35 ], //id = 40
            // ['code_account_id' => '1.2.01.12', 'name' => 'AKUMULASI PENYUSUTAN INVENTARIS KANTOR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 35 ], //id = 41

            // ['code_account_id' => '2.0.00', 'name' => 'LIABILITAS', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => null ], //id = 42
            // ['code_account_id' => '2.1.00', 'name' => 'LIABILITAS JANGKA PENDEK', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 42 ], //id = 43
            // ['code_account_id' => '2.1.01', 'name' => 'UTANG USAHA', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 43 ], //id = 44
            // ['code_account_id' => '2.1.01.01', 'name' => 'UTANG USAHA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 44 ], //id = 45
            // ['code_account_id' => '2.1.01.02', 'name' => 'HUTANG PAK CHANDRA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 44 ], //id = 46
            // ['code_account_id' => '2.1.01.03', 'name' => 'HUTANG KENDARAAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 44 ], //id = 47
            // ['code_account_id' => '2.1.01.04', 'name' => 'HUTANG - PT ISB', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 44 ], //id = 48
            // ['code_account_id' => '2.1.01.05', 'name' => 'HUTANG LAINNYA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 44 ], //id = 49
            // ['code_account_id' => '2.1.01.06', 'name' => 'HUTANG - PT MMCT', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 44 ], //id = 50
            // ['code_account_id' => '2.1.01.07', 'name' => 'HUTANG BANGUNAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 44 ], //id = 51

            // ['code_account_id' => '2.1.02', 'name' => 'UTANG PAJAK', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 43], //id = 52
            // ['code_account_id' => '2.1.02.01', 'name' => 'PPN KELUARAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 52], //id = 53
            // ['code_account_id' => '2.1.02.02', 'name' => 'UTANG PPH 21', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 52], //id = 54
            // ['code_account_id' => '2.1.02.03', 'name' => 'UTANG PPH 22', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 52], //id = 55
            // ['code_account_id' => '2.1.02.04', 'name' => 'UTANG PPH 23', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 52], //id = 56
            // ['code_account_id' => '2.1.02.05', 'name' => 'UTANG PPH 25', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 52], //id = 57
            // ['code_account_id' => '2.1.02.06', 'name' => 'UTANG PPH 4(2)', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 52], //id = 58
            // ['code_account_id' => '2.1.02.07', 'name' => 'PPH KURANG BAYAR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 52], //id = 59
            // ['code_account_id' => '2.1.02.08', 'name' => 'UTANG PPH 15', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 52], //id =60

            // ['code_account_id' => '2.1.03', 'name' => 'BIAYA YANG MASIH HARUS DIBAYAR', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 43], //id =61
            // ['code_account_id' => '2.1.03.01', 'name' => 'UTANG GAJI DAN UPAH', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 61], //id =62
            // ['code_account_id' => '2.1.03.02', 'name' => 'UTANG BPJS KETENAGAKERJAAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 61], //id =63
            // ['code_account_id' => '2.1.03.03', 'name' => 'UTANG BPJS KESEHATAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 61], //id =64
            // ['code_account_id' => '2.1.03.04', 'name' => 'UTANG LISTRIK DAN AIR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 61], //id =65
            // ['code_account_id' => '2.1.03.05', 'name' => 'UTANG TELEPON DAN INTERNET', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 61], //id =66
            // ['code_account_id' => '2.1.03.06', 'name' => 'UTANG ASURANSI', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 61], //id =67
            // ['code_account_id' => '2.1.03.07', 'name' => 'UTANG SEWA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 61], //id =68
            // ['code_account_id' => '2.1.03.08', 'name' => 'BIAYA YANG MASIH HARUS DIBAYAR LAINNYA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 61],  //id =69

            // ['code_account_id' => '2.1.04', 'name' => 'UTANG LAIN-LAIN', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 43], //id = 70
            // ['code_account_id' => '2.1.04.01', 'name' => 'UTANG BANK JANGKA PENDEK', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 70], //id = 71
            // ['code_account_id' => '2.1.04.02', 'name' => 'UTANG DEVIDEN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 70], //id = 72
            // ['code_account_id' => '2.1.04.03', 'name' => 'PENDAPATAN DITERIMA DI MUKA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 70], //id = 73
            // ['code_account_id' => '2.1.04.04', 'name' => 'UTANG PEMBELIAN BELUM DITAGIH', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 70], //id = 74
            // ['code_account_id' => '2.1.04.05', 'name' => 'UTANG PADA PT. BMS', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 70], //id = 75

            // ['code_account_id' => '2.2.00', 'name' => 'LIABILITAS JANGKA PANJANG', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 42], //id = 76
            // ['code_account_id' => '2.2.01', 'name' => 'UTANG BANK JANGKA PANJANG', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 76], //id = 77
            // ['code_account_id' => '2.2.02', 'name' => 'UTANG JANGKA PANJANG LAINNYA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 76], //id = 78

            // ['code_account_id' => '3.0.00', 'name' => 'EQUITAS', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => null], //id = 79

            // ['code_account_id' => '3.1.00', 'name' => 'MODAL', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 79], //id = 80
            // ['code_account_id' => '3.1.01', 'name' => 'MODAL DISETOR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 80], //id = 81
            // ['code_account_id' => '3.1.02', 'name' => 'TAMBAHAN MODAL DISETOR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 80], //id = 82

            // ['code_account_id' => '3.2.00', 'name' => 'SALDO LABA', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 79], //id = 83
            // ['code_account_id' => '3.2.01', 'name' => 'SALDO LABA DITAHAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 83], //id = 84
            // ['code_account_id' => '3.2.02', 'name' => 'SALDO LABA TAHUN BERJALAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 83], //id = 85

            // ['code_account_id' => '3.3.00', 'name' => 'DEVIDEN', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 79], //id = 86
            // ['code_account_id' => '3.3.01', 'name' => 'PREV ATAU DEVIDEN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 86], //id = 87

            // ['code_account_id' => '4.0.00', 'name' => 'PENDAPATAN USAHA', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => null], //id = 88
            // ['code_account_id' => '4.1.00', 'name' => 'PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 88], //id = 89
            // ['code_account_id' => '4.2.00', 'name' => 'DISKON PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 88], //id = 90
            // ['code_account_id' => '4.3.00', 'name' => 'RETUR PENJUALAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 88], //id = 91
            // ['code_account_id' => '4.4.00', 'name' => 'BIAYA ANGKUT PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 88], //id = 92

            // ['code_account_id' => '5.0.00', 'name' => 'HARGA POKOK PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 93
            // ['code_account_id' => '5.1.00', 'name' => 'HARGA POKOK PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 93], //id = 94
            // ['code_account_id' => '5.1.01', 'name' => 'HARGA POKOK PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 95
            // ['code_account_id' => '5.2.00', 'name' => 'HARGA POKOK BARANG DAGANG', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 93], //id = 96
            // ['code_account_id' => '5.2.01', 'name' => 'PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 96], //id = 97
            // ['code_account_id' => '5.2.02', 'name' => 'DISKON PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 96], //id = 98
            // ['code_account_id' => '5.2.03', 'name' => 'RETUR PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 96], //id = 99
            // ['code_account_id' => '5.2.04', 'name' => 'BIAYA ANGKUT PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 96], //id = 100

            // ['code_account_id' => '6.0.00', 'name' => 'BEBAN USAHA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 101
            // ['code_account_id' => '6.1.00', 'name' => 'BEBAN PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 101], //id = 102
            // ['code_account_id' => '6.1.01', 'name' => 'BEBAN IKLAN DAN PROMOSI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 102], //id = 103
            // ['code_account_id' => '6.1.02', 'name' => 'BEBAN KOMISI PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 102], //id = 104
            // ['code_account_id' => '6.1.03', 'name' => 'BEBAN TRANSPORTASI MARKETING', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 102], //id = 105
            // ['code_account_id' => '6.1.04', 'name' => 'BEBAN ENTERTAINMENT', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 102], //id = 106
            // ['code_account_id' => '6.1.05', 'name' => 'BEBAN LEGALITAS DOKUMEN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 102], //id = 107
            // ['code_account_id' => '6.1.06', 'name' => 'BEBAN GST PENGIRIMAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 102], //id = 108
            // ['code_account_id' => '6.1.07', 'name' => 'BEBAN OPERASIONAL EKSPEDISI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 102], //id = 109
            // ['code_account_id' => '6.1.08', 'name' => 'BEBAN PENGIRIMAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 102], //id = 110
            // ['code_account_id' => '6.2.00', 'name' => 'BEBAN ADMINISTRASI DAN UMUM', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 101], //id = 111
            // ['code_account_id' => '6.2.01', 'name' => 'BEBAN GAJI DAN UPAH', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 112
            // ['code_account_id' => '6.2.02', 'name' => 'BEBAN PPH 21', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 113
            // ['code_account_id' => '6.2.03', 'name' => 'BEBAN BPJS KETENAGAKERJAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 114
            // ['code_account_id' => '6.2.04', 'name' => 'BEBAN BPJS KESEHATAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 115
            // ['code_account_id' => '6.2.05', 'name' => 'BEBAN TUNJANGAN HARI RAYA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 116
            // ['code_account_id' => '6.2.06', 'name' => 'BEBAN BONUS DAN INSENTIF', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 117
            // ['code_account_id' => '6.2.07', 'name' => 'BEBAN TUNJANGAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 118
            // ['code_account_id' => '6.2.08', 'name' => 'BEBAN LEMBUR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 119
            // ['code_account_id' => '6.2.09', 'name' => 'BEBAN PERIJINAN DAN LISENSI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 120
            // ['code_account_id' => '6.2.10', 'name' => 'BEBAN SEWA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 121
            // ['code_account_id' => '6.2.11', 'name' => 'BEBAN LISTRIK DAN AIR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 122
            // ['code_account_id' => '6.2.12', 'name' => 'BEBAN TELEPON DAN INTERNET', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 123
            // ['code_account_id' => '6.2.13', 'name' => 'BEBAN KEBERSIHAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 124
            // ['code_account_id' => '6.2.14', 'name' => 'BEBAN KEAMANAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 125
            // ['code_account_id' => '6.2.15', 'name' => 'BEBAN PERLENGKAPAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 126
            // ['code_account_id' => '6.2.16', 'name' => 'BEBAN ATK DAN FOTOKOPI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 127
            // ['code_account_id' => '6.2.17', 'name' => 'BEBAN PENGIRIMAN DOKUMEN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 128
            // ['code_account_id' => '6.2.18', 'name' => 'BEBAN PERBAIKAN DAN PEMELIHARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 129
            // ['code_account_id' => '6.2.19', 'name' => 'BEBAN TRANSPORTASI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 130
            // ['code_account_id' => '6.2.20', 'name' => 'BEBAN PERJALANAN DINAS', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 131
            // ['code_account_id' => '6.2.21', 'name' => 'BEBAN REKRUITMEN DAN PELATIHAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 132
            // ['code_account_id' => '6.2.22', 'name' => 'BEBAN ASURANSI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 133
            // ['code_account_id' => '6.2.23', 'name' => 'BEBAN SUMBANGAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 134
            // ['code_account_id' => '6.2.24', 'name' => 'BEBAN JASA PROFESIONAL', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 135
            // ['code_account_id' => '6.2.25', 'name' => 'BEBAN RUMAH TANGGA KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 136
            // ['code_account_id' => '6.2.26', 'name' => 'BEBAN PERCETAKKAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 137
            // ['code_account_id' => '6.2.27', 'name' => 'BEBAN KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 138
            // ['code_account_id' => '6.2.28', 'name' => 'BEBAN BBM DAN PARKIR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 139
            // ['code_account_id' => '6.2.29', 'name' => 'BEBAN KEPERLUAN KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 111], //id = 140
            // ['code_account_id' => '6.3.00', 'name' => 'BEBAN PENYUSUTAN DAN AMORTISASI', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 101], //id = 141
            // ['code_account_id' => '6.3.01', 'name' => 'BEBAN PENYUSUTAN GEDUNG', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 142
            // ['code_account_id' => '6.3.02', 'name' => 'BEBAN PENYUSUTAN KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 143
            // ['code_account_id' => '6.3.03', 'name' => 'BEBAN PENYUSUTAN MESIN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 144
            // ['code_account_id' => '6.3.04', 'name' => 'BEBAN PENYUSUTAN PERALATAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 145
            // ['code_account_id' => '6.3.05', 'name' => 'BEBAN PENYUSUTAN INVENTARIS KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 146
            // ['code_account_id' => '6.3.06', 'name' => 'BEBAN PENYUSUTAN SARANA DAN PRASARANA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 147
            // ['code_account_id' => '6.3.07', 'name' => 'BEBAN AMORTISASI GOODWILL', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 148
            // ['code_account_id' => '6.3.08', 'name' => 'BEBAN AMORTISASI FRANCHISE', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 149
            // ['code_account_id' => '6.3.09', 'name' => 'BEBAN AMORTISASI LISENSI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 141], //id = 150
            // ['code_account_id' => '6.3.10', 'name' => 'BEBAN AMORTISASI HAK CIPTA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 141], //id = 151

            // ['code_account_id' => '7.0.00', 'name' => 'PENDAPATAN DI LUAR USAHA', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => null], //id = 152
            // ['code_account_id' => '7.1.00', 'name' => 'PENDAPATAN BUNGA BANK DAN JASA GIRO', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 152], //id = 153
            // ['code_account_id' => '7.2.00', 'name' => 'LABA ATAS SELISIH KURS', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 152], //id = 154
            // ['code_account_id' => '7.3.00', 'name' => 'LABA PELEPASAN ASET', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 152], //id = 155
            // ['code_account_id' => '7.4.00', 'name' => 'PENDAPATAN DI LUAR USAHA LAINNYA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 152], //id = 156

            // ['code_account_id' => '8.0.00', 'name' => 'BEBAN DI LUAR USAHA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 157
            // ['code_account_id' => '8.1.00', 'name' => 'BEBAN BUNGA PINJAMAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 157], //id = 158
            // ['code_account_id' => '8.2.00', 'name' => 'BEBAN ADMINISTRASI DAN PAJAK GIRO', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 157], //id = 159
            // ['code_account_id' => '8.3.00', 'name' => 'BEBAN PROVISI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 157], //id = 160
            // ['code_account_id' => '8.4.00', 'name' => 'RUGI ATAS SELISIH KURS', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 157], //id = 161
            // ['code_account_id' => '8.5.00', 'name' => 'RUGI PELEPASAN ASET', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 157], //id = 162
            // ['code_account_id' => '8.6.00', 'name' => 'BEBAN SANKSI PAJAK', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 157], //id = 163
            // ['code_account_id' => '8.7.00', 'name' => 'BEBAN PAJAK PENGHASILAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 157], //id = 164
            // ['code_account_id' => '8.8.00', 'name' => 'BEBAN DI LUAR USAHA LAINNYA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 157], //id = 165

            // ['code_account_id' => '9.0.00', 'name' => 'BIAYA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 166

            // ['code_account_id' => '2.1.04.06', 'name' => 'POIN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 70], //id = 167
            // ['code_account_id' => '2.1.04.07', 'name' => 'POIN MARGIN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 70], //id = 168

            ['code_account_id' => '1.0.00', 'name' => 'ASET', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 1
            ['code_account_id' => '1.1.00', 'name' => 'ASET LANCAR', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 1 ],  //id = 2

            ['code_account_id' => '1.1.01', 'name' => 'KAS', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 3
            ['code_account_id' => '1.1.01.01', 'name' => 'KAS IDR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 3 ], //id = 4
            ['code_account_id' => '1.1.01.02', 'name' => 'KAS KAS', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 3 ], //id = 5
            ['code_account_id' => '1.1.01.03', 'name' => 'KAS SGD', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 3 ], //id = 6

            ['code_account_id' => '1.1.02', 'name' => 'BANK', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 7
            ['code_account_id' => '1.1.02.01', 'name' => 'BANK BCA IDR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7] , //id = 8
            ['code_account_id' => '1.1.02.02', 'name' => 'BANK BCA SGD', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 9
            ['code_account_id' => '1.1.02.03', 'name' => 'BANK MANDIRI IDR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 10
            ['code_account_id' => '1.1.02.04', 'name' => 'BANK BNI SGD', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 11
            ['code_account_id' => '1.1.02.05', 'name' => 'BANK NIAGA IDR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 12
            ['code_account_id' => '1.1.02.06', 'name' => 'BANK NIAGA SGD', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 13
            // ['code_account_id' => '1.1.02.07', 'name' => 'BANK BCA CHANDRA SUSANTO', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 7 ], //id = 

            ['code_account_id' => '1.1.03', 'name' => ' PIUTANG', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 14
            ['code_account_id' => '1.1.03.01 ', 'name' => 'PIUTANG USAHA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 14], //id = 15
            ['code_account_id' => '1.1.03.02', 'name' => 'PIUTANG LAIN-LAIN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 14], //id = 16

            ['code_account_id' => '1.1.05', 'name' => 'PERLENGKAPAN', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 17
            ['code_account_id' => '1.1.05.01', 'name' => 'PERLENGKAPAN KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 17], //id = 18

            ['code_account_id' => '1.1.06', 'name' => 'PAJAK DIBAYAR DIMUKA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 19
            ['code_account_id' => '1.1.06.01', 'name' => 'PPN MASUKAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 19], //id = 20
            ['code_account_id' => '1.1.06.02', 'name' => ' PPH 21 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 19], //id = 21
            ['code_account_id' => '1.1.06.03', 'name' => 'PPH 22 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 19], //id = 22
            ['code_account_id' => '1.1.06.04', 'name' => 'PPH 23 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 19], //id = 23
            ['code_account_id' => '1.1.06.05', 'name' => 'PPH 24 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 19], //id = 24
            ['code_account_id' => '1.1.06.06', 'name' => 'PPH 25 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 19], //id = 25
            ['code_account_id' => '1.1.06.07', 'name' => 'PPH LEBIH BAYAR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 19], //id = 26
            ['code_account_id' => '1.1.06.08', 'name' => 'PPH 15 DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 19], //id = 27

            ['code_account_id' => '1.1.07', 'name' => 'BIAYA DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 2 ], //id = 28
            ['code_account_id' => '1.1.07.01', 'name' => 'UANG MUKA PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 28], //id = 29
            ['code_account_id' => '1.1.07.02', 'name' => 'SEWA DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 28], //id = 30
            ['code_account_id' => '1.1.07.03', 'name' => 'ASURANSI DIBAYAR DI MUKA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 28], //id = 31
            ['code_account_id' => '1.1.07.04', 'name' => 'UANG MUKA PEMBELIAN KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 28], //id = 32

            ['code_account_id' => '1.2.00', 'name' => 'ASET TETAP', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 1 ], //id = 33
            ['code_account_id' => '1.2.01', 'name' => 'ASET TETAP BERWUJUD', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 33], //id = 34
            ['code_account_id' => '1.2.01.01', 'name' => 'KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 34], //id = 35
            ['code_account_id' => '1.2.01.02', 'name' => 'PERALATAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 34], //id = 36
            ['code_account_id' => '1.2.01.03', 'name' => 'INVENTARIS KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 34], //id = 37
            ['code_account_id' => '1.2.01.04', 'name' => 'AKUMULASI PENYUSUTAN KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 34], //id = 38
            ['code_account_id' => '1.2.01.05', 'name' => 'AKUMULASI PENYUSUTAN PERALATAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 34], //id = 39
            ['code_account_id' => '1.2.01.06', 'name' => 'AKUMULASI PENYUSUTAN INVENTARIS KANTOR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 34], //id = 40

            ['code_account_id' => '2.0.00', 'name' => 'LIABILITAS', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => null ], //id = 41
            ['code_account_id' => '2.1.00', 'name' => 'LIABILITAS JANGKA PENDEK', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 41 ], //id = 42
            ['code_account_id' => '2.1.01', 'name' => 'UTANG USAHA', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 42], //id = 43
            ['code_account_id' => '2.1.01.01', 'name' => 'UTANG USAHA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 43], //id = 44
            ['code_account_id' => '2.1.01.02', 'name' => 'HUTANG KENDARAAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 43], //id = 45
            ['code_account_id' => '2.1.01.03', 'name' => 'HUTANG LAINNYA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 43], //id = 46
            // ['code_account_id' => '2.1.01.04', 'name' => 'HUTANG - PT MMCT', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 43], //id = 
            ['code_account_id' => '2.1.01.04', 'name' => 'HUTANG BANGUNAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 43], //id = 47

            ['code_account_id' => '2.1.02', 'name' => 'UTANG PAJAK', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 42], //id = 48
            ['code_account_id' => '2.1.02.01', 'name' => 'PPN KELUARAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 48], //id = 49
            ['code_account_id' => '2.1.02.02', 'name' => 'UTANG PPH 21', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 48], //id = 50
            ['code_account_id' => '2.1.02.03', 'name' => 'UTANG PPH 22', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 48], //id = 51
            ['code_account_id' => '2.1.02.04', 'name' => 'UTANG PPH 23', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 48], //id = 52
            ['code_account_id' => '2.1.02.05', 'name' => 'UTANG PPH 25', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 48], //id = 53
            ['code_account_id' => '2.1.02.06', 'name' => 'UTANG PPH 4(2)', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 48], //id = 54
            ['code_account_id' => '2.1.02.07', 'name' => 'PPH KURANG BAYAR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 48], //id = 55
            ['code_account_id' => '2.1.02.08', 'name' => 'UTANG PPH 15', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 48], //id =56

            ['code_account_id' => '2.1.03', 'name' => 'BIAYA YANG MASIH HARUS DIBAYAR', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 42], //id =57
            ['code_account_id' => '2.1.03.01', 'name' => 'UTANG GAJI DAN UPAH', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 57], //id =58
            ['code_account_id' => '2.1.03.02', 'name' => 'UTANG BPJS KETENAGAKERJAAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 57], //id =59
            ['code_account_id' => '2.1.03.03', 'name' => 'UTANG BPJS KESEHATAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 57], //id =60
            ['code_account_id' => '2.1.03.04', 'name' => 'UTANG LISTRIK DAN AIR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 57], //id =61
            ['code_account_id' => '2.1.03.05', 'name' => 'UTANG TELEPON DAN INTERNET', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 57], //id =62
            ['code_account_id' => '2.1.03.06', 'name' => 'UTANG ASURANSI', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 57], //id =63
            ['code_account_id' => '2.1.03.07', 'name' => 'UTANG SEWA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 57], //id =64
            ['code_account_id' => '2.1.03.08', 'name' => 'BIAYA YANG MASIH HARUS DIBAYAR LAINNYA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 57],  //id =65

            ['code_account_id' => '2.1.04', 'name' => 'UTANG LAIN-LAIN', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 42], //id = 66
            ['code_account_id' => '2.1.04.01', 'name' => 'UTANG BANK JANGKA PENDEK', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 66], //id = 67
            ['code_account_id' => '2.1.04.02', 'name' => 'UTANG DEVIDEN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 66], //id = 68
            ['code_account_id' => '2.1.04.03', 'name' => 'PENDAPATAN DITERIMA DI MUKA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 66], //id = 69
            ['code_account_id' => '2.1.04.04', 'name' => 'UTANG PEMBELIAN BELUM DITAGIH', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 66], //id = 70
            // ['code_account_id' => '2.1.04.05', 'name' => 'UTANG PADA PT. BMS', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 66], //id = 

            ['code_account_id' => '2.2.00', 'name' => 'LIABILITAS JANGKA PANJANG', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 41], //id = 71
            ['code_account_id' => '2.2.01', 'name' => 'UTANG BANK JANGKA PANJANG', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 71], //id = 72
            ['code_account_id' => '2.2.02', 'name' => 'UTANG JANGKA PANJANG LAINNYA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 71], //id = 73

            ['code_account_id' => '3.0.00', 'name' => 'EQUITAS', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => null], //id = 74

            ['code_account_id' => '3.1.00', 'name' => 'MODAL', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 74], //id = 75
            ['code_account_id' => '3.1.01', 'name' => 'MODAL DISETOR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 75], //id = 76
            ['code_account_id' => '3.1.02', 'name' => 'TAMBAHAN MODAL DISETOR', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 75], //id = 77

            ['code_account_id' => '3.2.00', 'name' => 'SALDO LABA', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 74], //id = 78
            ['code_account_id' => '3.2.01', 'name' => 'SALDO LABA DITAHAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 78], //id = 79
            ['code_account_id' => '3.2.02', 'name' => 'SALDO LABA TAHUN BERJALAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 78], //id = 80

            ['code_account_id' => '3.3.00', 'name' => 'DEVIDEN', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => 74], //id = 81
            ['code_account_id' => '3.3.01', 'name' => 'PREV ATAU DEVIDEN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 81], //id = 82

            ['code_account_id' => '4.0.00', 'name' => 'PENDAPATAN USAHA', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => null], //id = 83
            ['code_account_id' => '4.1.00', 'name' => 'PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 83], //id = 84
            ['code_account_id' => '4.2.00', 'name' => 'DISKON PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 83], //id = 85
            ['code_account_id' => '4.3.00', 'name' => 'RETUR PENJUALAN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 83], //id = 86
            ['code_account_id' => '4.4.00', 'name' => 'BIAYA ANGKUT PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 83], //id = 87

            ['code_account_id' => '5.0.00', 'name' => 'HARGA POKOK PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 88
            ['code_account_id' => '5.0.01', 'name' => 'PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 88], //id = 89
            ['code_account_id' => '5.0.02', 'name' => 'DISKON PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 88], //id = 90
            ['code_account_id' => '5.0.03', 'name' => 'RETUR PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 88], //id = 91
            ['code_account_id' => '5.0.04', 'name' => 'BIAYA ANGKUT PEMBELIAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 88], //id = 92

            ['code_account_id' => '6.0.00', 'name' => 'BEBAN USAHA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 93
            ['code_account_id' => '6.1.00', 'name' => 'BEBAN PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 93], //id = 94
            ['code_account_id' => '6.1.01', 'name' => 'BEBAN IKLAN DAN PROMOSI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 95
            ['code_account_id' => '6.1.02', 'name' => 'BEBAN KOMISI PENJUALAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 96
            ['code_account_id' => '6.1.03', 'name' => 'BEBAN TRANSPORTASI MARKETING', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 97
            ['code_account_id' => '6.1.04', 'name' => 'BEBAN ENTERTAINMENT', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 98
            ['code_account_id' => '6.1.05', 'name' => 'BEBAN LEGALITAS DOKUMEN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 99
            ['code_account_id' => '6.1.06', 'name' => 'BEBAN GST PENGIRIMAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 100
            ['code_account_id' => '6.1.07', 'name' => 'BEBAN OPERASIONAL EKSPEDISI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 101
            ['code_account_id' => '6.1.08', 'name' => 'BEBAN PENGIRIMAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 94], //id = 102
            ['code_account_id' => '6.2.00', 'name' => 'BEBAN ADMINISTRASI DAN UMUM', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 93], //id = 103
            ['code_account_id' => '6.2.01', 'name' => 'BEBAN GAJI DAN UPAH', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 104
            ['code_account_id' => '6.2.02', 'name' => 'BEBAN PPH 21', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 105
            ['code_account_id' => '6.2.03', 'name' => 'BEBAN BPJS KETENAGAKERJAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 106
            ['code_account_id' => '6.2.04', 'name' => 'BEBAN BPJS KESEHATAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 107
            ['code_account_id' => '6.2.05', 'name' => 'BEBAN TUNJANGAN HARI RAYA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 108
            ['code_account_id' => '6.2.06', 'name' => 'BEBAN BONUS DAN INSENTIF', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 109
            ['code_account_id' => '6.2.07', 'name' => 'BEBAN TUNJANGAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 110
            ['code_account_id' => '6.2.08', 'name' => 'BEBAN LEMBUR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 111
            ['code_account_id' => '6.2.09', 'name' => 'BEBAN PERIJINAN DAN LISENSI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 112
            ['code_account_id' => '6.2.10', 'name' => 'BEBAN SEWA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 113
            ['code_account_id' => '6.2.11', 'name' => 'BEBAN LISTRIK DAN AIR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 114
            ['code_account_id' => '6.2.12', 'name' => 'BEBAN TELEPON DAN INTERNET', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 115
            ['code_account_id' => '6.2.13', 'name' => 'BEBAN KEBERSIHAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 116
            ['code_account_id' => '6.2.14', 'name' => 'BEBAN KEAMANAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 117
            ['code_account_id' => '6.2.15', 'name' => 'BEBAN PERLENGKAPAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 118
            ['code_account_id' => '6.2.16', 'name' => 'BEBAN ATK DAN FOTOKOPI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 119
            ['code_account_id' => '6.2.17', 'name' => 'BEBAN PENGIRIMAN DOKUMEN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 120
            ['code_account_id' => '6.2.18', 'name' => 'BEBAN PERBAIKAN DAN PEMELIHARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 121
            ['code_account_id' => '6.2.19', 'name' => 'BEBAN TRANSPORTASI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 122
            ['code_account_id' => '6.2.20', 'name' => 'BEBAN PERJALANAN DINAS', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 123
            ['code_account_id' => '6.2.21', 'name' => 'BEBAN REKRUITMEN DAN PELATIHAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 124
            ['code_account_id' => '6.2.22', 'name' => 'BEBAN ASURANSI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 125
            ['code_account_id' => '6.2.23', 'name' => 'BEBAN SUMBANGAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 126
            ['code_account_id' => '6.2.24', 'name' => 'BEBAN JASA PROFESIONAL', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 127
            ['code_account_id' => '6.2.25', 'name' => 'BEBAN RUMAH TANGGA KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 128
            ['code_account_id' => '6.2.26', 'name' => 'BEBAN PERCETAKKAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 129
            ['code_account_id' => '6.2.27', 'name' => 'BEBAN KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 130
            ['code_account_id' => '6.2.28', 'name' => 'BEBAN BBM DAN PARKIR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 131
            ['code_account_id' => '6.2.29', 'name' => 'BEBAN KEPERLUAN KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 103], //id = 132
            ['code_account_id' => '6.3.00', 'name' => 'BEBAN PENYUSUTAN DAN AMORTISASI', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => 93], //id = 133
            ['code_account_id' => '6.3.01', 'name' => 'BEBAN PENYUSUTAN GEDUNG', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 134
            ['code_account_id' => '6.3.02', 'name' => 'BEBAN PENYUSUTAN KENDARAAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 135
            ['code_account_id' => '6.3.03', 'name' => 'BEBAN PENYUSUTAN MESIN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 136
            ['code_account_id' => '6.3.04', 'name' => 'BEBAN PENYUSUTAN PERALATAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 137
            ['code_account_id' => '6.3.05', 'name' => 'BEBAN PENYUSUTAN INVENTARIS KANTOR', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 138
            ['code_account_id' => '6.3.06', 'name' => 'BEBAN PENYUSUTAN SARANA DAN PRASARANA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 139
            ['code_account_id' => '6.3.07', 'name' => 'BEBAN AMORTISASI GOODWILL', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 140
            ['code_account_id' => '6.3.08', 'name' => 'BEBAN AMORTISASI FRANCHISE', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 141
            ['code_account_id' => '6.3.09', 'name' => 'BEBAN AMORTISASI LISENSI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 133], //id = 142
            ['code_account_id' => '6.3.10', 'name' => 'BEBAN AMORTISASI HAK CIPTA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 133], //id = 143

            ['code_account_id' => '7.0.00', 'name' => 'PENDAPATAN DI LUAR USAHA', 'default_posisi' => 'Credit', 'set_as_group' => true, 'parent_id' => null], //id = 144
            ['code_account_id' => '7.1.00', 'name' => 'PENDAPATAN BUNGA BANK DAN JASA GIRO', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 144], //id = 145
            ['code_account_id' => '7.2.00', 'name' => 'LABA ATAS SELISIH KURS', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 144], //id = 146
            ['code_account_id' => '7.3.00', 'name' => 'LABA PELEPASAN ASET', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 144], //id = 147
            ['code_account_id' => '7.4.00', 'name' => 'PENDAPATAN DI LUAR USAHA LAINNYA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 144], //id = 148

            ['code_account_id' => '8.0.00', 'name' => 'BEBAN DI LUAR USAHA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 149
            ['code_account_id' => '8.1.00', 'name' => 'BEBAN BUNGA PINJAMAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 149], //id = 150
            ['code_account_id' => '8.2.00', 'name' => 'BEBAN ADMINISTRASI DAN PAJAK GIRO', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 149], //id = 151
            ['code_account_id' => '8.3.00', 'name' => 'BEBAN PROVISI', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 149], //id = 152
            ['code_account_id' => '8.4.00', 'name' => 'RUGI ATAS SELISIH KURS', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 149], //id = 153
            ['code_account_id' => '8.5.00', 'name' => 'RUGI PELEPASAN ASET', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 149], //id = 154
            ['code_account_id' => '8.6.00', 'name' => 'BEBAN SANKSI PAJAK', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 149], //id = 155
            ['code_account_id' => '8.7.00', 'name' => 'BEBAN PAJAK PENGHASILAN', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 149], //id = 156
            ['code_account_id' => '8.8.00', 'name' => 'BEBAN DI LUAR USAHA LAINNYA', 'default_posisi' => 'Debit', 'set_as_group' => false, 'parent_id' => 149], //id = 157

            ['code_account_id' => '9.0.00', 'name' => 'BIAYA', 'default_posisi' => 'Debit', 'set_as_group' => true, 'parent_id' => null], //id = 158

            ['code_account_id' => '2.1.04.05', 'name' => 'KUOTA', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 66], //id = 159
            ['code_account_id' => '2.1.04.06', 'name' => 'KUOTA MARGIN', 'default_posisi' => 'Credit', 'set_as_group' => false, 'parent_id' => 66], //id = 160




        ];








        foreach ($accounts as $account) {
          COA::updateOrCreate(
                ['code_account_id' => $account['code_account_id']],
                $account
            );
        }
    }

}