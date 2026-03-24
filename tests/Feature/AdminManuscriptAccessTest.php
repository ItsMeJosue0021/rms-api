<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminManuscriptAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_admin_manuscripts(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/admin/manuscripts')
            ->assertOk();
    }

    public function test_regular_user_cannot_access_admin_manuscripts(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/admin/manuscripts')
            ->assertForbidden()
            ->assertJson([
                'message' => 'Forbidden.',
            ]);
    }
}
