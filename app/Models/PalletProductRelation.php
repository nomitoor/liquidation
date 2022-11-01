<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PalletProductRelation extends Model
{
    use HasFactory;
    protected $table = 'product_pallet_relations';

    protected $fillable = [
        'pallet_id', 'scanned_products_id','bol_id','type'
    ];

}
