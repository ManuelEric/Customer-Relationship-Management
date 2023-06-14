<?php

namespace App\Exports;

use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\School;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeacherTemplate implements WithEvents, WithTitle, WithHeadings, WithStyles
{

    public function headings(): array
    {
        $columns = [
            'Full Name',
            'Email',
            'Phone Number',
            'Date of Birth',
            'School',
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
        ];

        return $columns;
    }

    public function title(): string
    {
        return 'Teacher';
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
                $school_options = School::get()->pluck('sch_name')->toArray();

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $school_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $levelOfInterest_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $event_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $partner_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $eduf_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $kol_options)));

                $event->sheet->getDelegate()->getComment('A1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('B1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('C1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('E1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('J1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('K1')->getText()->createTextRun('Required if lead source All-In Event');
                $event->sheet->getDelegate()->getComment('L1')->getText()->createTextRun('Required if lead source All-In Partners');
                $event->sheet->getDelegate()->getComment('M1')->getText()->createTextRun('Required if lead source External Edufair');
                $event->sheet->getDelegate()->getComment('N1')->getText()->createTextRun('Required if lead source KOL');

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
        $sheet->getStyle('A1:O1')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'D9D9D9'],]);
        $sheet->getStyle('A1:O1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('B1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('C1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('E1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('J1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('K1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('L1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('M1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('N1')->getFont()->getColor()->setARGB('f0a318');
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
