<?php

namespace App\Repositories;

use App\Interfaces\TagRepositoryInterface;
use App\Models\Tag;
use DataTables;

class TagRepository implements TagRepositoryInterface 
{
    public function getAllTagsDataTables()
    {
        return Datatables::eloquent(Tag::query())->make(true);
    }

    public function getAllTags()
    {
        return Tag::all();
    }

    public function getTagById($tagId)
    {
        return Tag::find($tagId);
    }

    public function getTagByName($tagName)
    {
        return Tag::where('name', 'like', '%'.$tagName.'%')->first();
    }

    public function createTag(array $tags)
    {
        return Tag::insert($tags);
    }

    public function deleteTag($tagId)
    {
        return Tag::destroy($tagId);
    }

    public function updateTag($tagId, array $tags)
    {
        return Tag::find($tagId)->update($tags);
    }
}