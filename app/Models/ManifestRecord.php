<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManifestRecord extends Model
{
    use HasFactory;

    protected $table = 'manifest_upload_record';

    protected $fillable = [
        'file_name', 'number_of_entities', 'uploaded_by'
    ];
}
