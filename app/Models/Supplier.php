<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are not mass assignable.
     * Using an empty array means all attributes are mass assignable.
     * This is convenient for seeders but be more specific in production apps.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Defines the many-to-many relationship with Products.
     * A Supplier can have many Products.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            // This is crucial for accessing the 'quantity' and 'price' columns
            // from the 'product_supplier' pivot table.
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
