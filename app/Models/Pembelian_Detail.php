<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian_Detail extends Model
{
    protected $table = 'pembelian_details'; 
    protected $fillable = [
        'pembelian_id',
        'product_id',
        'harga',
        'qty',
        'subtotal',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
