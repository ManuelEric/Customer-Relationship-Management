<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtClientProgramController extends Controller
{
    public function getSuccessPrograms(Request $request, $authorization = null): JsonResponse
    {

        $programs = \App\Models\ClientProgram::
        with([
            'client' => function ($query) {
                $query->select('id', 'sch_id', 'first_name', 'last_name');
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

        $mappedPrograms = $programs->map(function ($data) {

            $clientprog_id = $data->clientprog_id;
            $invoice_id = $data->invoice->inv_id;
            $program_name = $data->program->program_name;
            $client_id = $data->client->id;
            $client_fname = $data->client->first_name;
            $client_lname = $data->client->last_name;
            $school_name = $data->client->school ? $data->client->school->sch_name : null;

            return [
                'clientprog_id' => $clientprog_id,
                'invoice_id' => $invoice_id,
                'program_name' => $program_name,
                'client' => [
                    'id' => $client_id,
                    'first_name' => $client_fname,
                    'last_name' => $client_lname,
                    'school_name' => $school_name,
                ]
            ];
        });

        return response()->json($mappedPrograms);
    }
}
