<?php

namespace App\Interfaces;

interface EdufReviewRepositoryInterface 
{
    public function getAllEdufairReviewByEdufairId($edufLId);
    public function getEdufairReviewById($edufRId);
    public function deleteEdufairReview($edufRId);
    public function createEdufairReview($edufLId, array $edufairReviewDetails);
    public function updateEdufairReview($edufLId, $edufRId, array $newDetails);
}