<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCorporatePicRequest;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\CorporatePicRepositoryInterface;
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
        $picId = $request->route('detail');

        $detail = $this->corporatePicRepository->getCorporatePicById($picId);

        return response()->json(
            [
                'success' => $detail ? true : false,
                'message' => $detail ? "Detail data has been retrieved" : "Couldn't get the detail data",
                'data' => $detail ? $detail : null
            ]
        );
    }

    public function store(StoreCorporatePicRequest $request)
    {
        $picDetails = $request->only([
            'pic_name',
            'pic_mail',
            'pic_phone',
            'pic_linkedin',
            'is_pic',
        ]);
        unset($picDetails['pic_phone']);
        $picDetails['pic_phone'] = $this->tnSetPhoneNumber($request->pic_phone);

        $picDetails['corp_id'] = $corporateId = $request->route('corporate');

        DB::beginTransaction();
        try {

            $this->corporatePicRepository->createCorporatePic($picDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store corporate PIC failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate/'.$corporateId)->withError('Failed to create corporate PIC');
            
        }

        return Redirect::to('instance/corporate/'.$corporateId)->withSuccess('Corporate PIC successfully created');
    }
    
    public function update(StoreCorporatePicRequest $request)
    {
        $newDetails = $request->only([
            'pic_name',
            'pic_mail',
            'pic_phone',
            'pic_linkedin',
            'is_pic',
        ]);

        unset($newDetails['pic_phone']);
        $picDetails['pic_phone'] = $this->tnSetPhoneNumber($request->pic_phone);

        $newDetails['corp_id'] = $corporateId = $request->route('corporate');
        $picId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->corporatePicRepository->updateCorporatePic($picId, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update corporate PIC failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate/'.$corporateId)->withError('Failed to update corporate PIC');
            
        }

        return Redirect::to('instance/corporate/'.$corporateId)->withSuccess('Corporate PIC successfully updated');
    }

    public function destroy(Request $request)
    {
        $corporateId = $request->route('corporate');
        $picId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->corporatePicRepository->deleteCorporatePic($picId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete corporate PIC failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate/'.$corporateId)->withError('Failed to delete corporate PIC');
        }

        return Redirect::to('instance/corporate/'.$corporateId)->withSuccess('Corporate PIC successfully deleted');
    }
}
