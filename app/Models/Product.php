<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Defines the many-to-many relationship with Suppliers.
     * A Product can be offered by many Suppliers.
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
            // This is crucial for accessing the 'quantity' and 'price' columns
            // from the 'product_supplier' pivot table.
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
