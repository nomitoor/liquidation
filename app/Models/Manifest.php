<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    use HasFactory;

    protected $fillable = [
        'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost', 'asin', 'GLDesc', 'unit_recovery', 'total_recovery', 'recovery_rate', 'removal_reason', 'filename','lpn','lqin'
    ];
}
