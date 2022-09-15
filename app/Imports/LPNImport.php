<?php

namespace App\Imports;

use App\Models\DailyManifest;
use App\Models\Manifest;
use App\Models\ManifestCompare;
use App\Models\ScannedProducts;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LPNImport implements ToModel, WithStartRow
{

    public function  __construct($filename)
    {
        $this->filename = $filename;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        Manifest::where('bol', $row[7])->update(['lpn' => $row[6]]);
        DailyManifest::where('bol', $row[7])->update(['lpn' => $row[6]]);
    }
}
