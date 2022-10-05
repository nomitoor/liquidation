<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LPN extends Model
{
    use HasFactory;
    protected $table ='lpn';
    protected $fillable = [
        'lpn',
        'bol',
        'asin',
        'package_id',
        'file_name',
    ];
}
