<?php

namespace App\Repositories;

use App\Interfaces\StreamRepositoryInterface;
use App\Models\Stream;

class StreamRepository implements StreamRepositoryInterface
{
    public function rnGetAllStreams()
    {
        return Stream::active()->get();
    }
}