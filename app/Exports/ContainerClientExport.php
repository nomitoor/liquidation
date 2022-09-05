<?php

namespace App\Exports;

use App\Models\Container;
use App\Models\Pallets;
use App\Models\ScannedProducts;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

class ContainerClientExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths
{

    use Exportable;
    protected $pallet_ids = [];

    public function __construct($container)
    {
        $this->container = $container;
        $this->custom();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $pallet_id = $this->container->id;
        $pallets = Container::whereHas('pallets', function ($query) use ($pallet_id) {
            $query->where('container_id', $pallet_id);
        })->first();
        $pallet_ids = [];
        if (!is_null($pallets)) {
            $pallet_ids = $pallets->pallets->pluck('id')->toArray();
            foreach ($pallet_ids as $pallet_id) {
                $this->pallet_ids[] = 'DE' . sprintf("%05d", $pallet_id);
            }
        }

        return ScannedProducts::whereIn('pallet_id', $pallet_ids);
    }

    // here you select the row that you want in the file
    public function map($row): array
    {
        $fields = [
            $row->asin,
            'https://www.amazon.de/dp/' . $row->asin,
            $row->item_description,
            $row->units,
            $row->unit_cost,
            $row->total_cost,
            'DE' . sprintf("%05d", Pallets::where('id', $row->pallet_id)->pluck('id')->first())
        ];

        return $fields;
    }

    public function headings(): array
    {
        return ['ASIN', 'LINK', 'ITEM DESCRIPTION', 'UNITS', 'UNIT COST', 'TOTAL COST', 'PALLET ID'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 10,
            'C' => 20,
            'D' => 50,
            'E' => 20,
            'F' => 20,
            'G' => 50,
        ];
    }

    public function custom()
    {
        \Excel::extend(static::class, function (ContainerClientExport $export, Sheet $sheet) {
            foreach ($sheet->getColumnIterator('B') as $row) {
                foreach ($row->getCellIterator() as $key => $cell) {
                    if (str_contains($cell->getValue(), '://')) {
                        $cell->setHyperlink(new Hyperlink($cell->getValue()));
                        $cell->setValue('AMAZON');
                    }
                }
            }
        }, AfterSheet::class);
    }
}
