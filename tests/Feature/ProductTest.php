<?php

namespace Tests\Feature;

use App\Jobs\CSVUploadJob;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_new_user(): void{
        //prepare data
        $data = [
            'name' => 'Samuel',
            'email' => 'samuel@gmail.com',
            'password' => 'Samuel001'
        ];

        // Simulate a POST request to register a new user
        $response = $this->postJson('/api/register', $data);

        //endure status code is correct
        $response->assertStatus(201);

        // Assert that the user exists in the database
        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Assert that the response contains the expected JSON structure
        $response->assertJsonStructure([
            'meta' => [
                'code',
                'status',
                'message',
            ],
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'access_token' => [
                    'token',
                    'type',
                    'expires_in',
                ],
            ],
        ]);


    }

    public function test_login_with_valid_credentials():void{
        //create the user in db
        User::factory()->create([
            'email' => 'mark@gmail.com',
            'password' => 'MarkMark001'
        ]);

        // Prepare login credentials
        $credentials = [
            'email' => 'mark@gmail.com',
            'password' => 'MarkMark001',
        ];

        // Simulate a POST request to login with valid credentials
        $response = $this->postJson('/api/login', $credentials);

        // Assert that the response indicates success
        $response->assertStatus(200);

        // Assert that the response contains the expected JSON structure
        $response->assertJsonStructure([
            'meta' => [
                'code',
                'status',
                'message',
            ],
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'access_token' => [
                    'token',
                    'type',
                    'expires_in',
                ],
            ],
        ]);

        // Assert that the user is logged in
        $this->assertTrue(Auth::check());

    }

    public function test_login_with_invalid_credentials():void{
         // Prepare login credentials
         $credentials = [
            'email' => 'mark001@gmail.com',
            'password' => 'MarkeeeMark001',
        ];

        // Simulate a POST request to login with valid credentials
        $response = $this->postJson('/api/login', $credentials);

        // Assert that the response indicates success
        $response->assertStatus(401);
 
        $response->assertJson([
            'status' => 'error',
            'message' => 'Email or password is incorrect',
        ]);
        // Assert that the user is not logged in
        $this->assertFalse(Auth::check());

    }


    public function test_can_upload_csv_file()
    {
        //generate user for authentocation
        $user = User::factory()->create();
        // Mock the CSVUploadJob to avoid actual dispatching
        Queue::fake();

        // Create a sample CSV file
        $file = UploadedFile::fake()->create('test.csv');

        // Simulate a POST request with the sample CSV file
        $response = $this->actingAs($user)->postJson('/api/upload', ['csv_file' => $file]);

         // Assert that the response indicates success
         $response->assertStatus(201)
                  ->assertJson([
                    'status' => 'success',
                    'message' => 'Upload Successful',
                  ]);

        // Assert that the CSVUploadJob was dispatched with correct data
        Queue::assertPushed(CSVUploadJob::class, function ($job) use ($file) {
            return true;
        });

    }

    public function test_display_product_successful(): void
    {
        //generate user for authentication
        $user = User::factory()->create();
        //create a dummy product
        $product = Product::create(['sku' => '1234', 'name' => 'Oluwatobi', 'description' => 'new product', 'brand' => 'Nike', 'created_at' => now(), 'updated_at' => now()]);
        //simulate the get api as protected
        $response = $this->actingAs($user)->get('api/upload/'. '1234');
        //ensure status structure and code is correct 
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
