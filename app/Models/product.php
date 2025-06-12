<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    protected $fillable = [
        'satuan_id', 'category_id', 'merek_id', 'sku', 'stok_minimal',
        'name', 'slug', 'harga_beli', 'harga_jual',
        'stok', 'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function merek()
    {
        return $this->belongsTo(Merek::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
