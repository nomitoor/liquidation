<?php

namespace App\Imports;

use App\Models\Manifest;
use Maatwebsite\Excel\Concerns\ToModel;

class ManifestImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        return new Manifest([
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
        ]);
    }
}
