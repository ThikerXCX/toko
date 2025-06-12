<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

    protected $fillable = [
        'name',
        'no_tlp',
        'alamat',
    ];

    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
    public function pembelianDetails()
    {
        return $this->hasManyThrough(Pembelian_Detail::class, Pembelian::class);
    }
}
