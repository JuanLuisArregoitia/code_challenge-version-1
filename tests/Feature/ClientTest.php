<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_clients(): void
    {
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson('/api/v1/clients');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_client(): void
    {
        $data = [
            'name'     => 'John',
            'lastname' => 'Doe',
            'email'    => 'john@example.com',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/clients', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'John');

        $this->assertDatabaseHas('clients', $data);
    }

    public function test_create_client_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/clients', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'lastname', 'email']);
    }

    public function test_can_show_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $client->id);
    }

    public function test_show_returns_404_for_nonexistent_client(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/v1/clients/999');

        $response->assertStatus(404);
    }

    public function test_can_update_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->putJson("/api/v1/clients/{$client->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_unauthenticated_user_cannot_access_clients(): void
    {
        $response = $this->getJson('/api/v1/clients');

        $response->assertStatus(401);
    }
}
