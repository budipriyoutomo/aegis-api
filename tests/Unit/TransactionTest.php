<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
    }

    public function test_sale_transaction_decreases_stock()
    {
        $this->authenticate();

        $product = Product::factory()->create(['stock' => 50]);

        $response = $this->postJson('/api/transactions', [
            'product_id' => $product->id,
            'quantity' => 10,
            'type' => 'sale'
        ]);

        $response->assertStatus(201)->assertJson([
            'message' => 'Transaction processed successfully'
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 40 // 50 - 10
        ]);
    }

    public function test_refund_transaction_increases_stock()
    {
        $this->authenticate();

        $product = Product::factory()->create(['stock' => 30]);

        $response = $this->postJson('/api/transactions', [
            'product_id' => $product->id,
            'quantity' => 5,
            'type' => 'refund'
        ]);

        $response->assertStatus(201)->assertJson([
            'message' => 'Transaction processed successfully'
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 35 // 30 + 5
        ]);
    }
}
