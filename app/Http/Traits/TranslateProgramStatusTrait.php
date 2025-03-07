<?php

namespace App\Http\Traits;

trait TranslateProgramStatusTrait
{
    public function translate(int $status): string
    {   
        switch ($status) {
            case 0:
                $convert = "Upcoming";
                break;
            case 1:
                $convert = "Ongoing";
                break;
            case 2:
                $convert = "Completed";
                break;
        }

        return $convert;
    }
}