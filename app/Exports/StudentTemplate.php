<?php

namespace App\Exports;

use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\School;
use App\Models\UserClient;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PDO;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplate implements FromCollection, WithHeadings, WithEvents, WithStrictNullComparison, WithTitle, WithStyles, WithColumnFormatting
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
            'Date of Birth',
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
            'Event',
            'Partner',
            'Edufair',
            'KOL',
            'Level of Interest',
            'Interested Program',
            'Year of Study Abroad',
            'Country of Study Abroad',
            'Interest Major',
            'Joined Date'
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

                $event_options = Event::get()->pluck('event_title')->toArray();

                $partner_options = Corporate::get()->pluck('corp_name')->toArray();

                $eduf_options = EdufLead::get()->pluck('organizerName')->toArray();

                $kol_options = Lead::where('main_lead', 'KOL')->get()->pluck('sub_lead')->toArray();

                $levelOfInterest_options = [
                    'High',
                    'Medium',
                    'Low',
                ];

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("E2")->getDataValidation();
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
                $validation = $event->sheet->getCell("G2")->getDataValidation();
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
                $validation = $event->sheet->getCell("N2")->getDataValidation();
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
                $validation = $event->sheet->getCell("O2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $event_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("P2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $partner_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("Q2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $eduf_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("R2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $kol_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("S2")->getDataValidation();
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

                $event->sheet->getDelegate()->getComment('A1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('B1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('C1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('G1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('I1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('N1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('O1')->getText()->createTextRun('Required if lead source All-In Event');
                $event->sheet->getDelegate()->getComment('P1')->getText()->createTextRun('Required if lead source All-In Partners');
                $event->sheet->getDelegate()->getComment('Q1')->getText()->createTextRun('Required if lead source External Edufair');
                $event->sheet->getDelegate()->getComment('R1')->getText()->createTextRun('Required if lead source KOL');
                $event->sheet->getDelegate()->getComment('T1')->getText()->createTextRun('Not Required, you can input more than 1 interest program with separator comma and space (, ) example : Admissions Mentoring : Pay As You Go, Events & Info Sessions : Education Fair');
                $event->sheet->getDelegate()->getComment('W1')->getText()->createTextRun('Not Required, you can input more than 1 interest major with separator comma and space (, ) example : Accounting, Agribusiness');

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

    public function columnFormats(): array
    {
        return [
            'X' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:X1')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'D9D9D9'],]);
        $sheet->getStyle('A1:X1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('B1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('C1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('G1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('I1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('N1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('O1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('P1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('Q1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('R1')->getFont()->getColor()->setARGB('f0a318');
        // $sheet->setStyle('A1')
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
