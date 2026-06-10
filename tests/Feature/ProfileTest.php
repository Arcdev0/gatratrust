<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
    }

    public function test_client_can_open_profile_page(): void
    {
        $client = $this->createClient();

        $response = $this->actingAs($client)->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('Informasi Akun');
        $response->assertSee('Ganti Password');
        $response->assertDontSee('Proposal Anggaran Kerja');
    }

    public function test_client_can_update_profile_and_password(): void
    {
        $client = $this->createClient();

        $response = $this->actingAs($client)->post(route('profile.update'), [
            'company' => 'Client Baru',
            'email' => 'client.baru@example.com',
            'current_password' => 'password',
            'new_password' => 'password-baru',
            'new_password_confirmation' => 'password-baru',
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success', 'Profile berhasil diperbarui.');

        $client->refresh();

        $this->assertSame('Client Baru', $client->company);
        $this->assertSame('client.baru@example.com', $client->email);
        $this->assertTrue(Hash::check('password-baru', $client->password));
    }

    public function test_client_cannot_update_password_with_incorrect_current_password(): void
    {
        $client = $this->createClient();

        $response = $this->actingAs($client)->from(route('profile.index'))->post(route('profile.update'), [
            'company' => $client->company,
            'email' => $client->email,
            'current_password' => 'password-salah',
            'new_password' => 'password-baru',
            'new_password_confirmation' => 'password-baru',
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password', $client->fresh()->password));
    }

    private function createClient(): User
    {
        $role = Role::create(['name' => 'Client']);

        return User::factory()->create([
            'name' => 'client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'company' => 'Client Corp',
        ]);
    }
}
