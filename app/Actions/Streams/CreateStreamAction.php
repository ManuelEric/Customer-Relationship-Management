<?php

namespace App\Actions\Streams;

use App\Models\Stream;

class CreateStreamAction
{
    /**
     * Execute the action to create a new stream.
     *
     * @return \App\Models\Stream
     */
    public function execute(array $streamDetails)
    {
        return Stream::create($streamDetails);
    }
}
