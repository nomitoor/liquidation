<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'pallet_id'
    ];

    public function pallets()
    {
        return $this->belongsToMany('App\Models\Pallets', 'container_pallets', 'container_id', 'pallet_id');
    }
}
