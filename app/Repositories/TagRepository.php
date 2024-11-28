<?php

namespace App\Repositories;

use App\Interfaces\TagRepositoryInterface;
use App\Models\MasterCountry;
use App\Models\Tag;
use DataTables;

class TagRepository implements TagRepositoryInterface 
{
    public function getAllTagsDataTables()
    {
        return Datatables::eloquent(Tag::query())->make(true);
    }

    public function getAllCountries()
    {
        return MasterCountry::all();
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

    public function createTag(array $tag)
    {
        return Tag::create($tag);
    }

    public function deleteTag($tagId)
    {
        return Tag::destroy($tagId);
    }

    public function updateTag($tagId, array $tag)
    {
        return tap(Tag::find($tagId))->update($tag);
    }
}