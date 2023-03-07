<?php

namespace App\Repositories;

use App\Interfaces\MentorRepositoryInterface;
use App\Models\v1\Mentor;

class MentorRepository implements MentorRepositoryInterface 
{
    # CRM
    public function getAllMentors()
    {
        return Mentor::orderBy('mt_id', 'asc')->get();
    }
}