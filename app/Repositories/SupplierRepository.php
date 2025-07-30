<?php

namespace App\Repositories;

use App\Interfaces\SupplierRepositoryInterface;
use App\Models\Supplier;

class SupplierRepository implements SupplierRepositoryInterface
{
    protected array $suppliersData = [
        'Supplier A' => [
            'products' => [
                'Dental Floss' => [
                    ['quantity' => 20, 'price' => 160.00],
                    ['quantity' => 1, 'price' => 9.00],
                ],
                'Ibuprofen' => [
                    ['quantity' => 10, 'price' => 48.00],
                    ['quantity' => 1, 'price' => 5.00],
                ],
            ],
        ],
        'Supplier B' => [
            'products' => [
                'Dental Floss' => [
                    ['quantity' => 10, 'price' => 71.00],
                    ['quantity' => 1, 'price' => 8.00],
                ],
                'Ibuprofen' => [
                    ['quantity' => 100, 'price' => 410.00],
                    ['quantity' => 5, 'price' => 25.00],
                    ['quantity' => 1, 'price' => 6.00],
                ],
            ],
        ],
    ];



    public function getAllSuppliersData(): array
    {
        $suppliers = Supplier::with(['products' => function ($query) {
            $query->select('products.id', 'name'); // reduce select columns
        }])->get();

        $suppliersData = [];

        foreach ($suppliers as $supplier) {
            foreach ($supplier->products as $product) {
                $suppliersData[$supplier->name]['products'][$product->name][] = [
                    'quantity' => $product->pivot->quantity,
                    'price' => (float) $product->pivot->price,
                ];
            }
        }

        return $this->suppliersData;
    }
}
