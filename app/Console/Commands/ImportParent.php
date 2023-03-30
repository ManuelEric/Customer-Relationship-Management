<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportParent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:parent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import parent from crm big data v1';
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {

            $crm_parents = $this->clientRepository->getParentFromV1();
            $progressBar = $this->output->createProgressBar($crm_parents->count());
            $progressBar->start();
            foreach ($crm_parents as $crm_parent)
            {
                $parentName = $crm_parent->pr_firstname.' '.$crm_parent->pr_lastname;
                if (!$parent = $this->clientRepository->getParentByParentName($parentName))
                {

                    $parents_phone = $this->getValueWithoutSpace($crm_parent->pr_phone);
                    if ($parents_phone != NULL)
                    {
                        $parents_phone = str_replace('-', '', $parents_phone);
                        $parents_phone = str_replace(' ', '', $parents_phone);

                        switch (substr($parents_phone, 0, 1)) {

                            case 0:
                                $parents_phone = "+62".substr($parents_phone, 1);
                                break;

                            case 6:
                                $parents_phone = "+".$parents_phone;
                                break;

                        }
                    }

                    $parentDetails = [
                        'first_name' => $crm_parent->pr_firstname,
                        'last_name' => $crm_parent->pr_lastname,
                        'mail' => $crm_parent->pr_mail,
                        'phone' => $parents_phone,
                        'dob' => $crm_parent->pr_dob,
                        'insta' => $crm_parent->pr_insta,
                        'state' => $crm_parent->pr_state,
                        'address' => $crm_parent->pr_address,
                        'st_password' => $crm_parent->pr_password,
                    ];
    
                    $this->clientRepository->createClient('Parent', $parentDetails);
                }
                $progressBar->advance();
            }
            $progressBar->finish();
            DB::commit();
            Log::info('Import parent works fine');

        } catch (Exception $e) {

            DB::rollBack();
            Log::warning('Import parent failed : '.$e->getMessage());

        }

        return Command::SUCCESS;
    }

    private function getValueWithoutSpace($value)
    {
        return $value == "" || $value == "-" || $value == "0000-00-00" || $value == 'N/A' ? NULL : $value;
    }
}
