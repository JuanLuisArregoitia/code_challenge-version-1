<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user   = User::factory()->create();
        $this->client = Client::factory()->create();
    }

    public function test_can_list_orders(): void
    {
        Order::factory()->count(3)->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_order(): void
    {
        $data = [
            'order_number' => 'ORD-001',
            'status_id'    => 1,
            'client_id'    => $this->client->id,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/orders', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.order_number', 'ORD-001');

        $this->assertDatabaseHas('orders', ['order_number' => 'ORD-001']);
    }

    public function test_create_order_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_number', 'status_id', 'client_id']);
    }

    public function test_create_order_requires_existing_client(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/orders', [
            'order_number' => 'ORD-001',
            'status_id'    => 1,
            'client_id'    => 9999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['client_id']);
    }

    public function test_can_show_order_with_relations(): void
    {
        $order = Order::factory()->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->user)->getJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonStructure(['data' => ['client']]);
    }

    public function test_can_update_order(): void
    {
        $order = Order::factory()->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->user)->putJson("/api/v1/orders/{$order->id}", [
            'status_id' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status_id', 2);
    }

    public function test_can_delete_order(): void
    {
        $order = Order::factory()->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('orders', ['id' => $order->id]);
    }

    public function test_unauthenticated_user_cannot_access_orders(): void
    {
        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(401);
    }
}
