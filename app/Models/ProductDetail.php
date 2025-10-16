<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'supplier_id',
    ];

    public function price(): HasOne
    {
        return $this->hasOne(ProductPrice::class, 'product_id');
    }

    public function stock(): HasOne
    {
        return $this->hasOne(ProductStock::class, 'product_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(BrandList::class, 'brand_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryList::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(ProductSupplier::class, 'supplier_id');
    }
}
