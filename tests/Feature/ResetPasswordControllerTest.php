<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use App\Models\User;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_reset()
    {
        $user = User::factory()->create([
            'use_email' => 'user@example.com',
        ]);

        $token = Password::createToken($user);

        $response = $this->postJson('/reset-password', [
            'token' => $token,
            'use_email' => $user->use_email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => trans(Password::PASSWORD_RESET)]);
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->use_password));
    }

    public function test_reset_fails_with_invalid_token()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/reset-password', [
            'token' => 'invalid-token',
            'use_email' => $user->use_email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => trans(Password::INVALID_TOKEN)]);
    }

    public function test_reset_fails_if_email_does_not_exist()
    {
        $response = $this->postJson('/reset-password', [
            'token' => 'sometoken',
            'use_email' => 'nonexistent@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['use_email']);
    }

    public function test_reset_fails_if_passwords_do_not_match()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/reset-password', [
            'token' => $token,
            'use_email' => $user->use_email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'WrongConfirmation!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_reset_fails_if_password_too_short()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/reset-password', [
            'token' => $token,
            'use_email' => $user->use_email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_reset_fails_if_fields_missing()
    {
        $response = $this->postJson('/reset-password', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['token', 'use_email', 'password']);
    }
    public function test_reset_fails_if_token_missing()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/reset-password', [
            'use_email' => $user->use_email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['token']);
    }
    public function test_reset_fails_if_email_missing()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/reset-password', [
            'token' => $token,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['use_email']);
    }
    public function test_reset_fails_if_password_missing()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/reset-password', [
            'token' => $token,
            'use_email' => $user->use_email,
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }
    public function test_reset_fails_if_password_confirmation_missing()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/reset-password', [
            'token' => $token,
            'use_email' => $user->use_email,
            'password' => 'NewPassword123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }
    
}
