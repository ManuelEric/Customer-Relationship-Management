<?php

namespace App\Console\Commands;

use App\Actions\Contracts\FindExpiringContractByTypeAction;
use App\Events\Contracts\SendingReminderExpiringContractEvent;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Services\Partnership\Partner\PartnerService;
use App\Services\User\UserService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendReminderExpirationAgreementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_expiration_agreement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending a reminder to Partnership Team if partner agreement that must be renewed or almost finished.';
    
    private PartnerAgreementRepositoryInterface $partnerAgreementRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private PartnerService $partnerService;

    public function __construct(PartnerAgreementRepositoryInterface $partnerAgreementRepository, CorporateRepositoryInterface $corporateRepository, PartnerService $partnerService)
    {
        parent::__construct();
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->corporateRepository = $corporateRepository;
        $this->partnerService = $partnerService;
    }

    # Purpose:
    # get data contracts user that almost meet the end of the contract
    # send mail to Internal Team
    public function handle()
    {
        # NOTE: CRON DIBIKIN SETIAP JAM 7 PAGI
        Log::debug('[CRON] send reminder expiration partner agreement working properly.');

        $partner_agreement_expired_soon = [];
        
        try {
            $agreements = $this->partnerAgreementRepository->rnGetExpiringPartnerAgreement(30);
            $progress_bar = $this->output->createProgressBar($agreements->count());
            $progress_bar->start();
            
            foreach ($agreements as $agreement) 
            {
                $cc_mail = [
                    env('PARTNERSHIP_MAIL_1'),
                    env('PARTNERSHIP_MAIL_2'),
                ];

                if (!$agreement->partner){
                    Log::error('Failed send reminder expiration partner agreement. Data partner not found!', [$agreement->toArray()]);
                    continue;
                }
                
                if (!$agreement->partnerPic){
                    Log::error('Failed send reminder expiration partner agreement. Data PIC Partner not found!', [$agreement->toArray()]);
                    continue;
                }
                
                if ($agreement->partnerPic->pic_mail == NULL || $agreement->partnerPic->pic_mail == ''){
                    Log::error('Failed send reminder expiration partner agreement. Data PIC mail not found!', [$agreement->toArray()]);
                    continue;
                }
    
                $partner_agreement_expired_soon = [
                    'full_name' => $agreement->partnerPic->pic_name,
                    'agreement_name' => $agreement->agreement_name,
                    'end_date' => $agreement->end_date,
                ];

                if(isset($agreement->partner->individualProfessional) && count($agreement->partner->individualProfessional->roles) > 0){
                    if(in_array(19, $agreement->partner->individualProfessional->roles)){

                    }
                }
                
                $this->partnerService->snSendMailExpirationAgreement($agreement->partnerPic->pic_mail, $partner_agreement_expired_soon);
                
                if($agreement->end_date == Date('Y-m-d')){
                    $this->corporateRepository->updateCorporate($agreement->corp_id, ['corp_status' => 'Expired']);
                }

                $agreement->increment('reminded', 1);

                $progress_bar->advance();
            }
        } catch (Exception $e) {
            Log::error('Failed send reminder expiration partner agreement: '. $e->getMessage() . ' | OnLine: ' . $e->getLine() . ' | OnFile: '. $e->getFile(), [$agreement->toArray()]);
        }

        return Command::SUCCESS;
    }
}
