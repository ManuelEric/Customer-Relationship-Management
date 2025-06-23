<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class JobProcessedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JobProcessed $event): void
    {
        $queue = $event->job->getQueue();
        Log::info("[JOB PROCESSED] Processed job on queue: $queue");
    }
}
