<?php

namespace App\Repositories;

use App\Interfaces\EdufReviewRepositoryInterface;
use App\Models\EdufLead;
use App\Models\EdufReview;

class EdufReviewRepository implements EdufReviewRepositoryInterface
{
    public function getAllEdufairReviewByEdufairId($edufLId)
    {
        return EdufLead::find($edufLId)->review->get();
    }

    public function getEdufairReviewById($edufRId)
    {
        return EdufReview::find($edufRId);
    }

    public function deleteEdufairReview($edufRId)
    {
        return EdufReview::whereId($edufRId)->delete();
    }

    public function createEdufairReview($edufLId, array $edufairReviewDetails)
    {
        $edufairReviewDetails['eduf_id'] = $edufLId;
        return EdufReview::create($edufairReviewDetails);
    }

    public function updateEdufairReview($edufLId, $edufRId, array $newDetails)
    {
        $newDetails['eduf_id'] = $edufLId;
        return EdufReview::whereId($edufRId)->update($newDetails);
    }
}