<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtClientProgramController extends Controller
{
    public function getSuccessPrograms(Request $request, $authorization = null): JsonResponse
    {

        $b2cPrograms = \App\Models\ClientProgram::
        with([
            'client' => function ($query) {
                $query->
                    select('id', 'sch_id', 'first_name', 'last_name', 'grade_now');
                    // selectRaw('UpdateGradeStudent (year(CURDATE()),year(created_at),month(CURDATE()),month(created_at),st_grade) as grade');
            },
            'client.school' => function ($query) {
                $query->select('sch_id', 'sch_name');
            },
            'invoice' => function ($query) {
                $query->select('clientprog_id', 'inv_id');
            },
            'program' => function ($query) {
                $query->select('prog_id', 'main_prog_id', 'prog_program');
            }
        ])->successAndPaid()->select('clientprog_id', 'prog_id', 'client_id')->get();

        $mappedB2CPrograms = $b2cPrograms->map(function ($data) {

            $clientprog_id = $data->clientprog_id;
            $invoice_id = $data->invoice->inv_id;
            $program_name = $data->program->program_name;
            $require = $data->program->main_prog->id == 4 ? "Tutor" : "Mentor";
            $client_id = $data->client->id;
            $client_fname = $data->client->first_name;
            $client_lname = $data->client->last_name;
            $client_grade = $data->client->grade_now;
            $school_name = $data->client->school ? $data->client->school->sch_name : null;

            return [
                'category' => 'b2c',
                'clientprog_id' => $clientprog_id,
                'invoice_id' => $invoice_id,
                'program_name' => $program_name,
                'require' => $require,
                'client' => [
                    'uuid' => $client_id,
                    'first_name' => $client_fname,
                    'last_name' => $client_lname,
                    'school_name' => $school_name,
                    'grade' => $client_grade,
                ]
            ];
        });

        $b2bPrograms = \App\Models\SchoolProgram::
        with([
            'school' => function ($query) {
                $query->select('sch_id', 'sch_name');
            },
            'invoiceB2b' => function ($query) {
                $query->select('schprog_id', 'invb2b_id');
            },
            'program' => function ($query) {
                $query->select('prog_id', 'main_prog_id', 'prog_program');
            }
        ])->success()->programIs('Academic & Test Preparation')->select('tbl_sch_prog.id', 'prog_id', 'sch_id')->get();

        $mappedB2BPrograms = $b2bPrograms->map(function ($data) {

            $schprog_id = $data->id;
            $invoiceb2b_id = $data->invoiceB2b->invb2b_id;
            $program_name = $data->program->program_name;
            $school_name = $data->school->sch_name;

            return [
                'category' => 'b2b',
                'schprog_id' => $schprog_id,
                'invoice_id' => $invoiceb2b_id,
                'program_name' => $program_name,
                'client' => [
                    'school_name' => $school_name,
                ]
            ];
        });
        

        $programs = $mappedB2CPrograms->merge($mappedB2BPrograms);

        return response()->json($programs);
    }

    public function fnGetSuccessEssayProgram()
    {
        $b2cPrograms = \App\Models\ClientProgram::
        with([
            'client' => function ($query) {
                $query->
                    select('id', 'sch_id', 'first_name', 'last_name')->
                    selectRaw('UpdateGradeStudent (year(CURDATE()),year(created_at),month(CURDATE()),month(created_at),st_grade) as grade');
            },
            'client.school' => function ($query) {
                $query->select('sch_id', 'sch_name');
            },
            'invoice' => function ($query) {
                $query->select('clientprog_id', 'inv_id');
            },
            'program' => function ($query) {
                $query->select('prog_id', 'main_prog_id', 'prog_program');
            },
            'clientMentor' => function ($query) {
                $query->
                    select('users.id', 'phone', 'email', 'password', 'active');
            }
        ])->successAndPaid()->select('clientprog_id', 'prog_id', 'client_id')->get();

        $mappedB2CPrograms = $b2cPrograms->map(function ($data) {

            $mentor_pics = [];
            $clientprog_id = $data->clientprog_id;
            $invoice_id = $data->invoice->inv_id;
            $program_name = $data->program->program_name;
            $require = $data->program->main_prog->id == 4 ? "Tutor" : "Mentor";
            $client_id = $data->client->id;
            $client_fname = $data->client->first_name;
            $client_lname = $data->client->last_name;
            $client_grade = $data->client->grade;
            $client_email = $data->client->mail;
            $client_address = $data->client->address;
            $school_name = $data->client->school ? $data->client->school->sch_name : null;

            foreach ($data->clientMentor as $mentor)
            {
                $mentor_pics[] = [
                    'mentor_id' => $mentor->id,
                    'type' => $this->fnGetMentorType($mentor->pivot->type),
                ];
            }

            return [
                'clientprog_id' => $clientprog_id,
                'invoice_id' => $invoice_id,
                'program_name' => $program_name,
                'require' => $require,
                'mentors' => $mentor_pics,
                'client' => [
                    'uuid' => $client_id,
                    'first_name' => $client_fname,
                    'last_name' => $client_lname,
                    'email' => $client_email,
                    'address' => $client_address,
                    'school_name' => $school_name,
                    'grade' => $client_grade,
                ]
            ];
        });

        return $mappedB2CPrograms;
    }

    private function fnGetMentorType(int $type): string
    {
        switch ($type)
        {
            case 1:
                $type_desc = 'Supervising Mentor';
                break;
            case 2:
                $type_desc = 'Profile Building & Exploration Mentor';
                break;
            case 3:
                $type_desc = 'Application Strategy Mentor';
                break;
            case 4:
                $type_desc = 'Writing Mentor';
                break;
            case 5:
                $type_desc = 'Tutor';
                break;
            case 6:
                $type_desc = 'Subject Specialist';
                break;
        }

        return $type_desc;
    }
}