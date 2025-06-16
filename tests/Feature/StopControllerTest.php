<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stop;

class StopControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_nearby_stops_returns_results()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Stop::factory()->count(5)->create();

        $response = $this->getJson('/api/stops?lat=46.2044&lon=6.1432');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_get_nearby_stops_requires_lat_and_lon()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/stops');
        $response->assertStatus(400);
        $response->assertJsonFragment(['error' => 'Latitude et longitude sont requises']);
    }

    public function test_get_nearby_stops_ignores_inactive()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Stop::factory()->create(['sto_actif' => 'N']);
        Stop::factory()->create(['sto_actif' => 'Y']);

        $response = $this->getJson('/api/stops?lat=46.2&lon=6.1');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Y', $data[0]['sto_actif']);
    }

    public function test_get_all_stops_returns_only_active()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Stop::factory()->create(['sto_actif' => 'Y']);
        Stop::factory()->create(['sto_actif' => 'N']);

        $response = $this->getJson('/api/stops/all');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Y', $data[0]['sto_actif']);
    }

    public function test_get_stops_by_name_returns_matching_results()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Stop::factory()->create(['sto_name' => 'Gare Cornavin']);
        Stop::factory()->create(['sto_name' => 'Plainpalais']);

        $response = $this->getJson('/api/stops/name?q=Gare');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Gare', $data[0]['sto_name']);
    }

    public function test_get_stops_by_name_requires_query_param()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/stops/name');
        $response->assertStatus(400);
        $response->assertJsonFragment(['error' => 'Le paramètre de recherche est requis.']);
    }

    public function test_nearby_stops_limit_is_respected()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Stop::factory()->count(10)->create(['sto_actif' => 'Y']);

        $response = $this->getJson('/api/stops?lat=46.2&lon=6.1');
        $response->assertStatus(200);
        $this->assertLessThanOrEqual(5, count($response->json('data')));
    }

    public function test_get_routes_for_stop()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $stop = Stop::factory()->create();

        $response = $this->getJson("/api/stops/{$stop->sto_id}/routes");
        $response->assertStatus(200)->assertJsonStructure(['stop', 'routes']);
    }

}
