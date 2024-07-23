<?php

namespace App\Jobs\Event\Stem;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProcessEmailQuestCompleter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    public $tries = 3;
    public $timeout = 600;

    // Priority levels: high, default, low
    public $priority = 'high';

    protected $mailDetails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $mailDetails)
    {
        $this->mailDetails = $mailDetails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        try {

            Mail::send('mail-template.quest-completer', $this->mailDetails, function ($message) {
                $message->to($this->mailDetails['email'], $this->mailDetails['recipient'])
                    ->subject('Here’s a gift for you, Level ' . $this->mailDetails['level'] . ' Maker’s Quest Completer!')
                    ->attach(public_path('img/makerspace/certificate/certificate_quest_level_'.$this->mailDetails['level'].'-min.jpg'));
            });
            $sent_status = 1;

        } catch (Exception $e) {
            
            $sent_status = 0;
            Log::debug('Failed to send quest completer mail : ' . $e->getMessage());

        }

        Log::debug('Send quest completer mail fullname: ' . $this->mailDetails['recipient'] . ' status: ' . $sent_status, ['fullname' => $this->mailDetails['recipient'], 'email' => $this->mailDetails['email'], 'level' => $this->mailDetails['level'], 'sent_status' => $sent_status]);


        
    }
}