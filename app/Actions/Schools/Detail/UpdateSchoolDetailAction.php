<?php

namespace App\Actions\Schools\Detail;

use App\Http\Requests\StoreSchoolDetailRequest;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Services\Instance\SchoolService;

class UpdateSchoolDetailAction
{
    use StandardizePhoneNumberTrait;
    private SchoolRepositoryInterface $schoolRepository;
    private SchoolDetailRepositoryInterface $schoolDetailRepository;
    private SchoolService $schoolService;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolDetailRepositoryInterface $schoolDetailRepository, SchoolService $schoolService)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->schoolService = $schoolService;
    }

    public function execute(
        StoreSchoolDetailRequest $request,
        $school_detail_id,
        Array $validated
    )
    {
        unset($validated['schdetail_phone'][0]);
        $validated['schdetail_phone'][0] = $this->tnSetPhoneNumber($request->schdetail_phone[0]);
        
        $school_details = $this->schoolService->snSetAttributeSchoolDetail($validated, true);

        $updated_school_detail = $this->schoolDetailRepository->updateSchoolDetail($school_detail_id, $school_details);
       
        return $updated_school_detail;
    }
}