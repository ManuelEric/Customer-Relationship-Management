<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Stream;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    public function all_streams()
    {
        return response()->json(Stream::active()->select('id', 'stream_name')->get());
    }
}
