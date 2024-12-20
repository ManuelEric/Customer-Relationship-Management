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
        Log::debug('[CRON] send reminder expiration partner agreement working properly.');

        $partner_agreement_expired_soon = [];
        
        try {
            $agreements = $this->partnerAgreementRepository->rnGetExpiringPartnerAgreement(30);
            $progress_bar = $this->output->createProgressBar($agreements->count());
            $progress_bar->start();
            
            foreach ($agreements as $agreement) 
            {
                # CC Mail (HR + Operation)
                $cc_mail = [
                    env('HR_MAIL'),
                    env('HR_MAIL_2'),
                    env('OPERATION_MAIL_2'),
                    env('OPERATION_MAIL_3'),
                ];
                
                # Sharon
                $recipient = env('OPERATION_MAIL_1');

                $tutor = $editor = $collaborator = false;

                if (!$agreement->partner){
                    Log::error('Failed send reminder expiration partner agreement. Data partner not found!', [$agreement->toArray()]);
                    continue;
                }
                
                // if (!$agreement->partnerPic){
                //     Log::error('Failed send reminder expiration partner agreement. Data PIC Partner not found!', [$agreement->toArray()]);
                //     continue;
                // }
                
                // if ($agreement->partnerPic->pic_mail == NULL || $agreement->partnerPic->pic_mail == ''){
                //     Log::error('Failed send reminder expiration partner agreement. Data PIC mail not found!', [$agreement->toArray()]);
                //     continue;
                // }
    
                $partner_agreement_expired_soon = [
                    'full_name' => $agreement->partner->partner_name,
                    'agreement_name' => $agreement->agreement_name,
                    'end_date' => $agreement->end_date,
                ];

                if($agreement->partner->partnership_type != null || $agreement->partner->partnership_type != ''){

                    $collaborator = $agreement->partner->where('partnership_type', 'Market Sharing/Referral Collaboration')->first() || $agreement->partner->where('partnership_type', 'Speaker')->first() ? true : false;

                    if($agreement->partner->partnership_type == 'Individual Professional'){
                        $roles = $agreement->partner->individualProfessional->roles()->pluck('role_id')->toArray();
                        $editor = in_array(3, $roles) || in_array(13, $roles) || in_array(14, $roles) || in_array(15, $roles) ? true : false;
                        $tutor = in_array(4, $roles) ? true : false;

                    }

                }
                
                if($tutor){
                    # Steven
                    $recipient = env('TUTOR_MAIL');
                }else if($editor){
                    # Thalia
                    $recipient = env('EDITOR_MAIL');
                }else if($collaborator){
                    # Tere and Feri
                    $recipient = [env('PARTNERSHIP_MAIL_1'), env('PARTNERSHIP_MAIL_2')];
                }

                $this->partnerService->snSendMailExpirationAgreement($partner_agreement_expired_soon, $recipient, $cc_mail);
                
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
