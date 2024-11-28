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

    private function checkAuth()
    {
        if ( Auth::check() )
            $user_logged_in = Auth::user()->full_name;
        else if ( Auth::guard('api')->check() )
            $user_logged_in = Auth::guard('api')->user()->full_name;
        else
            $user_logged_in = 'Unknown from ' . request()->ip();
            
        return $user_logged_in;
    }
}