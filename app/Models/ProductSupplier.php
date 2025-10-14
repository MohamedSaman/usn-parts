<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSupplier extends Model
{
    use HasFactory;
    
    protected $fillable = ['name','businessname', 'contact', 'address', 'email', 'phone', 'status', 'notes'];
    
    /**
     * Get the Productes from this supplier
     */
    public function Productes(): HasMany
    {
        return $this->hasMany(ProductDetail::class, 'supplier_id');
    }
}
