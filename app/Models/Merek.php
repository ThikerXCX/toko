<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merek extends Model
{

    protected $fillable = [
        'name',
        'description',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
