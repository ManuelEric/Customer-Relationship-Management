<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreAcceptanceRequest as V1APIStoreAcceptanceRequest;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\pivot\ClientAcceptance;
use App\Models\UserClient;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AcceptanceController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }


    public function fnListOfUniApplication(UserClient $student): JsonResponse
    {
        $mapped = $student->universityAcceptance->map(function ($item) {
            return [
                'univ_id' => $item->univ_id,
                'univ_name' => $item->univ_name,
                'univ_application_deadline' => $item->univ_application_deadline,
                'major_group' => $item->pivot->major_group->mg_name ?? null,
                'major' => $item->pivot->get_major_name,
                'category' => $item->pivot->category,
                'requirement_link' => $item->pivot->requirement_link
            ]; 
        });
        return response()->json($mapped);
    }

    public function fnAddUni(
        UserClient $student, 
        V1APIStoreAcceptanceRequest $request,
        LogService $log_service)
    {
        $validated = $request->safe()->only([
            'univ_id',
            'category',
            'major_group_id',
            'major_name',
            'status',
            'requirement_link',
        ]);

        DB::beginTransaction();
        try {
            $student->universityAcceptance()->attach($validated['univ_id'], [
                'category' => $validated['category'],
                'major_group_id' => $validated['major_group_id'],
                'major_name' => $validated['major_name'],
                'status' => $validated['status'],
                'requirement_link' => $validated['requirement_link'], 
            ]);
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_ALUMNI_ACCEPTANCE, $err->getMessage(), $err->getLine(), $err->getFile(), $validated);
            throw new HttpResponseException(
                response()->json(['errors' => 'Failed to add uni shortlist'], JsonResponse::HTTP_BAD_REQUEST)
            );
        }

        $log_service->createSuccessLog(LogModule::STORE_ALUMNI_ACCEPTANCE, 'New uni has been added to shortlist', $validated);
        return response()->json([
            'message' => 'Uni has successfully added',
        ]);
    }

    public function fnUpdateUni(
        UserClient $student, 
        V1APIStoreAcceptanceRequest $request,
        LogService $log_service
    )
    {
        $validated = $request->safe()->only([
            'univ_id',
            'category',
            'major_group_id',
            'major_name',
            'status',
            'requirement_link',
            'acceptance_id'
        ]);

        DB::beginTransaction();
        try {
            ClientAcceptance::find($validated['acceptance_id'])->update([
                'univ_id' => $validated['univ_id'],
                'category' => $validated['category'],
                'major_group_id' => $validated['major_group_id'],
                'major_name' => $validated['major_name'],
                'status' => $validated['status'],
                'requirement_link' => $validated['requirement_link'],
            ]);
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_ALUMNI_ACCEPTANCE, $err->getMessage(), $err->getLine(), $err->getFile(), $validated);
            throw new HttpResponseException(
                response()->json(['errors' => 'Failed to update uni shortlist'], JsonResponse::HTTP_BAD_REQUEST)
            );
        }

        $log_service->createSuccessLog(LogModule::UPDATE_ALUMNI_ACCEPTANCE, 'The uni has been updated to shortlist', $validated);
        return response()->json([
            'message' => 'Uni application has been updated'
        ]);
    }

    public function fnDeleteUni(
        UserClient $student, 
        LogService $log_service,
        $acceptance_id
    )
    {
        DB::beginTransaction();
        try {
            ClientAcceptance::findOrFail($acceptance_id)->delete();
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_ALUMNI_ACCEPTANCE, $err->getMessage(), $err->getLine(), $err->getFile());
            throw new HttpResponseException(
                response()->json(['errors' => 'Failed to delete uni shortlist'], JsonResponse::HTTP_BAD_REQUEST)
            );
        }

        $log_service->createSuccessLog(LogModule::DELETE_ALUMNI_ACCEPTANCE, 'The uni has been deleted to shortlist');
        return response()->json([
            'message' => 'Uni application has been deleted'
        ]);
    }
}
