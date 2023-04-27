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
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParentTemplate implements WithEvents, WithTitle, WithHeadings, WithStyles
{


    public function headings(): array
    {
        $columns = [
            'Full Name',
            'Email',
            'Phone Number',
            'Childrens Name',
            'Instagram',
            'State',
            'City',
            'Address',
            'Lead',
            'Level of Interest',
            'Interested Program',
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
                $validation = $event->sheet->getCell("I2")->getDataValidation();
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
                $validation = $event->sheet->getCell("J2")->getDataValidation();
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

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'D9D9D9'],]);
        $sheet->getStyle('A1:K1')->getFont()->setSize(14);
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
