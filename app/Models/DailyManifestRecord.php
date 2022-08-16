<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyManifestRecord extends Model
{
    use HasFactory;
    protected $table = 'daily_manifest_records';

    protected $fillable = [
        'file_name', 'number_of_entities', 'uploaded_by'
    ];
}
