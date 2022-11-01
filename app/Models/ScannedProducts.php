<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScannedProducts extends Model
{
    use HasFactory;

    protected $table = 'scanned_products';

    protected $fillable = [
        'id', 'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'pallet_id', 'total_cost', 'unknown_list', 'asin', 'GLDesc', 'unit_recovery', 'total_recovery', 'recovery_rate', 'removal_reason','lqin','file_name'
    ];
}
