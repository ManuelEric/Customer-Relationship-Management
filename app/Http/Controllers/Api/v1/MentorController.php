<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        //! not finished

        $request->validate([
            'degree' => 'required',
            'univ_id' => 'required_if:other_univ_name,null',
            'other_univ_name' => 'string|required_if:univ_id,null',
            'major_id' => 'required_if:other_major_name,null',
            'other_major_name' => 'required_if:major_id,null',
            'graduation_date' => 'nullable',
        ]);

        $validated = $request->only([
            'degree',
            'univ_id',
            'other_univ_name',
            'major_id',
            'other_major_name',
            'graduation_date'
        ]);

        DB::beginTransaction();
        try {
            $user = User::find($request->user()->id);
            $user->educations()->attach($validated);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

        }
    }
}
