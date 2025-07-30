<?php

namespace App\Interfaces;

interface SupplierRepositoryInterface
{
    /**
     * Retrieves all suppliers and their product pricing.
     *
     * @return array
     */
    public function getAllSuppliersData(): array;
}
