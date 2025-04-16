<?php

namespace App\Observers;

use App\Models\Purchase;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     */
    public function created(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase)
    {
        if ($purchase->isDirty('status')) {
            if ($purchase->status === 'completed') {
                $this->increaseStock($purchase);
            } elseif ($purchase->status === 'canceled') {
                $this->decreaseStock($purchase);
            }
        }
    }
    
    protected function increaseStock(Purchase $purchase)
    {
        if ($purchase->status === 'completed') {
            foreach ($purchase->details as $detail) {
                $product = $detail->product;
                $product->stock += $detail->quantity;
                $product->save();
            }
        }
    }

    protected function decreaseStock(Purchase $purchase)
    {
        foreach ($purchase->details as $detail) {
            $product = $detail->product;
            $product->stock -= $detail->quantity;
            $product->save();
        }
    }

    /**
     * Handle the Purchase "deleted" event.
     */
    public function deleted(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "restored" event.
     */
    public function restored(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "force deleted" event.
     */
    public function forceDeleted(Purchase $purchase): void
    {
        //
    }
}
