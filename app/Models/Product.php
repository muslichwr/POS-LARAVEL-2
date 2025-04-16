<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'supplier_id', 'name', 'slug', 'sku', 'price', 'stock', 'description', 'image'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function($product)
        {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
        
    }

    // Tambahkan akses untuk ambil URL gambar
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function sliders()
    {
        return $this->belongsToMany(Slider::class, 'product_slider')->withTimestamps();
    }
}
