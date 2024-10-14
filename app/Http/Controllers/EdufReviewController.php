<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEdufairReviewRequest;
use App\Interfaces\EdufReviewRepositoryInterface;
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

    public function store(StoreEdufairReviewRequest $request)
    {
        $eduf_lead_id = $request->eduf_id;
        $review_details = $request->only([
            'reviewer_name',
            'score',
            'review'
        ]);

        DB::beginTransaction();
        try {

            $this->edufReviewRepository->createEdufairReview($eduf_lead_id, $review_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store edufair review failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withError('Failed to create new review');
        }

        return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withSuccess('New review successfully created');
    }

    public function show(Request $request)
    {
        $id = $request->route('review');

        $review = $this->edufReviewRepository->getEdufairReviewById($id);

        return response()->json(['review' => $review]);
    }

    public function update(StoreEdufairReviewRequest $request)
    {
        $eduf_lead_id = $request->eduf_id;
        $eduf_review_Id = $request->route('review');
        $new_details = $request->only([
            'reviewer_name',
            'score',
            'review'
        ]);

        DB::beginTransaction();
        try {

            $this->edufReviewRepository->updateEdufairReview($eduf_lead_id, $eduf_review_Id, $new_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update edufair review failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withError('Failed to update review');
        }

        return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withSuccess('Review successfully updated');
    }

    public function destroy(Request $request)
    {
        $eduf_lead_id = $request->route('edufair');
        $eduf_review_Id = $request->route('review');

        DB::beginTransaction();
        try {

            $this->edufReviewRepository->deleteEdufairReview($eduf_review_Id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete edufair review failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withError('Failed to delete reivew');
        }

        return Redirect::to('master/edufair/' . $eduf_lead_id . '')->withSuccess('Review successfully deleted');
    }
}