<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDetailTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Order $order;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user    = User::factory()->create();
        $client        = Client::factory()->create();
        $this->order   = Order::factory()->create(['client_id' => $client->id]);
        $this->product = Product::factory()->create();
    }

    public function test_can_list_order_details(): void
    {
        OrderDetail::factory()->count(3)->create([
            'order_id'   => $this->order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/order-details');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_order_detail(): void
    {
        $data = [
            'order_id'   => $this->order->id,
            'product_id' => $this->product->id,
            'quantity'   => 5,
            'price'      => 99.99,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/order-details', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.quantity', 5);

        $this->assertDatabaseHas('order_details', ['order_id' => $this->order->id]);
    }

    public function test_create_order_detail_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/order-details', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id', 'product_id', 'quantity', 'price']);
    }

    public function test_create_order_detail_requires_existing_relations(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/order-details', [
            'order_id'   => 9999,
            'product_id' => 9999,
            'quantity'   => 1,
            'price'      => 10.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id', 'product_id']);
    }

    public function test_can_show_order_detail_with_relations(): void
    {
        $detail = OrderDetail::factory()->create([
            'order_id'   => $this->order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/v1/order-details/{$detail->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['order', 'product']]);
    }

    public function test_can_update_order_detail(): void
    {
        $detail = OrderDetail::factory()->create([
            'order_id'   => $this->order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/v1/order-details/{$detail->id}", [
            'quantity' => 10,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.quantity', 10);
    }

    public function test_can_delete_order_detail(): void
    {
        $detail = OrderDetail::factory()->create([
            'order_id'   => $this->order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/v1/order-details/{$detail->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('order_details', ['id' => $detail->id]);
    }

    public function test_unauthenticated_user_cannot_access_order_details(): void
    {
        $response = $this->getJson('/api/v1/order-details');

        $response->assertStatus(401);
    }
}
