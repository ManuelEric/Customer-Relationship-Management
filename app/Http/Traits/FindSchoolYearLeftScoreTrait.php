<?php
namespace App\Http\Traits;

trait FindSchoolYearLeftScoreTrait {

    public function getSchoolYearLeftScore($difference)
    {
        switch ($difference) {
            case 0:
                $score = 7;
                break;

            case 1:
                $score = 5;
                break;

            case 2:
                $score = 4;
                break;

            case 3:
                $score = 3;
                break;

            default:
                $score = 1;
        }

        return $score;
    }
}