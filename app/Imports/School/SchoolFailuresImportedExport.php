<?php

namespace App\Imports\School;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SchoolFailuresImportedExport implements FromArray,
    ShouldAutoSize,
    WithHeadings,
    ShouldQueue
{
    use Exportable;

    private int $row_count;
    protected Array $failures;

    public function __construct(Collection $failures)
    {
        // Log::debug(json_encode($failures));
        $getAllFailures = $failures->map(function ($item) {
            return ['row' => $item->row(), 'attribute' => $item->attribute(), 'errors' => $item->errors()];
        })->groupBy('row');
        // $errors = [];
        $errors2 = [];
        $collection = new Collection();
        foreach ($getAllFailures->toArray() as $row => $error) {
            foreach ($error as $e) {
                $errors2[] = [
                    'row' => $e['row'],
                    'error' => $e['errors'][0]
                ];

                // $errors[$row][] = str_replace($e['attribute'], trans("validation.attributes.{$e['attribute']}"), $e['errors'][0]);
            }
            // $collection->push((object)[$row, implode("\r\n", $errors[$row])]);
        }
        // $this->row_count = $collection->count() + 1;
        $this->failures = $errors2;
        Log::debug(json_encode($errors2));
        



    }

    public function array(): Array
    {

        return $this->failures;

    }




    public function headings(): array
    {
        return [
            'row',
            'errors'
        ];
    }
}