<?php

namespace App\Exports;

use App\Models\Program;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProgramList implements FromArray, WithHeadings, WithTitle, WithStyles
{
    use Exportable;

    public function headings(): array
    {
        $columns = [
            'Program Name',
        ];

        return $columns;
    }

    public function array(): array
    {
        $programs =  Program::all();
        $arrProgram = [];
        foreach ($programs as $key => $program) {
            $arrProgram[$key] = [
                $program->programName
            ];
        }

        return $arrProgram;
    }

    public function title(): string
    {
        return 'Program List';
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'D9D9D9'],]);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
