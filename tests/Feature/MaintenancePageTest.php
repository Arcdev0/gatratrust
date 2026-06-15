<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MaintenancePageTest extends TestCase
{
    public function test_maintenance_response_uses_gatra_trust_template(): void
    {
        config(['app.debug' => false]);

        Route::get('/maintenance-page-test', fn () => abort(503));

        $response = $this->get('/maintenance-page-test');

        $response->assertServiceUnavailable();
        $response->assertSee('Gatra Trust sedang meningkatkan sistem');
        $response->assertSee('Data Anda tetap aman selama proses pemeliharaan berlangsung.');
    }
}
