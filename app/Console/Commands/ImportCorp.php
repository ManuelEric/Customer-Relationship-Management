<?php

namespace App\Console\Commands;

use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportCorp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:corp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import corp data from crm big data v1';

    protected CorporateRepositoryInterface $corporateRepository;
    protected CorporatePicRepositoryInterface $corporatePicRepository;

    public function __construct(CorporateRepositoryInterface $corporateRepository, CorporatePicRepositoryInterface $corporatePicRepository)
    {
        parent::__construct();
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $corporates = $this->corporateRepository->getCorpFromV1();
        DB::beginTransaction();
        try {

            foreach ($corporates as $corporate) {
    
                # if corp id from v1 does not exist on v2
                if (!$this->corporateRepository->getCorporateById($corporate->corp_id)) {
        
                    # insert into corporate master data
                    $master = $this->corporateRepository->createCorporate($corporate->toArray());

                    # fetch corp detail
                    foreach ($corporate->detail as $corporateDetail) {

                        $picDetails = [
                            'corp_id' => $master->corp_id,
                            'pic_name' => $corporateDetail->corpdetail_fullname,
                            'pic_mail' => $corporateDetail->corpdetail_mail == "" ? null : $corporateDetail->corpdetail_mail,
                            'pic_linkedin' => $corporateDetail->corpdetail_linkedin == "" ? null : $corporateDetail->corpdetail_linkedin,
                            'pic_phone' => $corporateDetail->corpdetail_phone == "" ? null : $corporateDetail->corpdetail_phone
                        ];
                        # insert into corp pic
                        $this->corporatePicRepository->createCorporatePic($picDetails);

                    }
    
                }
    
            }
            DB::commit();
            Log::info('Import Corporate works fine');

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::warning('There\'s something wrong with import corporate : '.$e->getMessage());

        }
        return Command::SUCCESS;
    }
}
