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
            return [
                'clientprog_id' => $data->clientprog_id,
                'invoice_id' => $data->invoice->inv_id,
                'program_name' => $data->program->program_name,
                'client' => [
                    'id' => $data->client->id,
                    'first_name' => $data->client->first_name,
                    'last_name' => $data->client->last_name,
                    'school_name' => $data->client->school->sch_name,
                ]
            ];
        });

        return response()->json($mappedPrograms);
    }
}
