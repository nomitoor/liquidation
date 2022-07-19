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
        'category_id', 'description', 'bol_ids', 'total_price', 'total_unit'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
