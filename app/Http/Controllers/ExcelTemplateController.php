<?php

namespace App\Http\Controllers;

use App\Exports\ClientEventTemplate;
use App\Exports\MasterClient;
use App\Exports\StudentTemplate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelTemplateController extends Controller
{

    public function generateTemplate(Request $request)
    {
        $type = $request->route('type');
        switch ($type) {

            case "student":
                return Excel::download(new MasterClient(['student']), 'student-template.xlsx');
                break;

            case "parent":
                return Excel::download(new MasterClient(['parent']), 'parent-template.xlsx');
                break;


            case "client-event":
                return Excel::download(new ClientEventTemplate, 'client-event-template.xlsx');
                break;
        }
    }
}
