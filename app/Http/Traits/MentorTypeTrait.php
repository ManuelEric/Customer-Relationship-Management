<?php

namespace App\Http\Traits;

trait MentorTypeTrait
{
    public function tnDefineMentorType($mentorType)
    {
        switch ($mentorType) {
            case 1:
                return 'Supervising Mentor';
            case 2:
                return 'Profile Building & Exploration Mentor';
            case 3:
                return 'Application Strategy Mentor';
            case 4:
                return 'Writing Mentor';
            case 5:
                return 'Tutor';
            case 6:
                return 'Subject Specialist';
            default:
                return null;
        }
    }
}
