<?php
namespace App\Http\Traits;

trait FindDestinationCountryScore {

    public function getDestinationCountryScore($country)
    {
        switch ($country) {
            case "United States":
            case "United Kingdom":
                $score = 6;
                break;

            case "Canada":
            case "Australia":
                $score = 4;
                break;

            case "Asia":
                $score = 3;
                break;

            default:
                $score = 1;
        }

        return $score;
    }
}