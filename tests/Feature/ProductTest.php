<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_product(): void
    {
        $data = [
            'name'        => 'Laptop',
            'description' => 'A great laptop',
            'price'       => 999.99,
            'quantity'    => 10,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/products', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Laptop');

        $this->assertDatabaseHas('products', ['name' => 'Laptop']);
    }

    public function test_create_product_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'price', 'quantity']);
    }

    public function test_create_product_rejects_negative_price(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/products', [
            'name'        => 'Test',
            'description' => 'Test desc',
            'price'       => -10,
            'quantity'    => 5,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_can_show_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->putJson("/api/v1/products/{$product->id}", [
            'price' => 1299.99,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.price', 1299.99);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_unauthenticated_user_cannot_access_products(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(401);
    }
}
