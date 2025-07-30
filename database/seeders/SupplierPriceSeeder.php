<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SupplierPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use Laravel's schema builder for database-agnostic foreign key handling
        Schema::disableForeignKeyConstraints();

        // Clear out old data to make the seeder re-runnable
        Supplier::truncate();
        Product::truncate();
        DB::table('product_supplier')->truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // --- The rest of the seeder is the same ---

        // Create Suppliers
        $supplierA = Supplier::create(['name' => 'Supplier A']);
        $supplierB = Supplier::create(['name' => 'Supplier B']);

        // Create Products
        $dentalFloss = Product::create(['name' => 'Dental Floss']);
        $ibuprofen = Product::create(['name' => 'Ibuprofen']);

        // Attach pricing tiers using the relationship
        // Supplier A's prices
        $supplierA->products()->attach($dentalFloss->id, ['quantity' => 1, 'price' => 9.00]);
        $supplierA->products()->attach($dentalFloss->id, ['quantity' => 20, 'price' => 160.00]);
        $supplierA->products()->attach($ibuprofen->id, ['quantity' => 1, 'price' => 5.00]);
        $supplierA->products()->attach($ibuprofen->id, ['quantity' => 10, 'price' => 48.00]);

        // Supplier B's prices
        $supplierB->products()->attach($dentalFloss->id, ['quantity' => 1, 'price' => 8.00]);
        $supplierB->products()->attach($dentalFloss->id, ['quantity' => 10, 'price' => 71.00]);
        $supplierB->products()->attach($ibuprofen->id, ['quantity' => 1, 'price' => 6.00]);
        $supplierB->products()->attach($ibuprofen->id, ['quantity' => 5, 'price' => 25.00]);
        $supplierB->products()->attach($ibuprofen->id, ['quantity' => 100, 'price' => 410.00]);
    }
}
