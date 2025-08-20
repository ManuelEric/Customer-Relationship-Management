<?php

namespace App\Actions\Streams;

use App\Models\Stream;

class DeleteStreamAction
{
    /**
     * Execute the action to delete a stream.
     */
    public function execute(int $stream_id): bool
    {
        $stream = Stream::findOrFail($stream_id);

        return $stream->delete();
    }
}
