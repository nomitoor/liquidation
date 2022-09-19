<?php

namespace App\Imports;

use App\Models\DailyManifest;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
class DailyManifestImport implements ToModel, WithStartRow
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
        return new DailyManifest([
            'filename' => $this->filename,
            'removal_reason' => $row[4],
            'GLDesc' => $row[15],
            'asin' => $row[20],
            'recovery_rate' => $row[35],
            'unit_recovery' => $row[32],
            'total_recovery' => $row[34],
            'bol' => $row[6],
            'package_id' => $row[13],
            'item_description' => $row[24],
            'units' => $row[25],
            'unit_cost' => $row[30],
            'total_cost' => $row[33],
            'lpn' => '',
        ]);
    }
}
