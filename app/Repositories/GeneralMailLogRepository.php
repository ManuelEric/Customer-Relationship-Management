<?php

namespace App\Repositories;

use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Models\MailLog;

class GeneralMailLogRepository implements GeneralMailLogRepositoryInterface 
{
    public function getStatus($identifier)
    {
        return MailLog::where('identifier', $identifier)->first();
    }

    public function createLog(array $details)
    {
        return MailLog::create($details);
    }

    public function removeLog($identifier)
    {
        return MailLog::where('identifier', $identifier)->delete();
    }
}