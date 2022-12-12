<?php

namespace App\Repositories;

use App\Interfaces\TagRepositoryInterface;
use App\Models\Tag;

class TagRepository implements TagRepositoryInterface 
{

    public function getAllTags()
    {
        return Tag::all();
    }

    public function getTagById($tagId)
    {
        return Tag::find($tagId);
    }
}