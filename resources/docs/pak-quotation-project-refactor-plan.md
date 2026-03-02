# Refactor Plan: PAK / Quotation / Project (Single Source of Truth)

## 1) Kolom yang tetap di `paks` (master dokumen komersial)
- `id`
- `pak_number`, `pak_name`, `date`, `location`
- `pak_value` (sebagai amount dasar quotation)
- `customer_name`, `customer_address`, `attention`, `your_reference`, `terms_text`
- `customer_user_id` (opsional, relasi ke `users.id` role Client)
- `total_pak_cost`, `pph_23`, `ppn`, `estimated_profit`, `total_cost_percentage`

## 2) Kolom di `quotation` yang ditinggalkan/legacy
Kolom berikut dipertahankan sementara untuk backward compatibility, tapi sumber utama diambil dari PAK:
- `customer_name`, `customer_address`, `attention`, `your_reference`, `terms`
- `total_amount`, `discount`, `sub_total`

Target jangka panjang:
- Tetap simpan sebagai snapshot historis dokumen final.
- Input user untuk kolom di atas di form quotation **dihilangkan** (readonly dari PAK).

## 3) Kolom di `projects`
- Tetap: `client_id` untuk customer operasional project.
- Tetap: `pak_id` sebagai referensi komersial.
- Tidak duplikasi customer dokumen di `projects`.

## 4) Relasi model
- `Pak hasMany Quotation`
- `Pak belongsTo User as customerUser` (opsional)
- `Quotation belongsTo Pak`
- `ProjectTbl belongsTo User as client`
- `ProjectTbl belongsTo Pak`

## 5) Strategi backfill data lama (manual, tanpa insert otomatis)
1. Cari `quotation` yang `pak_id` masih `NULL`.
2. Buat/mapping PAK secara manual oleh tim bisnis (tidak dibuat otomatis oleh migration).
3. Update semua `quotation.pak_id` yang kosong ke PAK valid.
4. Jalankan migration enforce `NOT NULL` setelah data bersih.

## 6) Catatan implementasi
- Form create/edit quotation hanya memilih PAK dan menampilkan customer + amount readonly.
- Total quotation saat create diambil dari `pak.pak_value`.
- Fallback PDF untuk data lama tetap aman via snapshot quotation jika dibutuhkan.
