<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login()
    {
        $response = $this->postJson('/api/register', [
            'use_username' => 'Yann',
            'use_email' => 'yann@example.com',
            'use_password' => 'Password123!'
        ]);
        $response->assertStatus(201);

        $response = $this->postJson('/api/login', [
            'use_email' => 'yann@example.com',
            'use_password' => 'Password123!'
        ]);
        $response->assertStatus(200)->assertJsonStructure(['token', 'user']);
    }

    public function test_user_registration_validation_errors()
    {
        // Cas 1 : champs manquants
        $response = $this->postJson('/api/register', []);
        $response->assertStatus(422)->assertJsonValidationErrors(['use_username', 'use_email', 'use_password']);

        // Cas 2 : email invalide et mot de passe trop court
        $response = $this->postJson('/api/register', [
            'use_username' => 'Yann',
            'use_email' => 'not-an-email',
            'use_password' => 'short'
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['use_email', 'use_password']);
    }

    public function test_user_login_validation_errors()
    {
        // Cas 1 : champs manquants
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422)->assertJsonValidationErrors(['use_email', 'use_password']);

        // Cas 2 : identifiants incorrects
        $response = $this->postJson('/api/login', [
            'use_email' => 'wrong@example.com',
            'use_password' => 'Password123!'
        ]);
        $response->assertStatus(401)->assertJsonFragment(['message' => 'Identifiants incorrects']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/logout');
        $response->assertStatus(200)->assertJsonFragment(['message' => 'Déconnecté']);
    }

    public function test_user_can_get_his_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/me');
        $response->assertStatus(200)->assertJsonFragment(['use_email' => $user->use_email]);
    }

    public function test_unauthenticated_user_cannot_access_profile_or_logout()
    {
        $this->postJson('/api/logout')->assertStatus(401);
        $this->getJson('/api/me')->assertStatus(401);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        User::factory()->create(['use_email' => 'test@example.com']);

        $response = $this->postJson('/api/register', [
            'use_username' => 'AutreNom',
            'use_email' => 'test@example.com',
            'use_password' => 'Password123!'
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['use_email']);
    }

    public function test_login_fails_with_wrong_password()
    {
        User::factory()->create([
            'use_email' => 'test@example.com',
            'use_password' => bcrypt('CorrectPass123!')
        ]);

        $response = $this->postJson('/api/login', [
            'use_email' => 'test@example.com',
            'use_password' => 'WrongPass123!',
        ]);

        $response->assertStatus(401)->assertJsonFragment(['message' => 'Identifiants incorrects']);
    }


}
