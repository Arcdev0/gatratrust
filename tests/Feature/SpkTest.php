<?php

namespace Tests\Feature;

use App\Models\Kerjaan;
use App\Models\ProjectTbl;
use App\Models\Role;
use App\Models\Spk;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SpkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createMinimalSchema();
    }

    public function test_index_route_returns_200(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->get('/spk');

        $response->assertOk();
    }

    public function test_export_pdf_route_returns_pdf_response(): void
    {
        $user = $this->createAuthenticatedUser();

        $project = ProjectTbl::factory()->create([
            'client_id' => $user->id,
            'created_by' => $user->id,
            'kerjaan_id' => Kerjaan::factory()->create()->id,
        ]);

        $spk = Spk::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($user)->get(route('spk.exportPdf', $spk));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    private function createAuthenticatedUser(): User
    {
        $role = Role::query()->create(['name' => 'Admin']);

        return User::query()->create([
            'name' => 'user_'.uniqid(),
            'email' => uniqid('user', true).'@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
        ]);
    }

    private function createMinimalSchema(): void
    {
        Schema::dropAllTables();

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email');
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('company')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('kerjaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kerjaan')->unique();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('nama_project');
            $table->string('no_project')->unique();
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('kerjaan_id')->constrained('kerjaans');
            $table->text('deskripsi')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->date('tanggal');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->json('data_proyek')->nullable();
            $table->timestamps();
        });
    }
}
