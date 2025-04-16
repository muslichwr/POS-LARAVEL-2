<?php

namespace App\Observers;

use App\Models\Sale;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        // Jika status langsung completed saat pembuatan
        if ($sale->status === 'completed') {
            $this->decreaseStock($sale);
        }
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        // Cek apakah status berubah
        if ($sale->isDirty('status')) {
            match ($sale->status) {
                'completed' => $this->decreaseStock($sale),
                'canceled' => $this->increaseStock($sale),
                default => null,
            };
        }
    }

    /**
     * Kurangi stok saat penjualan selesai
     */
    protected function decreaseStock(Sale $sale): void
    {
        foreach ($sale->details as $detail) {
            $product = $detail->product;
            if ($product->stock < $detail->quantity) {
                throw new \Exception("Stok produk {$product->name} tidak mencukupi!");
            }
            $product->stock -= $detail->quantity;
            $product->save();
        }
    }

    /**
     * Kembalikan stok saat penjualan dibatalkan
     */
    protected function increaseStock(Sale $sale): void
    {
        foreach ($sale->details as $detail) {
            $product = $detail->product;
            $product->stock += $detail->quantity;
            $product->save();
        }
    }
    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "force deleted" event.
     */
    public function forceDeleted(Sale $sale): void
    {
        //
    }
}
