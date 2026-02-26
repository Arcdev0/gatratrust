<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Spk;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SpkFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('password');
                $table->timestamp('email_verified_at')->nullable();
                $table->unsignedBigInteger('role_id')->nullable();
                $table->string('company')->nullable();
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('spks')) {
            Schema::create('spks', function (Blueprint $table) {
                $table->id();
                $table->string('nomor')->unique();
                $table->date('tanggal');
                $table->string('pegawai_nama');
                $table->string('pegawai_jabatan');
                $table->string('pegawai_divisi')->nullable();
                $table->string('pegawai_nik_id')->nullable();
                $table->string('tujuan_dinas');
                $table->string('lokasi_perusahaan_tujuan')->nullable();
                $table->text('alamat_lokasi')->nullable();
                $table->text('maksud_ruang_lingkup')->nullable();
                $table->date('tanggal_berangkat');
                $table->date('tanggal_kembali');
                $table->unsignedSmallInteger('lama_perjalanan');
                $table->string('sumber_biaya')->nullable();
                $table->string('moda_transportasi');
                $table->string('sumber_biaya_opsi');
                $table->string('ditugaskan_oleh_nama');
                $table->string('ditugaskan_oleh_jabatan');
                $table->timestamps();
            });
        }
    }

    private function loginUser(): User
    {
        $role = Role::create(['name' => 'Admin']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'admin-test',
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function test_spk_index_route_is_accessible(): void
    {
        $this->loginUser();

        $response = $this->get(route('spk.index'));

        $response->assertStatus(200);
        $response->assertSee('SPK');
    }

    public function test_spk_export_pdf_endpoint_returns_pdf_response(): void
    {
        $this->loginUser();

        $spk = Spk::create([
            'nomor' => 'SPK-001',
            'tanggal' => '2026-02-23',
            'pegawai_nama' => 'Budi Santoso',
            'pegawai_jabatan' => 'Engineer',
            'pegawai_divisi' => 'Teknik',
            'pegawai_nik_id' => 'EMP001',
            'tujuan_dinas' => 'Audit Lapangan',
            'lokasi_perusahaan_tujuan' => 'PT Contoh',
            'alamat_lokasi' => 'Batam Centre',
            'maksud_ruang_lingkup' => 'Pemeriksaan berkala',
            'tanggal_berangkat' => '2026-02-24',
            'tanggal_kembali' => '2026-02-25',
            'lama_perjalanan' => 2,
            'sumber_biaya' => 'Operasional',
            'moda_transportasi' => 'darat',
            'sumber_biaya_opsi' => 'perusahaan',
            'ditugaskan_oleh_nama' => 'Direktur Utama',
            'ditugaskan_oleh_jabatan' => 'Direktur',
        ]);

        $response = $this->get(route('spk.exportPdf', $spk));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }
}
