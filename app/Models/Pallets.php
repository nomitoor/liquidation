<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pallets extends Model
{
    use HasFactory;

    protected $table = 'pallet';

    protected $fillable = [
        'category_id', 'description', 'bol_ids', 'total_price', 'total_unit','total_recovery'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function container()
    {
        return $this->belongsToMany('App\Models\Container', 'container_pallets', 'pallet_id', 'container_id');
    }

    public function palletProducts()
    {
        return $this->belongsToMany('App\Models\ScannedProducts', 'product_pallet_relation', 'pallet_id', 'scanned_prodcts_id');
    }
}
