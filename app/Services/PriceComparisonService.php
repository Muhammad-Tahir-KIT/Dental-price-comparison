<?php

namespace App\Services;

use App\Interfaces\SupplierRepositoryInterface;
use Illuminate\Support\Collection;

class PriceComparisonService
{
    protected SupplierRepositoryInterface $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Finds the best supplier for a given list of order items.
     *
     * @param array $orderItems e.g. [['product_name' => 'Dental Floss', 'quantity' => 5]]
     * @return array|null An array with 'supplier', 'total_price', and 'breakdown' or null if no suitable supplier is found.
     */
    public function findBestPrice(array $orderItems): ?array
    {
        $allSuppliersData = $this->supplierRepository->getAllSuppliersData();
        $supplierCosts = [];

        foreach ($allSuppliersData as $supplierName => $supplierData) {
            $costDetails = $this->calculateTotalCostForSupplier($supplierData, $orderItems);

            // If cost is null, it means the supplier cannot fulfill the order.
            if ($costDetails !== null) {
                $supplierCosts[$supplierName] = $costDetails;
            }
        }

        if (empty($supplierCosts)) {
            return null; // No supplier could fulfill the entire order.
        }

        // Sort the suppliers by 'total_price' ascending, while maintaining key association.
        uasort($supplierCosts, function ($a, $b) {
            return $a['total_price'] <=> $b['total_price'];
        });

        // After sorting, the cheapest supplier is the first one in the array.
        $bestSupplierName = array_key_first($supplierCosts);

        return [
            'supplier' => $bestSupplierName,
            'total_price' => $supplierCosts[$bestSupplierName]['total_price'],
            'breakdown' => $supplierCosts[$bestSupplierName]['breakdown'],
        ];
    }

    /**
     * Calculates the total cost of an entire order from a single supplier.
     *
     * @param array $supplierData
     * @param array $orderItems
     * @return array|null Cost details or null if the supplier can't fulfill the order.
     */
    private function calculateTotalCostForSupplier(array $supplierData, array $orderItems): ?array
    {
        $totalPrice = 0;
        $breakdown = [];

        foreach ($orderItems as $item) {
            $productName = $item['product_name'];
            $requestedQuantity = $item['quantity'];

            if (!isset($supplierData['products'][$productName])) {
                return null; // Supplier doesn't carry this product.
            }

            $productCostDetails = $this->calculateCostForProduct(
                $supplierData['products'][$productName],
                $requestedQuantity
            );

            if ($productCostDetails['cost'] === INF) {
                return null; // Cannot fulfill the required quantity for this product.
            }

            $totalPrice += $productCostDetails['cost'];
            $breakdown[$productName] = $productCostDetails['details'];
        }

        return [
            'total_price' => $totalPrice,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculates the minimum cost for a single product using a greedy algorithm.
     *
     * @param array $pricingTiers
     * @param int $requestedQuantity
     * @return array An array containing the total cost and the breakdown of packages.
     */
    private function calculateCostForProduct(array $pricingTiers, int $requestedQuantity): array
    {
        // Sort tiers by quantity descending to use largest packages first (greedy approach)
        $sortedTiers = collect($pricingTiers)->sortByDesc('quantity')->values()->all();

        $cost = 0;
        $remainingQuantity = $requestedQuantity;
        $details = [];

        foreach ($sortedTiers as $tier) {
            if ($remainingQuantity >= $tier['quantity']) {
                $numPackages = floor($remainingQuantity / $tier['quantity']);
                $cost += $numPackages * $tier['price'];
                $remainingQuantity -= $numPackages * $tier['quantity'];
                $details[] = [
                    'packages_count' => (int)$numPackages,
                    'package_size' => $tier['quantity'],
                    'package_price' => $tier['price'],
                ];
            }
        }

        // If after checking all tiers, there's still a remaining quantity,
        // it means the smallest package size (e.g. 1 unit) is not available to fulfill the rest.
        if ($remainingQuantity > 0) {
            return ['cost' => INF, 'details' => []]; // Cannot fulfill order exactly.
        }

        return ['cost' => $cost, 'details' => $details];
    }
}
