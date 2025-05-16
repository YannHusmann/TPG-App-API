<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stop;

class ThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_too_many_login_attempts_are_blocked()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'invalid@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->postJson('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    public function test_too_many_register_attempts_are_blocked()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/register', [
                'use_username' => 'user'.$i,
                'use_email' => 'user'.$i.'@example.com',
                'use_password' => 'password',
            ]);
        }

        $response = $this->postJson('/api/register', [
            'use_username' => 'user5',
            'use_email' => 'user5@example.com',
            'use_password' => 'password',
        ]);

        $response->assertStatus(429);
    }

    public function test_too_many_report_creation_attempts_are_blocked()
    {
        $user = User::factory()->create();
        $stop = Stop::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user, 'sanctum')->postJson('/api/reports', [
                'rep_sto_id' => $stop->sto_id,
                'rep_message' => 'Message ' . $i,
                'rep_type' => 'graffiti',
                'latitude' => 46.2,
                'longitude' => 6.1
            ]);
        }

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reports', [
            'rep_sto_id' => $stop->sto_id,
            'rep_message' => 'Message final',
            'rep_type' => 'graffiti',
            'latitude' => 46.2,
            'longitude' => 6.1
        ]);

        $response->assertStatus(429);
    }

    public function test_too_many_forgot_password_requests_are_blocked()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/forgot-password', [
                'email' => 'test@example.com',
            ]);
        }

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(429);
    }

    public function test_too_many_reset_password_requests_are_blocked()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/reset-password', [
                'email' => 'test@example.com',
                'token' => 'invalid-token',
                'password' => 'NewPassword1!',
                'password_confirmation' => 'NewPassword1!',
            ]);
        }

        $response = $this->postJson('/reset-password', [
            'email' => 'test@example.com',
            'token' => 'invalid-token',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response->assertStatus(429);
    }

}
