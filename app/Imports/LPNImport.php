<?php

namespace App\Imports;

use App\Models\DailyManifest;
use App\Models\Manifest;
use App\Models\LPN;
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
        return new LPN([
            'file_name' => $this->filename,
            'lpn' => $row[6],
            'bol' => $row[7],
            'asin' => $row[3],
            'package_id' => $row[2]
        ]);
    }
}
