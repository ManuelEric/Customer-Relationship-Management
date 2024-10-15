<?php

namespace App\Actions\Tags;

use App\Interfaces\TagRepositoryInterface;

class DeleteTagAction
{
    private TagRepositoryInterface $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function execute(
        $tag_id
    )
    {
        # delete tag
        $deleted_tag = $this->tagRepository->deleteTag($tag_id);

        return $deleted_tag;
    }
}