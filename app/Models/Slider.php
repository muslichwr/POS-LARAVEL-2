<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Slider extends Model
{
    protected $fillable = [
        'title', 'slug', 'image', 'link', 'order', 'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function($slider)
        {
            if (empty($slider->slug)) {
                $slider->slug = Str::slug($slider->name);
            }
        });
        
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    protected $casts = [
        'status' => 'boolean'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_sliders')->withTimestamps();
    }
}
