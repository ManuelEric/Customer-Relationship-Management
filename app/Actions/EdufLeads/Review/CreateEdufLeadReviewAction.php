<?php

namespace App\Actions\EdufLeads\Review;

use App\Interfaces\EdufReviewRepositoryInterface;

Class CreateEdufLeadReviewAction
{
    private EdufReviewRepositoryInterface $edufReviewRepository;

    public function __construct(EdufReviewRepositoryInterface $edufReviewRepository)
    {
        $this->edufReviewRepository = $edufReviewRepository;
    }

    public function execute(
        $eduf_lead_id,
        Array $new_review_details
    )
    {

        # store new eduf review  
        $new_eduf_review = $this->edufReviewRepository->createEdufairReview($eduf_lead_id, $new_review_details);


        return $new_eduf_review;
    }
}