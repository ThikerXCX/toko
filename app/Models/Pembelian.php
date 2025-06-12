<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{

    protected $fillable = [
        'supplier_id',
        'no_faktur',
        'tanggal',
        'total',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(Pembelian_Detail::class);
    }
}
