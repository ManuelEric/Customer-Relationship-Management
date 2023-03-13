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
            foreach ($crm_parents as $crm_parent)
            {
                $parentName = $crm_parent->pr_firstname.' '.$crm_parent->pr_lastname;
                if (!$parent = $this->clientRepository->getParentByParentName($parentName))
                {
                    $parentDetails = [
                        'first_name' => $crm_parent->pr_firstname,
                        'last_name' => $crm_parent->pr_lastname,
                        'mail' => $crm_parent->pr_mail,
                        'phone' => $crm_parent->pr_phone,
                        'dob' => $crm_parent->pr_dob,
                        'insta' => $crm_parent->pr_insta,
                        'state' => $crm_parent->pr_state,
                        'address' => $crm_parent->pr_address,
                        'st_password' => $crm_parent->pr_password,
                    ];
    
                    $this->clientRepository->createClient('Parent', $parentDetails);
                }
            }
            DB::commit();
            Log::info('Import parent works fine');

        } catch (Exception $e) {

            DB::rollBack();
            Log::warning('Import parent failed : '.$e->getMessage());

        }

        return Command::SUCCESS;
    }
}
