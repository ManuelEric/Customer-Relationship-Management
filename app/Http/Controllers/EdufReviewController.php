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
        $edufLId = $request->eduf_id;
        $reviewDetails = $request->only([
            'reviewer_name',
            'score',
            'review'
        ]);

        DB::beginTransaction();
        try {

            $this->edufReviewRepository->createEdufairReview($edufLId, $reviewDetails);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Store edufair review failed : ' . $e->getMessage());
            return Redirect::to('instance/edufair/'.$edufLId.'/edit')->withError('Failed to create new review');

        }

        return Redirect::to('instance/edufair/'.$edufLId.'/edit')->withSuccess('New review successfully created');
    }

    public function update(StoreEdufairReviewRequest $request)
    {
        $edufLId = $request->eduf_id;
        $edufRId = $request->route('review');
        $newDetails = $request->only([
            'reviewer_name',
            'score',
            'review'
        ]);

        DB::beginTransaction();
        try {

            $this->edufReviewRepository->updateEdufairReview($edufLId, $edufRId, $newDetails);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Update edufair review failed : ' . $e->getMessage());
            return Redirect::to('instance/edufair/'.$edufLId.'/edit')->withError('Failed to update review');

        }

        return Redirect::to('instance/edufair/'.$edufLId.'/edit')->withSuccess('Review successfully updated');
    }

    public function destroy(Request $request)
    {
        $edufLId = $request->route('edufair');
        $edufRId = $request->route('review');

        DB::beginTransaction();
        try {

            $this->edufReviewRepository->deleteEdufairReview($edufRId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete edufair review failed : ' . $e->getMessage());
            return Redirect::to('instance/edufair/'.$edufLId.'/edit')->withError('Failed to delete reivew');

        }

        return Redirect::to('instance/edufair/'.$edufLId.'/edit')->withSuccess('Review successfully deleted');
    }
}
