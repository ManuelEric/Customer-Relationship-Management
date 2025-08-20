<?php

namespace App\Repositories;

use App\Interfaces\StreamRepositoryInterface;
use App\Models\Stream;
use Yajra\DataTables\Facades\DataTables;

class StreamRepository implements StreamRepositoryInterface
{
    public function rnGetAllStreams()
    {
        return Stream::active()->get();
    }

    public function rnGetDataTables()
    {
        return DataTables::eloquent(Stream::query())
            ->make(true);
    }

    public function rnGetStreamById($stream_id)
    {
        return Stream::findOrFail($stream_id);
    }

    public function rnUpdateStream($stream_id, array $new_stream_details)
    {
        $stream = Stream::findOrFail($stream_id);
        $stream->update($new_stream_details);

        return $stream;
    }
}
