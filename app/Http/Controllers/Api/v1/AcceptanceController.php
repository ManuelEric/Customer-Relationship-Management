<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreAcceptanceRequest as V1APIStoreAcceptanceRequest;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\pivot\ClientAcceptance;
use App\Models\University;
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
        $universityAcceptance = ClientAcceptance::where('client_id', $student->id)->orderByRaw(
            "CASE
                WHEN status = 'Final Decision' THEN 1
                ELSE 2
            END ASC"
        )->get();
        $uni_application = $universityAcceptance->map(function ($item) {
            return [
                'id' => $item->id,
                'univ_id' => $item->univ_id,
                'univ_name' => $item->university->univ_name,
                'early_action' => $item->early_action,
                'early_decision' => $item->early_decision,
                'regular_deadline' => $item->regular_deadline,
                'major_group_id' => $item->major_group_id,
                'major_group' => $item->major_group->mg_name ?? null,
                'major' => $item->get_major_name,
                'category' => ucwords($item->category),
                'requirement_link' => $item->requirement_link,
                'status' => ucwords($item->status),
            ];
        });
        $latest_adm_program = $student->clientProgram()->whereRelation('program.main_prog', 'prog_name', 'Admissions Mentoring')->latest()->first();
        $total = $latest_adm_program->total_uni;

        return response()->json(compact('uni_application', 'total'));
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
            // fetch early_action, early_decision, regular_deadline
            $master_univ = University::find($validated['univ_id']);

            $student->universityAcceptance()->attach($validated['univ_id'], [
                'category' => $validated['category'],
                'major_group_id' => $validated['major_group_id'],
                'major_name' => $validated['major_name'],
                'status' => $validated['status'],
                'requirement_link' => $validated['requirement_link'],
                'early_action' => $master_univ->early_action,
                'early_decision' => $master_univ->early_decision,
                'regular_deadline' => $master_univ->regular_deadline,
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
    ) {
        $validated = $request->safe()->only([
            'univ_id',
            'category',
            'major_group_id',
            'major_name',
            'status',
            'requirement_link',
            'acceptance_id',
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
            'message' => 'Uni application has been updated',
        ]);
    }

    public function fnDeleteUni(
        UserClient $student,
        LogService $log_service,
        $acceptance_id
    ) {
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
            'message' => 'Uni application has been deleted',
        ]);
    }
}
