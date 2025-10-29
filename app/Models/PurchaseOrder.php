<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'supplier_id',
        'order_date',
        'received_date',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(ProductSupplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'order_id');

    }
    public function detail()
{
    return $this->hasOne(ProductDetail::class, 'code');
}

}
