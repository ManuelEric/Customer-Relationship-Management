<?php

namespace App\Exports;

use App\Models\UserClient;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class StudentTemplate implements FromCollection, WithHeadings, WithEvents, WithStrictNullComparison
{

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // store the results for later use
        return UserClient::select(DB::raw('CONCAT(first_name, " ", COALESCE(last_name)) as full_name'))->limit(1)->get();

    }

    public function headings(): array
    {
        $columns = [
            'Full Name',
            'Phone Number',
            'Parents Name',
            'Parents Phone',
            'School',
            'Graduation Year',
            'Grade',
            'Instagram',
            'Location',
            'Lead',
            'Level of Interest',
            'Interested Program',
            'Year of Study Abroad',
            'Country of Study Abroad',
            'University Destination',
            'Interest Major',
            'Status',
        ];

        return $columns;
    }

    public function registerEvents(): array
    {
        return[
            // handle by a closure.
            AfterSheet::class => function(AfterSheet $event) {

                // get layout counts (add 1 to rows for heading row)
                $row_count = 2;
                $column_count = 1;

                // set dropdown options
                $fullname_options = UserClient::limit(10)->get()->pluck('full_name')->toArray();

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("A2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST );
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a value from the drop-down list.');
                $validation->setFormula1(sprintf('"%s"',implode(',',$fullname_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("B2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST );
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a value from the drop-down list.');
                $validation->setFormula1(sprintf('"%s"',implode(',',$lastname_options)));

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

}
