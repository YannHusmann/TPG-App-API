<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_and_delete_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Test mise à jour
        $response = $this->putJson('/api/user/update', [
            'use_username' => 'UpdatedName',
        ]);
        $response->assertStatus(200)->assertJsonFragment(['use_username' => 'UpdatedName']);

        // Test suppression
        $response = $this->deleteJson('/api/user');
        $response->assertStatus(200)->assertJsonFragment(['message' => 'Utilisateur supprimé avec succès']);
    }

    public function test_update_fails_with_duplicate_email()
    {
        $user1 = User::factory()->create(['use_email' => 'one@example.com']);
        $user2 = User::factory()->create(['use_email' => 'two@example.com']);
        $this->actingAs($user1, 'sanctum');

        $response = $this->putJson('/api/user/update', [
            'use_email' => 'two@example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['use_email']);
    }

    public function test_update_password_and_verify_it_was_changed()
    {
        $user = User::factory()->create([
            'use_password' => Hash::make('OldPassword123!'),
        ]);
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/user/update', [
            'use_password' => 'NewPassword123!',
        ]);
        $response->assertStatus(200);

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->use_password));
    }

    public function test_update_rejects_invalid_email()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/user/update', [
            'use_email' => 'not-an-email',
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['use_email']);
    }

    public function test_guest_cannot_update_or_delete()
    {
        $this->putJson('/api/user/update', ['use_username' => 'test'])->assertStatus(401);
        $this->deleteJson('/api/user')->assertStatus(401);
    }

    public function test_no_update_if_data_unchanged()
    {
        $user = User::factory()->create([
            'use_username' => 'OriginalName'
        ]);
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/user/update', [
            'use_username' => 'OriginalName'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('OriginalName', $user->fresh()->use_username);
    }

    public function test_update_only_email_does_not_change_username_or_password()
    {
        $user = User::factory()->create([
            'use_username' => 'Original',
            'use_password' => Hash::make('OldPass123!'),
        ]);
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/user/update', [
            'use_email' => 'new@example.com',
        ]);

        $response->assertStatus(200);
        $fresh = $user->fresh();

        $this->assertEquals('Original', $fresh->use_username);
        $this->assertTrue(Hash::check('OldPass123!', $fresh->use_password));
        $this->assertEquals('new@example.com', $fresh->use_email);
    }

    public function test_user_is_deleted_from_database()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $this->deleteJson('/api/user')->assertStatus(200);
        $this->assertDatabaseMissing('users', ['use_id' => $user->use_id]);
    }

    public function test_update_fails_with_too_long_username()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/user/update', [
            'use_username' => str_repeat('a', 300),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['use_username']);
    }

    public function test_user_update_accepts_partial_update()
    {
        $user = User::factory()->create([
            'use_username' => 'Jean',
            'use_email' => 'jean@example.com',
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/user/update', [
            'use_username' => 'JeanModifié',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('JeanModifié', $user->fresh()->use_username);
        $this->assertEquals('jean@example.com', $user->fresh()->use_email);
    }

    public function test_user_cannot_update_own_role()
    {
        $user = User::factory()->create(['use_role' => 'user']);
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/user/update', [
            'use_role' => 'admin',
        ]);

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Vous ne pouvez pas modifier votre rôle.']);
    }

}
