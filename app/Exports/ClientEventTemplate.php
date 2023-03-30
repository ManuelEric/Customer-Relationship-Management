<?php

namespace App\Exports;

use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\UserClient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClientEventTemplate implements FromCollection, WithHeadings, WithEvents, WithStrictNullComparison, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        return UserClient::select(DB::raw('CONCAT(first_name, " ", COALESCE(last_name)) as full_name'))->limit(1)->get();
    }

    public function headings(): array
    {
        $columns = [
            'Client Name',
            'Event Name',
            'Conversion Lead',
            'Partner Name',
            'Edufair Name',
            'KOL Name',
            'Joined Date',
            'Status',
        ];

        return $columns;
    }

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {

                // get layout counts (add 1 to rows for heading row)
                $row_count = 2;
                $column_count = 9;

                // set dropdown options
                $fullname_options = UserClient::get()->pluck('full_name')->toArray();
                $event_options = Event::get()->pluck('event_title')->toArray();
                $lead_options = Lead::where('main_lead', '!=', 'KOL')->get()->pluck('main_lead')->toArray();
                $partner_options = Corporate::get()->pluck('corp_name')->toArray();
                $exteduf_options = EdufLead::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_eduf_lead.corp_id')
                    ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_eduf_lead.sch_id')
                    ->select([
                        DB::raw("(CASE WHEN tbl_eduf_lead.title = null OR tbl_eduf_lead.title = '' THEN
                                        (CASE WHEN tbl_eduf_lead.corp_id is not null THEN
                                            CONCAT(tbl_corp.corp_name, ' (', tbl_eduf_lead.event_start, ')')
                                        ELSE
                                            CONCAT(tbl_sch.sch_name, ' (', tbl_eduf_lead.event_start, ')')
                                        END)
                                ELSE
                                        tbl_eduf_lead.title
                                END) as organizer_name"),
                    ])
                    ->orderBy('organizer_name', 'asc')->get()->pluck('organizer_name')
                    ->toArray();
                $kol_options = Lead::where('main_lead', 'KOL')->orderBy('sub_lead', 'asc')->get()->pluck('sub_lead')->toArray();
                $status_options = [
                    'Join', 'Attend'
                ];

                array_push($lead_options, 'KOL');

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $fullname_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("B2")->getDataValidation();
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
                $validation->setFormula1(sprintf('"%s"', implode(',', $lead_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $partner_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $exteduf_options)));

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
                $validation->setFormula1(sprintf('"%s"', implode(',', $kol_options)));

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("H2")->getDataValidation();
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
            'D' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
