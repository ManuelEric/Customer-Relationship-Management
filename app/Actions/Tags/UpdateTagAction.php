<?php

namespace App\Actions\Tags;

use App\Interfaces\TagRepositoryInterface;

class UpdateTagAction
{
    private TagRepositoryInterface $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function execute(
        $tag_id,
        Array $new_tag_details
    )
    {

        $updated_tag = $this->tagRepository->updateTag($tag_id, $new_tag_details);

        return $updated_tag;
    }
}