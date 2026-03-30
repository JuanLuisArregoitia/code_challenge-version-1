<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Supplier $supplier;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user     = User::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->product  = Product::factory()->create();
    }

    public function test_can_list_supplier_products(): void
    {
        SupplierProduct::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'product_id'  => $this->product->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/supplier-products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_supplier_product(): void
    {
        $data = [
            'supplier_id' => $this->supplier->id,
            'product_id'  => $this->product->id,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/supplier-products', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('supplier_products', $data);
    }

    public function test_create_supplier_product_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/supplier-products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['supplier_id', 'product_id']);
    }

    public function test_create_supplier_product_requires_existing_relations(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/supplier-products', [
            'supplier_id' => 9999,
            'product_id'  => 9999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['supplier_id', 'product_id']);
    }

    public function test_can_show_supplier_product_with_relations(): void
    {
        $sp = SupplierProduct::factory()->create([
            'supplier_id' => $this->supplier->id,
            'product_id'  => $this->product->id,
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/v1/supplier-products/{$sp->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['supplier', 'product']]);
    }

    public function test_can_update_supplier_product(): void
    {
        $sp = SupplierProduct::factory()->create([
            'supplier_id' => $this->supplier->id,
            'product_id'  => $this->product->id,
        ]);

        $newProduct = Product::factory()->create();

        $response = $this->actingAs($this->user)->putJson("/api/v1/supplier-products/{$sp->id}", [
            'product_id' => $newProduct->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.product_id', $newProduct->id);
    }

    public function test_can_delete_supplier_product(): void
    {
        $sp = SupplierProduct::factory()->create([
            'supplier_id' => $this->supplier->id,
            'product_id'  => $this->product->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/v1/supplier-products/{$sp->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('supplier_products', ['id' => $sp->id]);
    }

    public function test_unauthenticated_user_cannot_access_supplier_products(): void
    {
        $response = $this->getJson('/api/v1/supplier-products');

        $response->assertStatus(401);
    }
}
