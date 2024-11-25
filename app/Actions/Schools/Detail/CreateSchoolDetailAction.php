<?php

namespace App\Actions\Schools\Detail;

use App\Http\Requests\StoreSchoolDetailRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Services\Instance\SchoolService;

class CreateSchoolDetailAction
{
    use CreateCustomPrimaryKeyTrait, StandardizePhoneNumberTrait;
    private SchoolDetailRepositoryInterface $schoolDetailRepository;
    private SchoolService $schoolService;

    public function __construct(SchoolDetailRepositoryInterface $schoolDetailRepository, SchoolService $schoolService)
    {
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->schoolService = $schoolService;
    }

    public function execute(
        StoreSchoolDetailRequest $request,
        Array $validated
    )
    {
        # using index 0
        # because there is only one data in the array
        unset($validated['schdetail_phone'][0]);
        $validated['schdetail_phone'][0] = $this->tnSetPhoneNumber($request->schdetail_phone[0]);

        $school_details = $this->schoolService->snSetAttributeSchoolDetail($validated);

        # store new school detail
        $new_school_detail =  $this->schoolDetailRepository->createSchoolDetail($school_details);

        return $new_school_detail;
    }
}