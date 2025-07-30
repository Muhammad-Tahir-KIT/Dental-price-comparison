<?php

namespace Tests\Unit\Services;

use App\Interfaces\SupplierRepositoryInterface;
use App\Services\PriceComparisonService;
use Mockery;
use PHPUnit\Framework\Attributes\Test; // <-- ADD THIS LINE
use Tests\TestCase;

class PriceComparisonServiceTest extends TestCase
{
    private $supplierData;

    protected function setUp(): void
    {
        parent::setUp();

        // This is the same data from our InMemory repository,
        // but defined here to keep the unit test isolated.
        $this->supplierData = [
            'Supplier A' => [
                'products' => [
                    'Dental Floss' => [['quantity' => 20, 'price' => 160.00], ['quantity' => 1, 'price' => 9.00]],
                    'Ibuprofen' => [['quantity' => 10, 'price' => 48.00], ['quantity' => 1, 'price' => 5.00]],
                ],
            ],
            'Supplier B' => [
                'products' => [
                    'Dental Floss' => [['quantity' => 10, 'price' => 71.00], ['quantity' => 1, 'price' => 8.00]],
                    'Ibuprofen' => [['quantity' => 100, 'price' => 410.00], ['quantity' => 5, 'price' => 25.00], ['quantity' => 1, 'price' => 6.00]],
                ],
            ],
        ];
    }

    #[Test] // <-- CHANGE THIS
    public function it_correctly_solves_example_1(): void
    {
        // Arrange
        $orderItems = [
            ['product_name' => 'Dental Floss', 'quantity' => 5],
            ['product_name' => 'Ibuprofen', 'quantity' => 12],
        ];

        $mockRepository = Mockery::mock(SupplierRepositoryInterface::class);
        $mockRepository->shouldReceive('getAllSuppliersData')->once()->andReturn($this->supplierData);

        $service = new PriceComparisonService($mockRepository);

        // Act
        $result = $service->findBestPrice($orderItems);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals('Supplier B', $result['supplier']);
        $this->assertEquals(102.00, $result['total_price']);
    }

    #[Test] // <-- CHANGE THIS
    public function it_correctly_solves_example_2(): void
    {
        // Arrange
        $orderItems = [
            ['product_name' => 'Ibuprofen', 'quantity' => 105],
        ];

        $mockRepository = Mockery::mock(SupplierRepositoryInterface::class);
        $mockRepository->shouldReceive('getAllSuppliersData')->once()->andReturn($this->supplierData);

        $service = new PriceComparisonService($mockRepository);

        // Act
        $result = $service->findBestPrice($orderItems);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals('Supplier B', $result['supplier']);
        $this->assertEquals(435.00, $result['total_price']);
    }

    #[Test] // <-- CHANGE THIS
    public function it_returns_null_if_no_supplier_can_fulfill_order(): void
    {
        // Arrange: "Magic Pills" is not sold by any supplier.
        $orderItems = [
            ['product_name' => 'Magic Pills', 'quantity' => 10],
        ];

        $mockRepository = Mockery::mock(SupplierRepositoryInterface::class);
        $mockRepository->shouldReceive('getAllSuppliersData')->once()->andReturn($this->supplierData);

        $service = new PriceComparisonService($mockRepository);

        // Act
        $result = $service->findBestPrice($orderItems);

        // Assert
        $this->assertNull($result);
    }
}
