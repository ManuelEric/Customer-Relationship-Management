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
                    select('id', 'uuid', 'sch_id', 'first_name', 'last_name')->
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
            }
        ])->successAndPaid()->select('clientprog_id', 'prog_id', 'client_id')->get();

        $mappedB2CPrograms = $b2cPrograms->map(function ($data) {

            $clientprog_id = $data->clientprog_id;
            $invoice_id = $data->invoice->inv_id;
            $program_name = $data->program->program_name;
            $require = $data->program->main_prog->id == 4 ? "Tutor" : "Mentor";
            $client_uuid = $data->client->uuid;
            $client_fname = $data->client->first_name;
            $client_lname = $data->client->last_name;
            $client_grade = $data->client->grade;
            $school_name = $data->client->school ? $data->client->school->sch_name : null;

            return [
                'category' => 'b2c',
                'clientprog_id' => $clientprog_id,
                'invoice_id' => $invoice_id,
                'program_name' => $program_name,
                'require' => $require,
                'client' => [
                    'uuid' => $client_uuid,
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
}
