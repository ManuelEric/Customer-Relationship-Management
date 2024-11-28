<?php

namespace App\Http\Controllers;

use App\Actions\EdufLeads\Review\CreateEdufLeadReviewAction;
use App\Actions\EdufLeads\Review\DeleteEdufLeadReviewAction;
use App\Actions\EdufLeads\Review\UpdateEdufLeadReviewAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreEdufairReviewRequest;
use App\Interfaces\EdufReviewRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EdufReviewController extends Controller
{
    private EdufReviewRepositoryInterface $edufReviewRepository;

    public function __construct(EdufReviewRepositoryInterface $edufReviewRepository)
    {
        $this->edufReviewRepository = $edufReviewRepository;
    }

    public function store(StoreEdufairReviewRequest $request, CreateEdufLeadReviewAction $createEdufLeadReviewAction, LogService $log_service)
    {
        $eduf_lead_id = $request->eduf_id;
        $new_review_details = $request->safe()->only([
            'reviewer_name',
            'score',
            'review'
        ]);

        DB::beginTransaction();
        try {

            $new_review = $createEdufLeadReviewAction->execute($eduf_lead_id, $new_review_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_EDUF_LEAD_REVIEW, $e->getMessage(), $e->getLine(), $e->getFile(), $new_review_details);

            return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withError('Failed to create new review');
        }

        $log_service->createSuccessLog(LogModule::STORE_EDUF_LEAD_REVIEW, 'New eduf lead review has been added', $new_review->toArray());

        return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withSuccess('New review successfully created');
    }

    public function show(Request $request)
    {
        $id = $request->route('review');
        $review = $this->edufReviewRepository->getEdufairReviewById($id);
        return response()->json(['review' => $review]);
    }

    public function update(StoreEdufairReviewRequest $request, UpdateEdufLeadReviewAction $updateEdufLeadReviewAction, LogService $log_service)
    {
        $eduf_lead_id = $request->eduf_id;
        $eduf_review_id = $request->route('review');
        $new_eduf_lead_review_details = $request->safe()->only([
            'reviewer_name',
            'score',
            'review'
        ]);

        DB::beginTransaction();
        try {

            $updated_eduf_lead_review = $updateEdufLeadReviewAction->execute($eduf_lead_id, $eduf_review_id, $new_eduf_lead_review_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_EDUF_LEAD_REVIEW, $e->getMessage(), $e->getLine(), $e->getFile(), $new_eduf_lead_review_details);

            return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withError('Failed to update review');
        }

        $log_service->createSuccessLog(LogModule::UPDATE_EDUF_LEAD_REVIEW, 'New eduf lead review has been updated', $updated_eduf_lead_review->toArray());

        return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withSuccess('Review successfully updated');
    }

    public function destroy(Request $request, DeleteEdufLeadReviewAction $deleteEdufLeadReviewAction, LogService $log_service)
    {
        $eduf_lead_id = $request->route('edufair');
        $eduf_review_id = $request->route('review');

        DB::beginTransaction();
        try {

            $deleteEdufLeadReviewAction->execute($eduf_review_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_EDUF_LEAD_REVIEW, $e->getMessage(), $e->getLine(), $e->getFile(), ['eduf_review_id' => $eduf_review_id]);

            return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withError('Failed to delete reivew');
        }

        $log_service->createSuccessLog(LogModule::DELETE_EDUF_LEAD_REVIEW, 'Eduf lead review has been deleted', ['eduf_review_id' => $eduf_review_id]);

        return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withSuccess('Review successfully deleted');
    }
}