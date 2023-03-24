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

class ParentTemplate implements FromCollection, WithEvents, WithTitle
{


    protected $headers;

    // public function __construct(ClientRepositoryInterface $clientRepository)
    // {
    //     $this->clientRepository = $clientRepository;
    // }

    public function collection()
    {
        $this->headers = collect([[
            'First Name',
            'Last Name',
            'E-mail',
            'Phone Number',
            'Date of Birth',
            'Instagram',
            'State/Region',
            'City',
            'Postal Code',
            'Address',
            'Lead Source',
            'Level of Interest',
            'Interested Program',
            'Children Name',
        ]]);

        // $this->headers = $this->getActionItems();

        return $this->headers;
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
                $column_count = 1;

                // set dropdown column
                $drop_column = [
                    'N',
                    'K',
                ];

                // set dropdown options
                // foreach ($childrens as $children) {
                $options = UserClient::whereHas(
                    'roles',
                    function ($query2) {
                        $query2->where('role_name', 'student');
                    }
                )->limit(10)->get()->pluck('fullName')->toArray();


                /**
                 * validation for bulkuploadsheet
                 */

                $objValidation = $event->sheet->getCell("{$drop_column[0]}2")->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1(sprintf('"%s"', implode(',', $options)));

                // clone validation to remaining rows
                for ($i = 3; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$drop_column[0]}{$i}")->setDataValidation(clone $objValidation);
                }

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }
}
