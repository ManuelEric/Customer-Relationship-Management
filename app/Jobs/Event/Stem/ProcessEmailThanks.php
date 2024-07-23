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

class ProcessEmailThanks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    public $tries = 3;
    public $timeout = 600;

    // Priority levels: high, default, low
    public $priority = 'high';

    protected $mailDetails;
    protected $clientEventId;
    protected ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $mailDetails, $clientEventId, ClientEventLogMailRepositoryInterface $clientEventLogMailRepository)
    {
        $this->mailDetails = $mailDetails;
        $this->clientEventId = $clientEventId;
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (in_array('student', $this->mailDetails['roles'])) {

            $view = 'mail-template.thanks-email-stem-student';
            $subject = 'Earn special discount to develop your project in Singapore!';
            $this->mailDetails['wa_text_anggie'] = 'Hello Anggie, I’m '.$this->mailDetails['recipient'].', I have attended STEM+ Wonderlab and would like to claim 100 USD discount for the Innovators-in-Residence program in Singapore. Can you give me further information about this program?';
            $this->mailDetails['wa_text_derry'] = 'Hello Derry, I’m '.$this->mailDetails['recipient'].', I have attended STEM+ Wonderlab and would like to claim 100 USD discount for the Innovators-in-Residence program in Singapore. Can you give me further information about this program?';


        } elseif (in_array('parent', $this->mailDetails['roles'])) {

            $view = 'mail-template.thanks-email-stem-parent';
            $subject = 'Dapatkan diskon special untuk program kami di Singapore!';
            $this->mailDetails['wa_text_anggie'] = 'Halo Anggie, saya '.$this->mailDetails['recipient'].', saya sudah hadir di STEM+ Wonderlab dan ingin claim 100 USD discount untuk program Innovators-in-Residence di Singapore. Apakah saya boleh diinformasikan lebih lanjut mengenai hal ini?';
            $this->mailDetails['wa_text_derry'] = 'Halo Derry, saya '.$this->mailDetails['recipient'].', saya sudah hadir di STEM+ Wonderlab dan ingin claim 100 USD discount untuk program Innovators-in-Residence di Singapore. Apakah saya boleh diinformasikan lebih lanjut mengenai hal ini?';
            

        }


        try {

            # send email
            Mail::send($view, $this->mailDetails, function ($message) use ($subject) {
                $message->to($this->mailDetails['email'], $this->mailDetails['recipient'])
                    ->subject($subject);
            });
            $sent_status = 1;

        } catch (Exception $e) {
            
            $sent_status = 0;

        }

        try {

            $logDetails = [
                'clientevent_id' => $this->clientEventId,
                'sent_status' => $sent_status,
                'category' => 'thanks-mail-after'
            ];


            # check if log is exists
            # when exists then just update the sent_status
            if ($foundLog = $this->clientEventLogMailRepository->getClientEventLogMailByClientEventIdAndCategory($this->clientEventId, 'thanks-mail-after')) {
                Log::info($this->clientEventId.' dan '.json_encode($foundLog));

                $this->clientEventLogMailRepository->updateClientEventLogMail($foundLog->id, ['sent_status' => 1]);

            } else {

                $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
            }


        } catch (Exception $e) {

            Log::error('Failed to create event log email thanks' . $e->getMessage());

        }

        
    }
}
