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
            'rep_status' => 'envoyé',
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/reports/{$report->rep_id}", [
            'rep_message' => 'Message modifié',
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
        $admin = User::factory()->create(['use_role' => 'admin']);
        $stop = Stop::factory()->create();

        Report::factory()->count(3)->create([
            'rep_sto_id' => $stop->sto_id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/reports/all');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    public function test_non_admin_cannot_see_all_reports()
    {
        $user = User::factory()->create(['use_role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reports/all');

        $response->assertStatus(403);
    }
}