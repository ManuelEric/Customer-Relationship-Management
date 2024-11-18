<?php

namespace App\Http\Controllers;

use App\Actions\Corporates\Pic\CreateCorporatePicAction;
use App\Actions\Corporates\Pic\DeleteCorporatePicAction;
use App\Actions\Corporates\Pic\UpdateCorporatePicAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreCorporatePicRequest;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CorporatePicController extends Controller
{
    use StandardizePhoneNumberTrait;

    private CorporatePicRepositoryInterface $corporatePicRepository;

    public function __construct(CorporatePicRepositoryInterface $corporatePicRepository)
    {
        $this->corporatePicRepository = $corporatePicRepository;
    }

    public function show(Request $request): JsonResponse
    {
        $pic_id = $request->route('detail');

        $detail = $this->corporatePicRepository->getCorporatePicById($pic_id);

        return response()->json(
            [
                'success' => $detail ? true : false,
                'message' => $detail ? "Detail data has been retrieved" : "Couldn't get the detail data",
                'data' => $detail ? $detail : null
            ]
        );
    }

    public function store(StoreCorporatePicRequest $request, CreateCorporatePicAction $createCorporatePicAction, LogService $log_service)
    {
        $pic_details = $request->safe()->only([
            'pic_name',
            'pic_mail',
            'pic_phone',
            'pic_linkedin',
            'is_pic',
        ]);

        $corporate_id = $request->route('corporate');

        DB::beginTransaction();
        try {

            $craeted_corporate_pic = $createCorporatePicAction->execute($request, $pic_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_CORPORATE_PIC, $e->getMessage(), $e->getLine(), $e->getFile(), $pic_details);

            return Redirect::to('instance/corporate/'.$corporate_id)->withError('Failed to create corporate PIC');
            
        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_CORPORATE_PIC, 'New corporate pic has been added', $craeted_corporate_pic->toArray());

        return Redirect::to('instance/corporate/'.$corporate_id)->withSuccess('Corporate PIC successfully created');
    }
    
    public function update(StoreCorporatePicRequest $request, UpdateCorporatePicAction $updateCorporatePicAction, LogService $log_service)
    {
        $corporate_pic_details = $request->safe()->only([
            'pic_name',
            'pic_mail',
            'pic_phone',
            'pic_linkedin',
            'is_pic',
        ]);

        $pic_id = $request->route('detail');
        $corporate_id = $request->route('corporate');

        DB::beginTransaction();
        try {

            $updated_corporate_pic = $updateCorporatePicAction->execute($request, $pic_id, $corporate_id, $corporate_pic_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_CORPORATE_PIC, $e->getMessage(), $e->getLine(), $e->getFile(), $updated_corporate_pic);

            return Redirect::to('instance/corporate/'.$corporate_id)->withError('Failed to update corporate PIC');
            
        }

        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_CORPORATE_PIC, 'Corporate pic has been updated', $updated_corporate_pic->toArray());

        return Redirect::to('instance/corporate/'.$corporate_id)->withSuccess('Corporate PIC successfully updated');
    }

    public function destroy(Request $request, DeleteCorporatePicAction $deleteCorporatePicAction, LogService $log_service)
    {
        $corporate_id = $request->route('corporate');
        $pic_id = $request->route('detail');

        DB::beginTransaction();
        try {

            $deleteCorporatePicAction->execute($pic_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_CORPORATE_PIC, $e->getMessage(), $e->getLine(), $e->getFile(), ['corp_id' => $corporate_id, 'pic_id' => $pic_id]);

            return Redirect::to('instance/corporate/'.$corporate_id)->withError('Failed to delete corporate PIC');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_CORPORATE_PIC, 'Corporate pic has been deleted', ['corp_id' => $corporate_id, 'pic_id' => $pic_id]);

        return Redirect::to('instance/corporate/'.$corporate_id)->withSuccess('Corporate PIC successfully deleted');
    }
}
