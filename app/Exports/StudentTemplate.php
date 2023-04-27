<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\School;
use App\Models\UserClient;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplate implements FromCollection, WithHeadings, WithEvents, WithStrictNullComparison, WithTitle, WithStyles
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
            'Email',
            'Phone Number',
            'Parents Name',
            'Parents Phone',
            'School',
            'Graduation Year',
            'Grade',
            'Instagram',
            'State',
            'City',
            'Address',
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
        return [
            // handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {
                $row_count = 2;
                $column_count = 1;

                // set dropdown options
                $parentname_options = UserClient::whereRelation('roles', 'role_name', 'Parent')->get()->pluck('full_name')->toArray();

                $school_options = School::get()->pluck('sch_name')->toArray();

                $lead_options = Lead::get()->pluck('main_lead')->toArray();

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("D2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $parentname_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("F2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $school_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("M2")->getDataValidation();
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

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Student';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:T1')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'D9D9D9'],]);
        $sheet->getStyle('A1:T1')->getFont()->setSize(14);
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
