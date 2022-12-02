<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientStudentRequest;
use Illuminate\Http\Request;

class ClientStudentController extends Controller
{
    
    public function index()
    {
        return view('pages.client.student.index');
    }

    public function store(StoreClientStudentRequest $request)
    {
        
    }

    public function create()
    {
        return view('pages.client.student.form');
    }
}
