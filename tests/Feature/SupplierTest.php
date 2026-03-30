<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_suppliers(): void
    {
        Supplier::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson('/api/v1/suppliers');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_supplier(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/suppliers', [
            'name' => 'Acme Corp',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Acme Corp');

        $this->assertDatabaseHas('suppliers', ['name' => 'Acme Corp']);
    }

    public function test_create_supplier_requires_name(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/suppliers', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_show_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/v1/suppliers/{$supplier->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $supplier->id);
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->putJson("/api/v1/suppliers/{$supplier->id}", [
            'name' => 'Updated Corp',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Corp');
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/v1/suppliers/{$supplier->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    public function test_unauthenticated_user_cannot_access_suppliers(): void
    {
        $response = $this->getJson('/api/v1/suppliers');

        $response->assertStatus(401);
    }
}
