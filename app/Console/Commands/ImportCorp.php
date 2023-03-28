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
        
                    $corp_phone = $this->getValueWithoutSpace($corporate->corp_phone);
                    if ($corp_phone != NULL)
                    {
                        $corp_phone = str_replace('-', '', $corp_phone);
                        $corp_phone = str_replace(' ', '', $corp_phone);
                        $corp_phone = str_replace('.', '', $corp_phone);
                        $corp_phone = str_replace('–', '', $corp_phone);
                        $corp_phone = str_replace(array('(', ')'), '', $corp_phone);
                        $corp_phone = str_replace(array('[', ']'), '', $corp_phone);

                        switch (substr($corp_phone, 0, 1)) {

                            case 0:
                                $corp_phone = "+62".substr($corp_phone, 1);
                                break;

                            case 6:
                                $corp_phone = "+".$corp_phone;
                                break;

                            case "+":
                                $corp_phone = $corp_phone;
                                break;

                            default: 
                                $corp_phone = "+62".$corp_phone;

                        }
                    }

                    # insert into corporate master data
                    $corporateDetails = [
                        'corp_id' => $corporate->corp_id,
                        'corp_name' => $corporate->corp_name,
                        'corp_industry' => $this->getValueWithoutSpace($corporate->corp_industry),
                        'corp_mail' => $this->getValueWithoutSpace($corporate->corp_mail),
                        'corp_phone' => $corp_phone,
                        'corp_insta' => $this->getValueWithoutSpace($corporate->corp_insta),
                        'corp_site' => $this->getValueWithoutSpace($corporate->corp_site),
                        'corp_region' => $this->getValueWithoutSpace($corporate->corp_region),
                        'corp_address' => $this->getValueWithoutSpace($corporate->corp_address),
                        'corp_note' => $this->getValueWithoutSpace($corporate->corp_note),
                        'corp_password' => $this->getValueWithoutSpace($corporate->corp_password),
                        'country_type' => 'Indonesia',
                        'type' => 'Corporate'
                    ];

                    $master = $this->corporateRepository->createCorporate($corporateDetails);

                    # fetch corp detail
                    foreach ($corporate->detail as $corporateDetail) {

                        $pic_phone = $this->getValueWithoutSpace($corporateDetail->corpdetail_phone);
                        if ($pic_phone != NULL)
                        {
                            $pic_phone = str_replace('-', '', $pic_phone);
                            $pic_phone = str_replace(' ', '', $pic_phone);
                            $pic_phone = str_replace(array('(', ')'), '', $pic_phone);

                            switch (substr($pic_phone, 0, 1)) {

                                case 0:
                                    $pic_phone = "+62".substr($pic_phone, 1);
                                    break;

                                case 6:
                                    $pic_phone = "+".$pic_phone;
                                    break;

                            }
                        }

                        $picDetails = [
                            'corp_id' => $master->corp_id,
                            'pic_name' => $corporateDetail->corpdetail_fullname,
                            'pic_mail' => $corporateDetail->corpdetail_mail == "" ? null : $corporateDetail->corpdetail_mail,
                            'pic_linkedin' => $corporateDetail->corpdetail_linkedin == "" ? null : $corporateDetail->corpdetail_linkedin,
                            'pic_phone' => $pic_phone
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

    private function getValueWithoutSpace($value)
    {
        return $value == "" || $value == "-" || $value == "tidak ada" || $value == "no contact" || $value == "0000-00-00" || $value == 'N/A' ? NULL : $value;
    }
}
