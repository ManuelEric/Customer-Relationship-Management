<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolDetailRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolDetailController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;

    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolDetailRepositoryInterface $schoolDetailRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolDetailRepositoryInterface $schoolDetailRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
    }

    public function store(StoreSchoolDetailRequest $request)
    {
        $validated = $request->all([
            'sch_id',
            'schdetail_name',
            'schdetail_mail',
            'schdetail_grade',
            'schdetail_position',
            'schdetail_phone',
            'is_pic',
        ]);
        
        # using index 0
        # because there is only one data in the array
        unset($validated['schdetail_phone'][0]);
        $validated['schdetail_phone'][0] = $this->tnSetPhoneNumber($request->schdetail_phone[0]);


        DB::beginTransaction();
        try {

            $representMaxLength = count($validated['schdetail_name']);
            for ($i = 0; $i < $representMaxLength; $i++) {
                $schoolDetails[] = [
                    'sch_id' => $validated['sch_id'],
                    'schdetail_fullname' => $validated['schdetail_name'][$i],
                    'schdetail_email' => $validated['schdetail_mail'][$i],
                    'schdetail_grade' => $validated['schdetail_grade'][$i],
                    'schdetail_position' => $validated['schdetail_position'][$i],
                    'schdetail_phone' => $validated['schdetail_phone'][$i],
                    'is_pic' => $validated['is_pic'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            $this->schoolDetailRepository->createSchoolDetail($schoolDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school contact person failed : ' . $e->getMessage());
            return Redirect::to('instance/school/' . $request->sch_id)->withError('Failed to create a new contact person');
        }

        return Redirect::to('instance/school/' . $request->sch_id)->withSuccess('School contact person successfully created');
    }

    public function create(Request $request)
    {
        $school_id = $request->route('school');

        return view('pages.instance.school.detail.form')->with(
            [
                'school_id' => $school_id
            ]
        );
    }

    public function edit(Request $request): JsonResponse
    {
        $schoolDetailId = $request->route('detail');

        # retrieve school detail data by id
        $schoolDetail = $this->schoolDetailRepository->getSchoolDetailById($schoolDetailId);

        return response()->json([
            'school_id' => $schoolDetail->sch_id,
            'schoolDetail' => $schoolDetail,
        ]);
    }

    public function update(StoreSchoolDetailRequest $request)
    {
        $validated = $request->all([
            'sch_id',
            'schdetail_name',
            'schdetail_mail',
            'schdetail_grade',
            'schdetail_position',
            'schdetail_phone',
            'is_pic'
        ]);

        unset($validated['schdetail_phone'][0]);
        $validated['schdetail_phone'][0] = $this->tnSetPhoneNumber($request->schdetail_phone[0]);

        $schoolDetailId = $request->route('detail');

        DB::beginTransaction();
        try {

            $representMaxLength = count($validated['schdetail_name']);
            for ($i = 0; $i < $representMaxLength; $i++) {
                $schoolDetails = [
                    'sch_id' => $validated['sch_id'],
                    'schdetail_fullname' => $validated['schdetail_name'][$i],
                    'schdetail_email' => $validated['schdetail_mail'][$i],
                    'schdetail_grade' => $validated['schdetail_grade'][$i],
                    'schdetail_position' => $validated['schdetail_position'][$i],
                    'schdetail_phone' => $validated['schdetail_phone'][$i],
                    'is_pic' => $validated['is_pic'][$i],
                    'updated_at' => Carbon::now(),
                ];
            }

            $this->schoolDetailRepository->updateSchoolDetail($schoolDetailId, $schoolDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update contact person failed : ' . $e->getMessage());
            return Redirect::to('instance/school/' . $request->sch_id)->withError('Failed to update a contact person');
        }

        return Redirect::to('instance/school/' . $request->sch_id)->withSuccess('Contact person successfully updated');
    }

    public function destroy(Request $request)
    {
        $schoolId = $request->route('school');
        $schoolDetailId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->schoolDetailRepository->deleteSchoolDetail($schoolDetailId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete contact person failed : ' . $e->getMessage());
            return Redirect::to('instance/school/' . $schoolId)->withError('Failed to delete a contact person');
        }

        return Redirect::to('instance/school/' . $schoolId)->withSuccess('Contact person has successfully deleted');
    }
}