# AGENTS.md — Project gatratrus

Aturan kerja untuk AI (Codex)

---

## 1. Tujuan

Repository ini adalah project production.

AI hanya bertugas membantu pengembangan dengan perubahan seminimal mungkin,
tanpa merusak stabilitas sistem.

AI adalah asisten developer, bukan arsitek sistem.

Stabilitas lebih penting daripada kreativitas.

---

## 2. Environment Project

Framework:

- Laravel 12.50.0

Versi PHP:

- Production (VPS): PHP 8.5.3
- Local Development: PHP 8.3.26

Aturan kompatibilitas:

- Semua kode HARUS kompatibel minimal dengan PHP 8.3
- Jangan gunakan fitur khusus PHP 8.4 atau 8.5
- Ikuti aturan composer: php ^8.2
- Perubahan harus bisa berjalan baik di local dan production

---

## 3. Struktur Branch (WAJIB DIIKUTI)

Branch yang digunakan:

- prod → branch production (VPS pull dari sini)
- dev → branch development (AI bekerja di sini)

Aturan:

- DILARANG commit langsung ke prod
- DILARANG membuat PR langsung ke prod
- Semua pekerjaan harus berasal dari dev
- Buat branch baru dari dev (misal: feature/nama-fitur)
- Flow merge:
  feature/\* → dev → prod (manual approval)

VPS melakukan:
git pull origin prod

---

## 4. Batasan Perubahan

AI BOLEH:

- Mengubah file di:
  app/
  routes/
  resources/
  database/
  tests/

- Menambahkan migration jika memang dibutuhkan
- Menambahkan test

AI TIDAK BOLEH:

- Upgrade/downgrade Laravel
- Mengubah versi PHP
- Mengubah arsitektur besar
- Mengganti pola project secara menyeluruh
- Menambah package berat tanpa alasan jelas

Jika butuh package baru:

- Jelaskan alasan
- Jelaskan dampaknya
- Jelaskan alternatifnya

---

## 5. Standar Coding

- Ikuti struktur dan pola yang sudah ada
- Controller harus tetap tipis
- Logika bisnis mengikuti pola yang sudah digunakan di repo
- Jangan duplikasi logic
- Jangan buat pola baru tanpa alasan kuat

Validasi:

- Gunakan FormRequest jika memang sudah digunakan di modul serupa

Keamanan:

- Jangan melemahkan authentication
- Jangan menghapus middleware
- Jangan mengubah policy tanpa instruksi

---

## 6. Aturan Database

- Jangan ubah struktur tabel tanpa migration
- Migration harus bisa rollback (down() benar)
- Jangan hardcode data penting di runtime
- Gunakan seeder jika perlu data default

---

## 7. Quality Check (WAJIB SEBELUM SELESAI)

Sebelum task dianggap selesai:

1. Jalankan formatter:
   ./vendor/bin/pint

2. Jalankan test:
   php artisan test

3. Pastikan:
    - Tidak ada dd()
    - Tidak ada dump()
    - Tidak ada var_dump()
    - Tidak ada debug code tersisa
    - Tidak ada import tidak terpakai

---

## 8. Definition of Done

Task selesai jika:

- Tidak error
- Test lulus
- Format rapi
- Tidak ada breaking change tidak sengaja

Output harus menyertakan:

- Ringkasan perubahan
- Cara test
- Asumsi yang digunakan (jika ada)

---

## 9. Prinsip Utama

- Perubahan kecil lebih baik daripada perubahan besar
- Jangan sentuh file yang tidak relevan
- Jangan mengubah konfigurasi production
- Jika ragu, pilih pendekatan paling aman
