<?php

namespace App\Exports;

use App\Models\ScannedProducts;
use App\Models\ManifestCompare;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
class ScannedProductsExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths
{

    use Exportable;

    public function __construct($id)
    {
        $this->id = $id;
        $this->custom();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        if (is_array($this->id)) {
            $first_id = array_values($this->id)[0];
            $count = count(array_values($this->id));
            $last_id = array_values($this->id)[$count-1];

            // dd($first_id, $last_id);
            return ManifestCompare::whereBetween('id', [$first_id, $last_id]);
        } else {
            return ScannedProducts::where('pallet_id', $this->id);
        }
    }

    // here you select the row that you want in the file
    public function map($row): array
    {
        $fields = [
            $row->bol,
            'https://www.amazon.de/dp/' . $row->asin,
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
        return ['BOL','LINK', 'PACKAGE ID', 'ITEM DESCRIPTION', 'UNITS', 'UNIT COST', 'TOTAL COST', 'GL DESCRIPTION', 'UNIT RECOVERY', 'TOTAL RECOVERY', 'RECOVERY RATE', 'REMOVAL RATE'];
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

    public function custom()
    {
        \Excel::extend(static::class, function (ScannedProductsExport $export, Sheet $sheet) {
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
