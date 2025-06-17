<?php

namespace App\Exports;

use App\Http\Traits\MentorTypeTrait;
use App\Models\UserClient;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GraduatedMenteeGlobalExport implements FromCollection, WithHeadings, WithStyles
{
    use MentorTypeTrait;

    protected $graduated_mentees;

    public function __construct($graduated_mentees)
    {
        $this->graduated_mentees = $graduated_mentees;
    }

    public function headings(): array
    {
        return [
            'No',
            'Full Name',
            'University Name',
            'Major Group',
            'Major',
            'Application Year',
            'Mentor Type',
            'Profile Building Mentor',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Make first row (headings) bold
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $collections = [];
        # loop through graduated mentees
        # and map the data to the collection
        # we use $index to get the number of the mentee
        # and $single to get the mentee data
        foreach ($this->graduated_mentees as $index => $single)
        {
            $have_university_acceptance = count($single->universityAcceptance) > 0 ? true : false;

            # actually, there will be more than 1 university acceptance
            # but we only need the last one
            # so we take the last one by using count($single->universityAcceptance)-1
            $university_acceptance = $have_university_acceptance ? $single->universityAcceptance[count($single->universityAcceptance)-1] : null;
            $university_name = $have_university_acceptance ? $university_acceptance->univ_name : null;
            $major_group = $have_university_acceptance && $university_acceptance->pivot->major_group_id !== NULL ? $university_acceptance->pivot->major_group->mg_name : null;
            $major = $have_university_acceptance ? $university_acceptance->pivot->get_major_name : null;

            
            # determine which type of mentor does the user has
            $latest_admission = $single->clientProgram[0];
            # with orderByPivot, it helps get the latest record 
            $select_profile_building_mentor = $latest_admission->clientMentor()->first()?->full_name ?? null;
            // $logged_in_mentor_type = $latest_admission->clientMentor()->where('users.id', Auth::guard('api')->user()->id)->orderByPivot('id', 'desc')->get();
            $mapped_mentor_type = $this->tnDefineMentorType(1);
            

            $collections[] = [
                'no' => $index + 1,
                'full_name' => $single->full_name,
                'university_name' => $university_name,
                'major_group' => $major_group,
                'major' => $major,
                'application_year' => $single->application_year,
                'mentor' => $mapped_mentor_type,
                'profile_building_mentor' => $select_profile_building_mentor,
            ];
        }

        return collect($collections);
    }
}
