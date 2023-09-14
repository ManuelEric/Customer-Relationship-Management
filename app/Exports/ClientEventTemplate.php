<?php

namespace App\Exports;

use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\School;
use App\Models\UserClient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientEventTemplate implements WithHeadings, WithEvents, WithStrictNullComparison, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        $columns = [
            'Event Name',
            'Date',
            'Audience',
            'Name',
            'Email',
            'Phone Number',
            'Child / Parent Name',
            'Child / Parent Email',
            'Child / Parent Phone Number',
            // 'Existing / New Leads',
            // 'Mentee / Non mentee',
            'Registration Type',
            'School',
            'Class Of', # Grade / Expected graduation year
            'Lead',
            // 'Event',
            'Partner',
            'Edufair',
            'KOL',
            'Itended Major',
            'Destination Country',
            'Number Of Attend',
            'Referral Code',
            'Notes',
            'Reason Join', # Why do you want to join this program / event ?
            'Expectation Join', # What do you expect to gain & learn from the event / program ?
            'Status', # What do you expect to gain & learn from the event / program ?
        ];

        return $columns;
    }

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:X1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);

                // get layout counts (add 1 to rows for heading row)
                $row_count = 2;
                $column_count = 15;

                $event_options = Event::get()->pluck('event_title')->toArray();

                $lead_options = Lead::get()->pluck('main_lead')->toArray();

                $partner_options = Corporate::get()->pluck('corp_name')->toArray();

                $eduf_options = EdufLead::get()->pluck('organizerName')->toArray();

                $kol_options = Lead::where('main_lead', 'KOL')->get()->pluck('sub_lead')->toArray();

                $school_options = School::get()->pluck('sch_name')->toArray();

                $lead_exist_options = ['Existing', 'New'];

                $mentee_exist_options = ['Mentee', 'Non-mentee'];

                $audience_options = ['Student', 'Parent', 'Teacher'];
                
                $status_options = ['Join', 'Attend'];

                $regtype_options = ['PR', 'OTS'];

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("A2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $lead_exist_options)));

                // set dropdown list for first data row
                // $validation = $event->sheet->getCell("E2")->getDataValidation();
                // $validation->setType(DataValidation::TYPE_LIST);
                // $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                // $validation->setAllowBlank(false);
                // $validation->setShowInputMessage(true);
                // $validation->setShowErrorMessage(true);
                // $validation->setShowDropDown(true);
                // $validation->setErrorTitle('Input error');
                // $validation->setError('Value is not in list.');
                // $validation->setPromptTitle('Pick from list');
                // $validation->setPrompt('Please pick a value from the drop-down list.');
                // $validation->setFormula1(sprintf('"%s"', implode(',', $mentee_exist_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("C2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $audience_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $regtype_options)));

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


                // set dropdown list for first data row
                // $validation = $event->sheet->getCell("L2")->getDataValidation();
                // $validation->setType(DataValidation::TYPE_LIST);
                // $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                // $validation->setAllowBlank(false);
                // $validation->setShowInputMessage(true);
                // $validation->setShowErrorMessage(true);
                // $validation->setShowDropDown(true);
                // $validation->setErrorTitle('Input error');
                // $validation->setError('Value is not in list.');
                // $validation->setPromptTitle('Pick from list');
                // $validation->setPrompt('Please pick a value from the drop-down list.');
                // $validation->setFormula1(sprintf('"%s"', implode(',', $event_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $partner_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $eduf_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $kol_options)));
                
                // set dropdown list for first data row
                $validation = $event->sheet->getCell("X2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $status_options)));

                $event->sheet->getDelegate()->getComment('A1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('B1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('C1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('D1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('E1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('F1')->getText()->createTextRun('Required');
                // $event->sheet->getDelegate()->getComment('G1')->getText()->createTextRun('Required');
                // $event->sheet->getDelegate()->getComment('H1')->getText()->createTextRun('Required');
                // $event->sheet->getDelegate()->getComment('I1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('K1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('L1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('M1')->getText()->createTextRun('Required');
                $event->sheet->getDelegate()->getComment('N1')->getText()->createTextRun('Required if lead source All-In Partners');
                $event->sheet->getDelegate()->getComment('O1')->getText()->createTextRun('Required if lead source External Edufair');
                $event->sheet->getDelegate()->getComment('P1')->getText()->createTextRun('Required if lead source KOL');
                $event->sheet->getDelegate()->getComment('X1')->getText()->createTextRun('Required');

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:X1')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'D9D9D9'],]);
        $sheet->getStyle('A1:X1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('B1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('C1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('D1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('E1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('F1')->getFont()->getColor()->setARGB('FF0000');
        // $sheet->getStyle('G1')->getFont()->getColor()->setARGB('FF0000');
        // $sheet->getStyle('H1')->getFont()->getColor()->setARGB('FF0000');
        // $sheet->getStyle('I1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('K1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('L1')->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('M1')->getFont()->getColor()->setARGB('FF0000');
        // $sheet->getStyle('L1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('N1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('O1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('P1')->getFont()->getColor()->setARGB('f0a318');
        $sheet->getStyle('X1')->getFont()->getColor()->setARGB('FF0000');
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
