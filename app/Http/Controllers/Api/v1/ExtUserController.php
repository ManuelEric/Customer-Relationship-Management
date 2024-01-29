<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExtUserController extends Controller
{
    public function getMemberOfDepartments(Request $request)
    {
        $department = $request->route('department');
        return $department;
    }
}
