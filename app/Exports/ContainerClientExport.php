<?php

namespace App\Exports;

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
    protected $asin = '';

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
        $pallets = $this->container->with('pallets')->first();
        $pallet_ids = $pallets->pallets->pluck('id')->toArray();
        foreach ($pallet_ids as $pallet_id) {
            $this->pallet_ids[] = 'DE' . sprintf("%05d", $pallet_id);
        }

        return ScannedProducts::whereIn('pallet_id', $pallet_ids);
    }

    // here you select the row that you want in the file
    public function map($row): array
    {
        $fields = [
            $row->asin,
            'AMAZON',
            $row->item_description,
            $row->units,
            $row->unit_cost,
            $row->total_cost,
            'DE' . sprintf("%05d", Pallets::where('id', $row->pallet_id)->pluck('id')->first())
        ];

        $this->asin = $row->asin;

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
                foreach ($row->getCellIterator() as $cell) {
                    if (str_contains($cell->getValue(), 'AMAZON')) {
                        $cell->setHyperlink(new Hyperlink('https://www.amazon.de/dp/' . $this->asin));
                    }
                }
            }
        }, AfterSheet::class);
    }
}
