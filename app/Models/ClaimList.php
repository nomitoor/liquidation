<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimList extends Model
{
    use HasFactory;

    protected $table = 'claim_list';

    protected $fillable = [
        'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost', 'claim_desription'
    ];
}
