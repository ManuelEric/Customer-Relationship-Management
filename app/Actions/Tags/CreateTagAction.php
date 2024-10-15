<?php

namespace App\Actions\Tags;

use App\Interfaces\TagRepositoryInterface;

class CreateTagAction
{
    private TagRepositoryInterface $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function execute(
        Array $new_tag_details
    )
    {
        # store new subject
        $new_tag = $this->tagRepository->createTag($new_tag_details);

        return $new_tag;
    }
}