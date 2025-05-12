<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_link_is_sent_to_valid_user()
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/forgot-password', [
            'use_email' => $user->use_email,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => trans('passwords.sent')]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }


    public function test_password_reset_fails_for_non_existent_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'use_email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['use_email']);
    }

    public function test_password_reset_fails_for_invalid_email_format()
    {
        $response = $this->postJson('/api/forgot-password', [
            'use_email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['use_email']);
    }

    public function test_password_reset_fails_when_email_is_missing()
    {
        $response = $this->postJson('/api/forgot-password', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['use_email']);
    }

    public function test_password_reset_link_contains_token_and_email()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson('/api/forgot-password', [
            'use_email' => $user->use_email,
        ]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification, $channels) use ($user) {
            $url = $notification->getResetUrl($user);
            return str_contains($notification->toMail($user)->actionUrl, $url);
        });
    }

}
