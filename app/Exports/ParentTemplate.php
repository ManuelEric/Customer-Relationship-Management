<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\Lead;
use App\Models\UserClient;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParentTemplate implements WithEvents, WithTitle, WithHeadings
{


    public function headings(): array
    {
        $columns = [
            'First Name',
            'Last Name',
            'Email',
            'Phone Number',
            'Date of Birth',
            'Instagram',
            'State',
            'City',
            'Postal Code',
            'Address',
            'Lead Source',
            'Level of Interest',
            'Interest Program',
            'Childrens Name'
        ];

        return $columns;
    }

    public function title(): string
    {
        return 'Parent';
    }

    public function registerEvents(): array
    {

        //$event = $this->getEvent();
        return [
            AfterSheet::class => function (AfterSheet $event) {


                // get layout counts (add 1 to rows for heading row)
                $row_count = 2;
                $column_count = 14;

                $lead_options = Lead::get()->pluck('main_lead')->toArray();
                $levelOfInterest_options = [
                    'High',
                    'Medium',
                    'Low',
                ];

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("K2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a value from the drop-down list.');
                $validation->setFormula1(sprintf('"%s"', implode(',', $lead_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("L2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a value from the drop-down list.');
                $validation->setFormula1(sprintf('"%s"', implode(',', $levelOfInterest_options)));

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }
}
