<?php

namespace App\Http\Traits;

trait ClientMentorTrait
{
    public function translateType(Int $status): string
    {
        switch ($status) {
            case 1: 
                $type = "Supervising Mentor";
                break;
            case 2: 
                $type = "Profile Building & Exploration Mentor";
                break;
            case 3: 
                $type = "Application Strategy Mentor";
                break;
            case 4: 
                $type = "Writing Mentor";
                break;
            case 5: 
                $type = "Tutor";
                break;
            case 6: 
                $type = "Subject Specialist";
                break;
        }

        return $type;
    }
}