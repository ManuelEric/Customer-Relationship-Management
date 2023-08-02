<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\SubProg;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;



class DataPotentialClient implements FromArray, WithHeadings, WithTitle, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    public function headings(): array
    {
        $columns = [
            'No',
            'Name',
            'School',
            'Graduation Year',
            'Contry Destination',
            'Spesific Concern',
            'Phone Number',
            'Client Status',
            'Status', # default 'Qualified'
            '$ Fund', # default 'No'
            'Lead Source', # Referral / Other
            'Register As', # default student
            'Major', # decided / undecided
            'Already Joined', # default '-'
            'Is there any upcoming seasonal program?', # default 'no'
            'Seasonal Program', # default '-'
            'Month Year', # Format (Y-m) value now()
            'Lead From' # default sales
        ];

        return $columns;
    }

    public function array(): array
    {
        $clients =
            Client::whereHas('clientProgram', function ($subQuery) {
                $subQuery->whereIn('status', [0, 2, 3]); # because refund and cancel still marked as potential client
            })->whereDoesntHave('clientProgram', function ($subQuery) {
                $subQuery->where('status', 1);
            })->whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->orderBy('created_at', 'desc')->get();


        $arrClient = [];

        foreach ($clients as $key => $client) {

            if (isset($client->st_grade)) {
                $graduationYear = 12 - ($client->st_grade - date('Y'));
            } else {
                $graduationYear = '-';
            }

            $destinationContries = 'Undecided';
            if ($client->destinationCountries->count() > 0) {

                $sort = $client->destinationCountries->sortByDesc('score')->values()->all();
                $destinationContries = $sort[0]->name;
            }

            $subProg = SubProg::all();

            $spesificConcern = '-';
            if ($client->interestPrograms->count() > 0) {

                $sort = $client->interestPrograms->sortBy('id')->values()->all();
                if ($subProg->where('id', $sort[0]->sub_prog_id)->first() != null) {
                    $spesificConcern = $subProg->where('id', $sort[0]->sub_prog_id)->first()->spesificConcern->count() > 0 ? $subProg->where('id', $sort[0]->sub_prog_id)->first()->spesificConcern->first()->name : '-';
                } else {
                    $spesificConcern = '-';
                }
            }

            $major = 'Undecided';
            if ($client->interestMajor->count() > 0) {

                $major =  'Decided';
            }


            $arrClient[$key] = [
                $key + 1,
                $client->first_name . ' ' . $client->last_name,
                !isset($client->school) ? '-' : (isset($client->school->sch_type) ? $client->school->sch_type : '-'),
                $graduationYear,
                $destinationContries,
                $spesificConcern,
                $client->phone,
                'New',
                $client->phone == null || $client->phone == '' ? 'Not Qualified' : 'Qualified',
                'No',
                $client->lead == null ? 'Other' : ($client->lead->main_lead == 'Referral' ? 'Referral' : 'Other'),
                'Student',
                $major,
                '-',
                'No',
                '-',
                Carbon::now()->format('Y-m'),
                'Sales'
            ];
        }

        return $arrClient;
    }

    public function title(): string
    {
        return 'Potential Client';
    }

    public function columnFormats(): array
    {
        return [
            'G' => '+#',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:R1')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'D9D9D9'],]);
        $sheet->getStyle('A1:R1')->getFont()->setSize(14);
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}
