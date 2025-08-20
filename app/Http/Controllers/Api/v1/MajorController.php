<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\MajorGroup;

class MajorController extends Controller
{
    public function fnGetMajor()
    {
        return Major::active()->select('id', 'name')->orderBy('name')->get();
    }

    public function fnGetMajorGroup()
    {
        return MajorGroup::select('id', 'mg_name')->get();
    }
}
