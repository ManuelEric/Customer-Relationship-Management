<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Http\Request;

class SchoolController extends Controller
{

    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }
    
    public function search(Request $request)
    {
        $terms = $request->search;
        if (empty($terms))
            return response()->json([]);

        // $schools = $this->schoolRepository->getAllSchools();
        // $schools_name = $schools->pluck('sch_name')->toArray();

        // # matching words
        // $matched = $this->levenshtein_distance($terms, $schools_name);
        // return response()->json($matched);

        $schools_found = $this->schoolRepository->findSchoolByTerms($terms);
        if ($schools_found->count() == 0)
            return response()->json([['sch_id' => 'SCH-NEW', 'sch_name' => 'Add new']]);


        # there are multiple options
        # 1. if no schools were found then show the add new option
        # 2. if no schools were found then show suggestion + add new option

        foreach ($schools_found as $school) {

            $formatted[] = [
                'sch_id' => $school->sch_id,
                'sch_name' => $school->sch_name
            ];

        }

        return response()->json($formatted);
    }

    private function levenshtein_distance($input, $arrays)
    {
        // no shortest distance found, yet
        $shortest = -1;

        // loop through words to find the closest
        foreach ($arrays as $word) {

            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($input, $word);

            // check for an exact match
            if ($lev == 0) {

                // closest word is this one (exact match)
                $closest = $word;
                $shortest = 0;

                // break out of the loop; we've found an exact match
                break;
            }

            // if this distance is less than the next found shortest
            // distance, OR if a next shortest word has not yet been found
            if ($lev <= $shortest || $shortest < 0) {
                // set the closest match, and shortest distance
                $closest  = $word;
                $shortest = $lev;
            }
        }

        echo "Input word: $input\n";
        if ($shortest == 0) {
            echo "Exact match found: $closest\n";
        } else {
            echo "Did you mean: $closest?\n";
        }


    }
}
