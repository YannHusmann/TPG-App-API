<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\User;
use App\Models\Report;
use App\Notifications\ReportStatusChangedNotification;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent_when_report_status_changes()
    {
        Notification::fake();

        $admin = User::factory()->create(['use_role' => 'admin']);
        $user  = User::factory()->create();
        $report = Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_status' => 'envoyé',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/reports/{$report->rep_id}/status", [
            'status' => 'en traitement',
        ]);

        $response->assertStatus(200);
        Notification::assertSentTo($user, ReportStatusChangedNotification::class);
    }

    public function test_notification_is_not_sent_for_unchanged_status()
    {
        Notification::fake();

        $admin = User::factory()->create(['use_role' => 'admin']);
        $user  = User::factory()->create();
        $report = Report::factory()->create([
            'rep_use_id' => $user->use_id,
            'rep_status' => 'traité',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/reports/{$report->rep_id}/status", [
            'status' => 'traité',
        ]);

        $response->assertStatus(200);
        Notification::assertNothingSent();
    }
}
