<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'model',
        'image',
        'description',
        'barcode',
        'status',
        'brand_id',
        'category_id',
        'stock_id',
        'price_id',
        'supplier_id'
    ];

 
    /**
     * Get the supplier of the Product
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(ProductSupplier::class);
    }

    /**
     * Get the price information for the Product
     */
    public function price(): HasOne
    {
        return $this->hasOne(ProductPrice::class, 'product_id');
    }

    /**
     * Get the stock information for the Product
     */
    public function stock(): HasOne
    {
        return $this->hasOne(ProductStock::class, 'product_id');
    }

}
