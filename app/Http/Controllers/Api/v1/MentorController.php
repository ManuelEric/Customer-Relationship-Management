<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    public function fnGetEducation(Request $request)
    {
        $educations = $request->user()->educations;
        $mapped_educations = $educations->map(function($item) {
            return [
                'university' => $item->univ_name,
                'major' => $item->major_name,
                'degree' => $item->pivot->degree,
                'graduated_at' => $item->pivot->graduation_date
            ];
        });

        return response()->json($mapped_educations);
    }

    public function fnStoreEducation(Request $request)
    {
        
    }
}
