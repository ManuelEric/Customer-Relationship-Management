<?php

namespace App\Services\Log;

use App\Enum\LogModule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogService
{
    protected $auth;
    public function __construct()
    {
        $this->auth = ! is_null(Auth::user()) ? Auth::user()->full_name : auth()->guard('api')->user()->full_name;
    }

    public function createErrorLog(LogModule $module, String $message, String $line, String $file_location, Array $content = [])
    {
        Log::error("{$module->value} : {$message} on {$file_location} line {$line} | done by {$this->auth}", $content);
    }

    public function createSuccessLog(LogModule $module, String $message, Array $content = [])
    {
        Log::notice("{$module->value} : {$message} | done by {$this->auth}", $content);
    }

    public function createInfoLog(LogModule $module, String $message)
    {
        Log::info("{$module->value} : {$message} | done by {$this->auth}");
    }
}