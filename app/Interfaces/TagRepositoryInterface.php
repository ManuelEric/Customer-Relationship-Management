<?php

namespace App\Interfaces;

interface TagRepositoryInterface 
{
    public function getAllTagsDataTables();
    public function getAllTags();
    public function getTagById($tagId);
    public function getTagByName($tagName);
    public function createTag(array $tags);
    public function deleteTag($tagId);
    public function updateTag($tagId, array $tags);
}