<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'quantity', 'purchase_price', 'subtotal'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_price' => 'decimal:3',
        'subtotal' => 'decimal:3'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
