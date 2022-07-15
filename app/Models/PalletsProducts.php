<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PalletsProducts extends Model
{
    use HasFactory;

    protected $table = 'pallets_products';

    protected $fillable = [
        'pallets', 'bol_id'
    ];

    public $timestamps = false;
}
