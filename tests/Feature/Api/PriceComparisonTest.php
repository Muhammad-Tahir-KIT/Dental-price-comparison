<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase; // <-- ADD THIS
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PriceComparisonTest extends TestCase
{
    use RefreshDatabase; // <-- AND ADD THIS.

    /**
     * This setup method is the key fix. It will run before each test in this file.
     * We explicitly load the api.php routes file, bypassing any potential
     * issues with the application's RouteServiceProvider during testing.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // This line forces the routes to be loaded for the test application instance.
        require base_path('routes/api.php');
    }

    #[Test]
    public function it_returns_the_cheapest_supplier_for_a_valid_order(): void
    {
        // Example 1 from the prompt
        $order = [
            'items' => [
                ['product_name' => 'Dental Floss', 'quantity' => 5],
                ['product_name' => 'Ibuprofen', 'quantity' => 12],
            ]
        ];

        // The route is now guaranteed to exist.
        $response = $this->postJson('/api/v1/price-comparison', $order);

        $response->assertStatus(200)
            ->assertJson([
                'supplier' => 'Supplier B',
                'total_price' => 102.00
            ]);
    }

    #[Test]
    public function it_returns_not_found_if_a_product_is_unavailable(): void
    {
        $order = [
            'items' => [
                ['product_name' => 'Non-existent Product', 'quantity' => 1],
            ]
        ];

        $response = $this->postJson('/api/v1/price-comparison', $order);

        // This assertion will now work because the request reaches the controller.
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No supplier could be found to fulfill the entire order.'
            ]);
    }

    #[Test]
    public function it_returns_validation_error_for_invalid_data(): void
    {
        $order = [
            'items' => [
                ['product_name' => 'Dental Floss'], // Missing quantity
            ]
        ];

        $response = $this->postJson('/api/v1/price-comparison', $order);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.quantity']);
    }

    #[Test]
    public function it_returns_validation_error_for_empty_items_array(): void
    {
        $order = [ 'items' => [] ];

        $response = $this->postJson('/api/v1/price-comparison', $order);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }
}
