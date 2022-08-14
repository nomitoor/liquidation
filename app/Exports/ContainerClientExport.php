<?php

namespace App\Exports;

use App\Models\ScannedProducts;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ContainerClientExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths
{

    use Exportable;
    protected $pallet_ids = [];

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $pallets = $this->container->with('pallets')->first();
        $pallet_ids = $pallets->pallets->pluck('id')->toArray();
        foreach ($pallet_ids as $pallet_id) {
            $this->pallet_ids[] = 'DE'.sprintf("%05d", $pallet_id);
        }

        return ScannedProducts::whereIn('pallet_id', $pallet_ids);
    }

    // here you select the row that you want in the file
    public function map($row): array
    {
        $fields = [
            $row->asin,
            $row->item_description,
            $row->units,
            $row->unit_cost,
            $row->total_cost,
            $this->pallet_ids
        ];
        return $fields;
    }

    public function headings(): array
    {
        return ['ASIN', 'ITEM DESCRIPTION', 'UNITS', 'UNIT COST', 'TOTAL COST', 'PALLET ID'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 50,
            'E' => 20,
            'F' => 50,
        ];
    }
}
