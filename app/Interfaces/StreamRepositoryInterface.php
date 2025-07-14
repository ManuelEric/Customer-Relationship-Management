<?php

namespace App\Interfaces;

interface StreamRepositoryInterface
{
    public function rnGetAllStreams();
    public function rnGetDataTables();
    public function rnGetStreamById($stream_id);
    public function rnUpdateStream($stream_id, array $new_stream_details);
}
