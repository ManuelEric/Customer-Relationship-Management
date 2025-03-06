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
                $main_program = 'Academic & Test Preparation';
                $sub_program = ['Academic Tutoring', 'Subject Tutoring'];
                break;

            case "admissions":
                $main_program = 'Admissions Mentoring';
                $sub_program = 'all';
                break;
        }

        if ( (! $main_program ) && (! $sub_program) )
        {
            throw new HttpResponseException(
                response()->json([
                    'errors' => 'Invalid program'
                ])
            );
        }

        return [$main_program, $sub_program];
    }
}