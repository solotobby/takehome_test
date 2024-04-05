<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_display_product_successful(): void
    {
        $user = User::factory()->create();

        $product = Product::create(['sku' => '1234', 'name' => 'Oluwatobi', 'description' => 'new product', 'brand' => 'Nike', 'created_at' => now(), 'updated_at' => now()]);
        
        $response = $this->actingAs($user)->get('api/upload/'. '1234');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Product details',
                     'data' => [
                            'sku' => $product->sku,
                            'name' => $product->name,
                            'description' => $product->description,
                            'brand' => $product->brand
                     ],
                 ]);
    }

    public function test_returns_error_if_product_not_found()
    {
        $user = User::factory()->create();
        // Simulate a GET request for a non-existent product
        $response = $this->actingAs($user)->get('api/upload/'. 'rudjfskjhkd');

        // Assert that the response indicates an error
        $response->assertStatus(401)
                 ->assertJson([
                    'status' => 'error',
                    'message' => 'No product found',
                 ]);
    }
}
