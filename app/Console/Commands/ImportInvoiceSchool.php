<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportInvoiceSchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:invoice_school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import invoice school from big data v1 to invb2b big data v2';

    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, SchoolProgramRepositoryInterface $schoolProgramRepository)
    {
        parent::__construct();

        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoiceSchools = $this->invoiceB2bRepository->getAllInvoiceSchoolFromCRM();
        $progressBar = $this->output->createProgressBar($invoiceSchools->count());
        $progressBar->start();
        $new_invoiceSchools = [];
        DB::beginTransaction();
        try {

            foreach ($invoiceSchools as $invSch) {
                $this->info($invSch->invsch_id);
                $this->info(' dan ');
                $invoiceV2 = $this->invoiceB2bRepository->getInvoiceB2bByInvId($invSch->invsch_id);
                $this->info(json_encode($invoiceV2));
                // $this->info('');
                if (count($invoiceV2) == 0) {
                    $this->info('masuk dan schprog_id : '.$invSch->schprog_id);
                    $this->info(' school prog : '.json_encode($this->schoolProgramRepository->getSchoolProgramById($invSch->schprog_id)));
                    $this->info('');
                    if (!$schoolProgCheck = $this->schoolProgramRepository->getSchoolProgramById($invSch->schprog_id))
                        continue;
    
                    $new_invoiceSchools[] = $array = [
                        'invb2b_num' => $invSch->invsch_num,
                        'invb2b_id' => $invSch->invsch_id,
                        'schprog_id' => $invSch->schprog_id,
                        'partnerprog_id' => null,
                        'ref_id' => null,
                        'invb2b_priceidr' => $invSch->invsch_price,
                        'invb2b_participants' => $invSch->invsch_participants,
                        'invb2b_disc' => null,
                        'invb2b_discidr' => $invSch->invsch_disc == 0 ? null : $invSch->invsch_disc,
                        'invb2b_totprice' => null,
                        'invb2b_totpriceidr' => $invSch->invsch_totprice,
                        'invb2b_words' => null,
                        'invb2b_wordsidr' => $invSch->invsch_words,
                        'invb2b_date' => $invSch->invsch_date,
                        'invb2b_duedate' => $invSch->invsch_duedate,
                        'invb2b_pm' => $invSch->invsch_pm == '' ? 'Full Payment' : $invSch->invsch_pm,
                        'invb2b_notes' => $invSch->invsch_notes == '' ? null : $invSch->invsch_notes,
                        'invb2b_tnc' => $invSch->invsch_tnc == '' ? null : $invSch->invsch_tnc,
                        'invb2b_status' => 1,
                        'curs_rate' => null,
                        'currency' => 'idr',
                        'is_full_amount' => (($invSch->invsch_price - $invSch->invsch_disc) == $invSch->invsch_totprice ? 1 : 0),
                        'created_at' => $invSch->invsch_date,
                        'updated_at' => $invSch->invsch_date,
                    ];
                    $this->invoiceB2bRepository->createInvoiceB2b($array);
                }
                $progressBar->advance();
                
            }
            $progressBar->finish();

            // if (count($new_invoiceSchools) > 0) {
            //     $this->invoiceB2bRepository->insertInvoiceB2b($new_invoiceSchools);
            // }
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $this->info($e->getMessage().' on Line '.$e->getLine());
        }


        return Command::SUCCESS;
    }
}
