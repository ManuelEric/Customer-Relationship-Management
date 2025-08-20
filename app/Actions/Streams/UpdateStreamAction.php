<?php

namespace App\Actions\Streams;

use App\Interfaces\StreamRepositoryInterface;

class UpdateStreamAction
{
    private StreamRepositoryInterface $streamRepository;

    public function __construct(StreamRepositoryInterface $streamRepository)
    {
        $this->streamRepository = $streamRepository;
    }

    public function execute(
        $stream_id,
        array $new_subject_details
    ) {

        $updated_subject = $this->streamRepository->rnUpdateStream($stream_id, $new_subject_details);

        return $updated_subject;
    }
}
