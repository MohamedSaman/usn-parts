<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'product_id',
        'supplier_id',
        'order_date',
        'received_date',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(ProductDetail::class, 'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(ProductSupplier::class, 'supplier_id');
    }
}
