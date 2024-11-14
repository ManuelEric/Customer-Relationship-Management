<?php

namespace App\Http\Controllers;

use App\Actions\Schools\Detail\CreateSchoolDetailAction;
use App\Actions\Schools\Detail\DeleteSchoolDetailAction;
use App\Actions\Schools\Detail\UpdateSchoolDetailAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSchoolDetailRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Services\Instance\SchoolService;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SchoolDetailController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;

    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolDetailRepositoryInterface $schoolDetailRepository;
    protected SchoolService $schoolService;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolDetailRepositoryInterface $schoolDetailRepository, SchoolService $schoolService)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->schoolService = $schoolService;
    }

    public function store(StoreSchoolDetailRequest $request, CreateSchoolDetailAction $createSchoolDetailAction, LogService $log_service)
    {
        $validated = $request->safe()->only([
            'sch_id',
            'schdetail_name',
            'schdetail_mail',
            'schdetail_grade',
            'schdetail_position',
            'schdetail_phone',
            'is_pic',
        ]);
        
        
        DB::beginTransaction();
        try {

            $created_school_detail = $createSchoolDetailAction->execute($request, $validated);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SCHOOL_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), $validated);

            return Redirect::to('instance/school/' . $request->sch_id)->withError('Failed to create a new contact person');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SCHOOL_DETAIL, 'New school detail has been added', $created_school_detail->toArray());

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
        $school_detail_id = $request->route('detail');

        # retrieve school detail data by id
        $school_detail = $this->schoolDetailRepository->getSchoolDetailById($school_detail_id);

        return response()->json([
            'school_id' => $school_detail->sch_id,
            'schoolDetail' => $school_detail,
        ]);
    }

    public function update(StoreSchoolDetailRequest $request, UpdateSchoolDetailAction $updateSchoolDetailAction, LogService $log_service)
    {
        $validated = $request->safe()->only([
            'sch_id',
            'schdetail_name',
            'schdetail_mail',
            'schdetail_grade',
            'schdetail_position',
            'schdetail_phone',
            'is_pic'
        ]);

        $school_detail_id = $request->route('detail');
        
        DB::beginTransaction();
        try {

            $updated_school_detail = $updateSchoolDetailAction->execute($request, $school_detail_id, $validated);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_SCHOOL_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), $validated);

            return Redirect::to('instance/school/' . $request->sch_id)->withError('Failed to update a contact person');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_SCHOOL_DETAIL, 'School detail has been updated', $updated_school_detail->toArray());

        return Redirect::to('instance/school/' . $request->sch_id)->withSuccess('Contact person successfully updated');
    }

    public function destroy(Request $request, DeleteSchoolDetailAction $deleteSchoolDetailAction, LogService $log_service)
    {
        $school_id = $request->route('school');
        $school_detail_id = $request->route('detail');

        DB::beginTransaction();
        try {

            $deleted_school_detail = $deleteSchoolDetailAction->execute($school_detail_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SCHOOL_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), $deleted_school_detail->toArray());

            return Redirect::to('instance/school/' . $school_id)->withError('Failed to delete a contact person');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL_DETAIL, 'School detail has been deleted', $deleted_school_detail->toArray());

        return Redirect::to('instance/school/' . $school_id)->withSuccess('Contact person has successfully deleted');
    }
}