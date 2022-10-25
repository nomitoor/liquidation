<?php
namespace App\Exports;
use App\Models\ScannedProducts;
use App\Models\Pallets;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use Illuminate\Support\Facades\DB;

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
        $all_bol_ids = Pallets::where('id', $this->id)->get(['bol_ids']);
        $bol_ids = unserialize($all_bol_ids[0]->bol_ids);
        $data = ScannedProducts::whereIn('bol', $bol_ids)->orWhereIn('package_id', $bol_ids )->orWhereIn('lqin',$bol_ids );
        //   $data = ScannedProducts::where('pallet_id', $this->id);
   //   $data = DB::table('scanned_products')->whereIn('bol', $bol_ids);

      // dd($data);

       //  dd(0);
        return $data;
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
            $row->GLDesc,

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
