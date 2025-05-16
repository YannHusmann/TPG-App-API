<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Stop;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_report()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reports', [
            'rep_sto_id' => $stop->sto_id,
            'rep_message' => 'Signalement test',
            'rep_type' => 'graffiti',
            'latitude' => 46.2044,
            'longitude' => 6.1432,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data']);
    }

    public function test_user_can_get_own_reports()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_message' => 'Problème test',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reports');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    public function test_user_can_update_own_report_if_not_treated()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        $report = Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_message' => 'Ancien message',
            'rep_type' => 'graffiti',
            'rep_status' => 'envoyé',
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/reports/{$report->rep_id}", [
            'rep_message' => 'Message modifié',
            'rep_type' => 'vitre cassée',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['rep_message' => 'Message modifié']);
    }

    public function test_user_cannot_update_report_if_treated()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        $report = Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_message' => 'Ancien message',
            'rep_status' => 'traité',
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/reports/{$report->rep_id}", [
            'rep_message' => 'Tentative de modification',
            'rep_type' => 'graffiti',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_report_if_not_treated()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        $report = Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_status' => 'envoyé',
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/reports/{$report->rep_id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Signalement supprimé avec succès']);
    }

    public function test_user_cannot_delete_report_if_treated()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        $report = Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_status' => 'traité',
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/reports/{$report->rep_id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_see_all_reports()
    {
        // Crée un utilisateur admin explicitement
        $admin = User::factory()->create([
            'use_role' => 'admin',
            'use_email' => 'admin@example.com',
            'use_username' => 'adminuser',
        ]);

        // Dump pour vérifier que le rôle est bien 'admin'
        $this->assertEquals('admin', $admin->use_role);

        // Crée un arrêt et quelques rapports
        $stop = Stop::factory()->create();
        Report::factory()->count(3)->create([
            'rep_sto_id' => $stop->sto_id,
        ]);

        // Authentifie-toi en tant qu’admin et appelle la route
        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/reports/all');

        // Assertions
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data']);
        $this->assertGreaterThanOrEqual(3, count($response->json('data.data') ?? [])); // pagination
    }


    public function test_non_admin_cannot_see_all_reports()
    {
        $user = User::factory()->create(['use_role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reports/all');

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_other_users_report()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $stop = Stop::factory()->create();

        $report = Report::factory()->create([
            'rep_use_id' => $otherUser->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_status' => 'envoyé',
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/reports/{$report->rep_id}", [
            'rep_message' => 'Tentative de modification',
            'rep_type' => 'graffiti',
        ]);

        $response->assertStatus(404);
    }

    public function test_user_cannot_delete_other_users_report()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $stop = Stop::factory()->create();

        $report = Report::factory()->create([
            'rep_use_id' => $otherUser->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_status' => 'envoyé',
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/reports/{$report->rep_id}");

        $response->assertStatus(404);
    }

    public function test_report_creation_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/reports', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rep_message', 'rep_type']);
    }


    public function test_report_creation_fails_if_stop_does_not_exist()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reports', [
            'rep_sto_id' => 'INVALID',
            'rep_message' => 'Test sur arrêt inexistant',
            'rep_type' => 'graffiti',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['rep_sto_id']);
    }

    public function test_admin_can_get_stats_per_stop()
    {
        $admin = User::factory()->create(['use_role' => 'admin']);
        $stop = Stop::factory()->create();
        Report::factory()->count(3)->create(['rep_sto_id' => $stop->sto_id]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/reports/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['rep_sto_id', 'total']]]);
        $this->assertEquals(3, $response->json('data.0.total'));
    }

    public function test_user_can_filter_reports()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_status' => 'envoyé',
            'created_at' => now()->subDays(2),
        ]);

        Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_sto_id' => $stop->sto_id,
            'rep_status' => 'traité',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reports/filter?status=envoyé');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data')); // Pagination fix
    }

    public function test_admin_can_change_report_status()
    {
        $admin = User::factory()->create(['use_role' => 'admin']);
        $report = Report::factory()->create(['rep_status' => 'envoyé']);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/reports/{$report->rep_id}/status", [
            'status' => 'traité',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Statut mis à jour']);

        $this->assertDatabaseHas('reports', [
            'rep_id' => $report->rep_id,
            'rep_status' => 'traité',
        ]);
    }

    public function test_non_admin_cannot_get_stats()
    {
        $user = User::factory()->create(['use_role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reports/stats');
        $response->assertStatus(403);
    }

    public function test_change_status_fails_if_report_not_found()
    {
        $admin = User::factory()->create(['use_role' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')->putJson('/api/reports/9999/status', [
            'status' => 'traité',
        ]);

        $response->assertStatus(404);
    }

    public function test_change_status_fails_if_status_missing()
    {
        $admin = User::factory()->create(['use_role' => 'admin']);
        $report = Report::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/reports/{$report->rep_id}/status", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_user_can_filter_reports_by_date_and_stop()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_sto_id' => $stop->sto_id,
            'created_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reports/filter?from=' . now()->subDays(4)->toDateString() . '&to=' . now()->subDays(2)->toDateString());
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reports/filter?stop_id=' . $stop->sto_id);
        $response->assertStatus(200);
        $this->assertGreaterThan(0, count($response->json('data.data')));
    }

    public function test_report_creation_fails_with_invalid_type()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/reports', [
            'rep_message' => 'Un message valide',
            'rep_type' => 'invalid_type'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rep_type']);
    }

    public function test_report_creation_succeeds_with_only_route_id()
    {
        $user = User::factory()->create();
        $route = \App\Models\Route::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/reports', [
            'rep_message' => 'Dégât sur la ligne uniquement',
            'rep_type' => \App\Enums\ReportType::values()[0],
            'rep_rou_id' => $route->rou_id
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Signalement enregistré']);
    }

    public function test_report_creation_fails_if_type_missing()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/reports', [
            'rep_message' => 'Message sans type'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rep_type']);
    }

    public function test_report_creation_fails_if_message_missing()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/reports', [
            'rep_type' => \App\Enums\ReportType::values()[0]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rep_message']);
    }

}
