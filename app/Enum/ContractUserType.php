<?php

namespace App\Enum;

enum ContractUserType: string
{   
    case EDITOR = 'Editor';
    case TUTOR = 'Tutor';
    case EXTERNAL_MENTOR = 'External Mentor';
    case INTERNSHIP = 'Internship';
    case PROBATION = 'Probation';
}
