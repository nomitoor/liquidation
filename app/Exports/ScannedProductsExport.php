<?php

namespace App\Exports;

use App\Models\ScannedProducts;
use App\Models\ManifestCompare;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ScannedProductsExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths
{

    use Exportable;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        if (is_array($this->id)) {
            return ManifestCompare::whereIn('id', array_values($this->id));
        } else {
            return ScannedProducts::where('pallet_id', $this->id);
        }
    }

    // here you select the row that you want in the file
    public function map($row): array
    {
        $fields = [
            $row->bol,
            $row->package_id,
            $row->item_description,
            $row->units,
            $row->unit_cost,
            $row->total_cost,
            $row->GLDesc,
            $row->unit_recovery,
            $row->total_recovery,
            $row->recovery_rate,
            $row->removal_reason

        ];
        return $fields;
    }

    public function headings(): array
    {
        return ['BOL', 'PACKAGE ID', 'ITEM DESCRIPTION', 'UNITS', 'UNIT COST', 'TOTAL COST', 'GL DESCRIPTION', 'UNIT RECOVERY', 'TOTAL RECOVERY', 'RECOVERY RATE', 'REMOVAL RATE'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 50,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20
        ];
    }
}
