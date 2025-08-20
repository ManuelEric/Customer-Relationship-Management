<?php

namespace App\Repositories;

use App\Interfaces\MajorGroupRepositoryInterface;
use App\Models\MajorGroup;
use Illuminate\Database\Eloquent\Collection;

class MajorGroupRepository implements MajorGroupRepositoryInterface
{
    public function getMajorGroups(): Collection
    {
        return MajorGroup::all();
    }
}
