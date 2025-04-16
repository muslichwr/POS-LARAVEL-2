<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductSlider extends Pivot
{
    protected $table = 'product_sliders';

    protected $fillable = [
        'slider_id',
        'product_id',
    ];

    // Jika ada kolom tambahan di tabel pivot, tambahkan di sini
    // Contoh: protected $fillable = ['slider_id', 'product_id', 'position'];
    
    // Aktifkan timestamps
    public $timestamps = true;
}
