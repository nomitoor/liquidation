<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pallets extends Model
{
    use HasFactory;

    protected $table = 'pallet';

    protected $fillable = [
        'pallets_id', 'bol_ids', 'total_price', 'total_unit'
    ];
}
