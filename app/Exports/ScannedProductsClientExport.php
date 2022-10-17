<?php
namespace App\Exports;
use App\Models\ScannedProducts;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

class ScannedProductsClientExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths
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
        return ScannedProducts::where('pallet_id', $this->id);
    }

    // here you select the row that you want in the file
    public function map($row): array
    {
        $fields = [
            $row->asin,
            'https://www.amazon.de/dp/' . $row->asin,
            $row->GLDesc,
            $row->units,
            $row->unit_cost,
            $row->total_cost,
            $row->item_description,

        ];
        return $fields;
    }

    public function headings(): array
    {
        return ['ASIN', 'LINK', 'ITEM DESCRIPTION', 'UNITS', 'UNIT COST', 'TOTAL COST', 'GL Description'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 50,
            'E' => 20,
        ];
    }

    public function custom()
    {
        \Excel::extend(static::class, function (ScannedProductsClientExport $export, Sheet $sheet) {
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
