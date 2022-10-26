<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class NewAdminExports implements FromQuery, WithMapping, WithHeadings, WithColumnWidths
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
            return ManifestCompare::whereIn('bol', array_values($this->id));
        } else {
            // return ScannedProducts::where('pallet_id', $this->id);
            // $all_bol_ids = Pallets::where('id', $this->id)->get(['bol_ids']);
            // $bol_ids = unserialize($all_bol_ids[0]->bol_ids);
            // $data = ScannedProducts::whereIn('bol', $bol_ids)->orWhereIn('package_id', $bol_ids )->orWhereIn('lqin',$bol_ids);;
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
            $row->removal_reason,
            $row->asin,

        ];
        return $fields;
    }

    public function headings(): array
    {
        return ['BOL','LINK', 'PACKAGE ID', 'ITEM DESCRIPTION', 'UNITS', 'UNIT COST', 'TOTAL COST', 'GL DESCRIPTION', 'UNIT RECOVERY', 'TOTAL RECOVERY', 'RECOVERY RATE', 'REMOVAL RATE', 'ASIN'];
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
            'K' => 20,
            'L' => 20,
        ];
    }

    public function custom()
    {
        \Excel::extend(static::class, function (NewAdminExports $export, Sheet $sheet) {
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
