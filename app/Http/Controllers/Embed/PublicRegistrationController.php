<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicRegistrationRequest;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Http\Request;

class PublicRegistrationController extends Controller
{
    private SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }
    
    public function register()
    {
        $schools = $this->schoolRepository->getAllSchools();

        return view('form-embed.form-website')->with([
            'schools' => $schools
        ]);
    }

    public function store(StorePublicRegistrationRequest $request)
    {

        $registrationDetail = [
            'parent_name' => $request->name,
            'child_name' => $request->child_name,
            'email' => $request->email,
            'phone' => $request->fullnumber,
            'school' => $request->school,
            'graduation_year' => $request->grade,
            'interest_program' => $request->program
        ];
        
        if ($request->school == "new-school")
            $registrationDetail['school'] = $request->new_school_box;

        // parent detail
        $parentDetail = [
            'first_name' => $registrationDetail['parent_name'],
            'mail' => ''
        ];

        // children detail

        // interest program for children


    }
}
