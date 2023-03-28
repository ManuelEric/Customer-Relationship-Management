<?php

namespace App\Http\Controllers;

use App\Exports\ClientEventTemplate;
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
                return Excel::download(new StudentTemplate, 'student-template.xlsx');
                break;


            case "client-event":
                return Excel::download(new ClientEventTemplate, 'client-event-template.xlsx');
                break;
        }
    }
}
