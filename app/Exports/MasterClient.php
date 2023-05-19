<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MasterClient implements WithMultipleSheets
{
    protected $type;

    public function __construct(array $type)
    {
        $this->type = $type;
    }

    public function array(): array
    {
        return $this->type;
    }

    public function sheets(): array
    {
        switch ($this->type[0]) {
            case 'student':
                $sheets = [
                    new StudentTemplate('Student'),
                    new ProgramList('Program List'),
                ];
                break;

            case 'parent':
                $sheets = [
                    new ParentTemplate('Parent'),
                    new ProgramList('Program List'),
                ];
                break;
        }

        return $sheets;
    }
}
