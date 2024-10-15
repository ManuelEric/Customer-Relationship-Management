<?php

namespace App\Actions\EdufLeads\Review;

use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\EdufReviewRepositoryInterface;

class UpdateEdufLeadReviewAction
{
    use StandardizePhoneNumberTrait;
    private EdufReviewRepositoryInterface $edufReviewRepository;

    public function __construct(EdufReviewRepositoryInterface $edufReviewRepository)
    {
        $this->edufReviewRepository = $edufReviewRepository;
    }

    public function execute(
        $eduf_lead_id,
        $eduf_review_id,
        Array $new_eduf_lead_review_details
    )
    {
       
        # Update eduf lead review
        $updated_eduf_lead_review = $this->edufReviewRepository->updateEdufairReview($eduf_lead_id, $eduf_review_id, $new_eduf_lead_review_details);

        return $updated_eduf_lead_review;
    }
}