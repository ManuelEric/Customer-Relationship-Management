<?php

namespace App\Actions\EdufLeads\Review;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\EdufReviewRepositoryInterface;

class DeleteEdufLeadReviewAction
{
    use CreateCustomPrimaryKeyTrait;
    private EdufReviewRepositoryInterface $edufReviewRepository;

    public function __construct(EdufReviewRepositoryInterface $edufReviewRepository)
    {
        $this->edufReviewRepository = $edufReviewRepository;
    }

    public function execute(
        $eduf_review_id
    )
    {
        # Delete eduf_lead_review
        $deleted_eduf_lead_review = $this->edufReviewRepository->deleteEdufairReview($eduf_review_id);

        return $deleted_eduf_lead_review;
    }
}