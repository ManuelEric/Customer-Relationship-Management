<?php

namespace App\Exports;

use App\Models\UserClient;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActiveMenteeGlobalExport implements FromCollection, WithHeadings
{
    protected $active_mentees;

    public function __construct($active_mentees)
    {
        $this->active_mentees = $active_mentees;
    }

    public function headings(): array
    {
        return [
            'No',
            'Full Name',
            'School Name',
            'Grade',
            'Application Year',
            'Mentoring Progress Status',
            'Joining Year',
            'Program Name',
            'Free Trial',
            'Package',
            'Curriculum',
            'Profile Building Mentor',
            'Registered At'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        foreach ($this->active_mentees as $index => $single)
        {
            // # determine which type of mentor does the user has
            $latest_admission = $single->clientProgram->first(); 
            // # with orderByPivot, it helps get the latest record 
            $select_profile_building_mentor = $latest_admission->clientMentor()->first()?->full_name ?? null;    

            $collections[] = [
                'no' => $index + 1,
                'full_name' => $single->full_name,
                'sch_name' => $single->school->sch_name ?? null,
                'grade' => $single->grade_now,
                'application_year' => $single->application_year,
                'mentoring_progress_status' => $single->mentoring_progress_status,
                'joining_year' => Carbon::parse($single->clientProgram()->whereRelation('program.main_prog', 'prog_name', 'Admissions Mentoring')->latest()->first()->success_date)->format('Y'),
                'program_name' => $latest_admission->invoice_program_name,
                'free_trial' => preg_match("/free trial/i", $latest_admission->package),
                'package' => $latest_admission->package,
                'curriculum' => $latest_admission->curriculum,
                'profile_building_mentor' => $select_profile_building_mentor,
                'registered_at' => Carbon::parse($single->created_at)->format('Y-m-d')
            ];
        };
        return collect($collections);
    }
}
