<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_order_id',
        'amount',
        'payment_method',
        'payment_reference',
        'reference',
        'payment_date',
        'status',
        'is_completed',
        'cheque_number',
        'bank_name',
        'cheque_date',
        'cheque_status',
        'bank_transaction',
        'notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(ProductSupplier::class, 'supplier_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function allocations()
    {
        return $this->hasMany(PurchasePaymentAllocation::class, 'purchase_payment_id');
    }
}
