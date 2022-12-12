<?php

namespace App\Interfaces;

interface TagRepositoryInterface 
{
    public function getAllTags();
    public function getTagById($tagId);
}