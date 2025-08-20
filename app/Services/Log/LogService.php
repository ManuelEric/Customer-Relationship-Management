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
        $this->auth = $this->checkAuth();
    }

    public function createErrorLog(LogModule $module, string $message, string|int $line, string $file_location, array $content = [])
    {
        Log::error("{$module->value} : {$message} on {$file_location} line {$line} | done by {$this->auth}", $content);
    }

    public function createSuccessLog(LogModule $module, string $message, array $content = [])
    {
        Log::notice("{$module->value} : {$message} | done by {$this->auth}", $content);
    }

    public function createInfoLog(LogModule $module, string $message)
    {
        Log::info("{$module->value} : {$message} | done by {$this->auth}");
    }

    private function checkAuth()
    {
        if (Auth::check()) {
            $user_logged_in = Auth::user()->full_name;
        } elseif (Auth::guard('api')->check()) {
            $user_logged_in = Auth::guard('api')->user()->full_name;
        } else {
            $user_logged_in = 'Unknown from '.request()->ip();
        }

        return $user_logged_in;
    }
}
