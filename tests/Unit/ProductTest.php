<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
    }

    public function test_create_product()
    {
        $this->authenticate();

        $response = $this->postJson('/api/products', [
            'name' => 'Sample Product',
            'description' => 'Product description',
            'price' => 100.00,
            'stock' => 50
        ]);

        $response->assertStatus(201)->assertJson([
            'message' => 'Product created successfully'
        ]);
    }

    public function test_view_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)->assertJson(['id' => $product->id]);
    }

    public function test_update_product()
    {
        $product = Product::factory()->create();
        $this->authenticate();

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'price' => 150.00,
            'stock' => 45
        ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'Product updated successfully'
        ]);
    }

    public function test_delete_product()
    {
        $product = Product::factory()->create();
        $this->authenticate();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)->assertJson([
            'message' => 'Product deleted successfully'
        ]);
    }
}
