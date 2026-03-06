<?php

namespace Tests\Feature;

use App\Models\Pak;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationPakIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_create_quotation_without_pak_id(): void
    {
        $user = $this->createAuthenticatedUser();
        Status::query()->create(['name' => 'pending']);

        $payload = [
            'quo_no' => 'Q.001/GPT/02-26',
            'date' => now()->toDateString(),
            'items' => [
                [
                    'description' => 'Test Item',
                    'qty' => 1,
                    'unit_price' => 1000,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('quotations.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['pak_id']);
    }

    public function test_quotation_endpoint_returns_customer_from_pak(): void
    {
        $user = $this->createAuthenticatedUser();

        $pak = Pak::query()->create([
            'pak_name' => 'PAK Test',
            'pak_number' => '001/GPT-PAK/02-2026',
            'pak_value' => 2500000,
            'location' => 'dalam_kota',
            'date' => now()->toDateString(),
            'customer_name' => 'PT Client Test',
            'customer_address' => 'Jl. Test 123',
            'attention' => 'Budi',
            'your_reference' => 'REF-01',
            'terms_text' => 'Net 30',
        ]);

        $response = $this->actingAs($user)->get(route('quotations.pakDetail', $pak->id));

        $response->assertOk();
        $response->assertJsonPath('data.customer_name', 'PT Client Test');
        $response->assertJsonPath('data.pak_value', 2500000);
    }

    private function createAuthenticatedUser(): User
    {
        $role = Role::query()->firstOrCreate(['name' => 'Admin']);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
