<?php

namespace App\Http\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;

trait MainProgramTrait
{
    public function tnGetMainProgramName(String $requested_program): array
    {
        $main_program = false; # default value 
        $sub_program = [];
        switch ($requested_program)
        {
            case "academic":
                $main_program = 'Test Preparation';
                $group_of = 'Tutoring'; 
                $sub_program = 'all';
                break;

            case "admissions":
                $main_program = 'Admissions Mentoring';
                $group_of = 'Admissions';
                $sub_program = 'all';
                break;

            /* new after program name changes */
            case "tutoring":
                $group_of = 'Tutoring';
                $sub_program = 'all';
                break;

            default: 
                throw new HttpResponseException(
                    response()->json([
                        'errors' => 'Invalid program'
                    ])
                );
        }

        if ( (! $main_program ) && (! $sub_program) )
        {
            throw new HttpResponseException(
                response()->json([
                    'errors' => 'Invalid program'
                ])
            );
        }

        return [$group_of, $sub_program];
    }
}
