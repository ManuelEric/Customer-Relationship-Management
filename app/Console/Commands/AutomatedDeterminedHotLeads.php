<?php

namespace App\Console\Commands;

use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Models\ClientLeadTracking;
use App\Models\InitialProgram;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomatedDeterminedHotLeads extends Command
{
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    use CreateCustomPrimaryKeyTrait;

    public function __construct(ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository)
    {
        parent::__construct();
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automate:determine_hot_leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic gave suggestion program for the clients and gave status hot, warm, cold.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Cron automated determine hot leads works fine.');

        # get raw data by the oldest client
        $rawData = DB::table('client_lead')->orderBy('id', 'asc')->get();
        $progressBar = $this->output->createProgressBar($rawData->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            $recalculate = false;
            $triggerUpdate = false;
            $newClient = false;
            foreach ($rawData as $clientData) {
    
                $spesificConcerns = DB::table('tbl_interest_prog')->leftjoin('tbl_prog', 'tbl_interest_prog.prog_id', '=', 'tbl_prog.prog_id')->leftjoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')->where('client_id', $clientData->id)->get();
                $leadTracking = $this->clientLeadTrackingRepository->getAllClientLeadTrackingByClientId($clientData->id);
                
                $newClient = $leadTracking->count() > 0 ? false : true;
    
                // 01 April & 01 Oktober
                if (date('d-m') == '01-04' || date('d-m') == '01-08') {
                    $recalculate = true;
                }    
    
                $this->info($clientData->name);
                $this->info($clientData->id);
                $isFunding = $clientData->is_funding;
                $schoolCategorization = $clientData->school_categorization;
                $gradeCategorization = $clientData->grade_categorization;
                $countryCategorization = $clientData->country_categorization;
                $majorCategorization = $clientData->major_categorization;
                $type = $clientData->type; # existing client (new, existing mentee, existing non mentee)
                $weight_attribute_name = "weight_" . $type;
    
    
                $initialPrograms = InitialProgram::orderBy('id', 'asc')->get();
                foreach ($initialPrograms as $initialProgram) {

                    $last_id = ClientLeadTracking::max('group_id');
                    $group_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 5) : '00000';
                    $group_id_with_label = 'CLT-' . $this->add_digit($group_id_without_label + 1, 5);
                        
                    $initProgramId = $initialProgram->id;
                    $triggerUpdate = $leadTracking->where('initialprogram_id', $initProgramId)->where('status', 1)->count() < 1 ? true : false;
                    
                    $initProgramName = $initialProgram->name;
    
                    $lastGroupId = $leadTracking->where('initialprogram_id', $initProgramId)->max('group_id');

                    $programLeadTracking = $leadTracking->where('type', 'Program')->where('group_id', $lastGroupId)->first();
                    $statusLeadTracking = $leadTracking->where('type', 'Lead')->where('group_id', $lastGroupId)->first();

                    # Check Program
                    $programBuckets = DB::table('tbl_program_buckets_params')->leftJoin('tbl_param_lead', 'tbl_param_lead.id', '=', 'tbl_program_buckets_params.param_id')->where('tbl_program_buckets_params.initialprogram_id', $initProgramId)->where('tbl_param_lead.value', 1)->orderBy('tbl_program_buckets_params.id', 'asc')->get();
    
                    $total_result = 0;
    
                    if ($recalculate == false && $triggerUpdate == false && $newClient == false){
                        continue;
                    }
    
    
                    foreach ($programBuckets as $programBucket) {
                        $programBucketId = $programBucket->bucket_id;
                        $paramName = $programBucket->name;
                        $weight = $programBucket->{$weight_attribute_name};
    
                        switch ($paramName) {
                            case "School":
                                $field = "school_categorization";
                                $value_of_field = $clientData->{$field};
    
                                if ($clientData->is_funding != null && $clientData->is_funding == 1) {
                                    switch ($clientData->type_school) {
                                        case 'Home Schooling':
                                            $value_of_field = 4;
                                            break;
                                        case 'National Private':
                                            $value_of_field = 6;
                                            break;
                                        case 'National':
                                            $value_of_field = 8;
                                            break;
                                    }
                                }
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('programbucket_id', $programBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
    
                            case "Grade":
                                $field = "grade_categorization";
                                $value_of_field = $clientData->{$field};
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('programbucket_id', $programBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
    
                            case "Destination_country":
                                $field = "country_categorization";
                                $value_of_field = $clientData->{$field};
    
                                if ($clientData->is_funding != null && $clientData->is_funding == 1 && $value_of_field == 8) {
                                    $value_of_field = 9;
                                }
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('programbucket_id', $programBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
    
                            case "Status":
                                # ini berlaku utk menentukan hot warm and cold
                                # bisa dikonfirmasi kembali ke ka Hafidz
                                break;
    
                            case "Major":
                                $field = "major_categorization";
                                $value_of_field = $clientData->{$field};
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('programbucket_id', $programBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
    
                            case "Priority":
                                switch ($initProgramName) {
                                    case "Admission Mentoring":
                                        $value_from_library = 1;
                                        $sub_result = ($weight / 100) * 1;
                                        break;
    
                                    case "Experiential Learning":
                                        $value_from_library = 0.75;
                                        $sub_result = ($weight / 100) * 0.75;
                                        break;
    
                                    case "Academic Performance (SAT)":
                                        $value_from_library = 0.50;
                                        $sub_result = ($weight / 100) * 0.50;
                                        break;
    
                                    case "Academic Performance (Academic Tutoring)":
                                        $value_from_library = 0.25;
                                        $sub_result = ($weight / 100) * 0.25;
                                        break;
                                }
                                break;
    
                            // case "Seasonal":
                            //     # pertama buat view table seasonal
                            //     # yg isinya adalah event / program apa saja yang akan diadakan dalam 4/6 bulan ke depan
                            //     # lalu apabila ada seasonal program maka scorenya 1 
                            //     # yg dimana 1 ini akan dikalikan dengan weight nya (contoh : 10%)
                            //     # masukkan 10% ini ke dalam variable sub_result
                            //     # find value from library
    
    
                            //     $checkSeasonal = DB::table('tbl_seasonal_lead')->where('initialprogram_id', $initProgramId)->whereBetween(
                            //         'start',
                            //         [Carbon::now(), Carbon::now()->addMonths(6)->toDateString()]
                            //     )->first();
    
                            //     $sub_result = ($weight / 100) * 0;
                            //     $value_from_library = 0;
    
                            //     if (isset($checkSeasonal)) {
                            //         $sub_result = ($weight / 100) * 1;
                            //         $value_from_library = 1;
                            //     } else {
                            //         switch ($initProgramName) {
                            //             case "Admission Mentoring":
                            //                 $sub_result = ($weight / 100) * 1;
                            //                 $value_from_library = 1;
                            //                 break;
    
                            //             case "Academic Performance (Academic Tutoring)":
                            //                 $sub_result = ($weight / 100) * 1;
                            //                 $value_from_library = 1;
                            //                 break;
                            //         }
                            //         break;
                            //     }
                            //     break;
    
                            case "Already_joined":
                                # buat function 
                                # utk melakukan pengecekan berdasarkan initial program dan initial sub program
                                # (contoh : sedang melakukan pengecekan di program Experiential Learning
                                # maka, gunakan id initial program dan cari melalui init sub program utk mendapatkan
                                # client program yg memiliki sub program tsb dari tbl_init_prog_sub.
                                # jika count > 0 maka asumsikan sudah pernah join maka beri nilai 0 > bisa dikonfirmasi ke ka Hafidz lagi 
                                # apakah yg sudah pernah join diberi nilai 0 apa 1
    
                                $subprog_id = DB::table('tbl_initial_program_lead')->select('tbl_sub_prog.id as sub_id')
                                    ->join('tbl_initial_prog_sub_lead', 'tbl_initial_prog_sub_lead.initialprogram_id', '=', 'tbl_initial_program_lead.id')
                                    ->join('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_initial_prog_sub_lead.subprogram_id')
                                    ->where('tbl_initial_program_lead.id', $initProgramId)->get()->pluck('sub_id');
    
                                $joined = DB::table('tbl_client_prog')->select(DB::raw('COUNT(*) as count'))
                                    ->join('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
                                    ->join('tbl_sub_prog', 'tbl_prog.sub_prog_id', '=', 'tbl_sub_prog.id')
                                    ->where('tbl_client_prog.client_id', $clientData->id)
                                    ->where('tbl_client_prog.status', 1)
                                    ->whereIn('tbl_sub_prog.id', $subprog_id)->get()->pluck('count');
    
                                $sub_result = ($weight / 100) * 1;
                                $value_from_library = 1;
    
                                if ($joined->first() > 0) {
                                    $sub_result = ($weight / 100) * 0;
                                    $value_from_library = 0;
                                }
    
                                break;
                        }
    
    
                        $total_result += $sub_result;
    
                        switch ($initProgramName) {
                            case "Admission Mentoring":
                                $spesificConcerns->where('main_prog_id', 1)->first() != null ? $total_result = 1 : null;
                                break;
    
                            case "Experiential Learning":
                                $spesificConcerns->where('main_prog_id', 2)->first() != null ? $total_result = 0.95 : null;
                                break;
    
                            case "Academic Performance (SAT)":
                                // join tbl sub prog -> where sub prog name like SAT%
                                $spesificConcerns->where('tbl_sub_prog.sub_prog_name', 'like', 'SAT%')->count() > 0 ? $total_result = 0.9 : null;
                                break;
    
                            case "Academic Performance (Academic Tutoring)":
                                // join tbl sub prog -> where sub prog name = Academic Tutoring
                                $spesificConcerns->where('tbl_sub_prog.sub_prog_name', 'Academic Tutoring')->first() != null ? $total_result = 0.85 : null;
                                break;
                        }
    
                        $programScore = $total_result;
    
                        // $this->info($initProgramName . ' dengan param : ' . $paramName . ' menghasilkan : ' . $value_from_library . ' in percent : ' . $sub_result . '%');
                    }
                    // $this->info('Total dari program : ' . $initProgramName . ' menghasilkan score : ' . $total_result);
                    // $this->info('');
    
                    // $this->info('============= Lead ==========');
    
                    $programBucketDetails = [
                        'group_id' => $group_id_with_label,
                        'client_id' => $clientData->id,
                        'initialprogram_id' => $initProgramId,
                        'type' => 'Program',
                        'total_result' => $total_result,
                        'status' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
    
                    # end check program
    
                    # Check Lead
                    $leadBuckets = DB::table('tbl_lead_bucket_params')->leftJoin('tbl_param_lead', 'tbl_param_lead.id', '=', 'tbl_lead_bucket_params.param_id')->where('tbl_lead_bucket_params.initialprogram_id', $initProgramId)->where('tbl_param_lead.value', 1)->orderBy('tbl_lead_bucket_params.id', 'asc')->get();
    
                    $total_result = 0;
                    foreach ($leadBuckets as $leadBucket) {
                        $leadBucketId = $leadBucket->bucket_id;
                        $paramName = $leadBucket->name;
                        $weight = $leadBucket->{$weight_attribute_name};
    
                        switch ($paramName) {
                            case "School":
                                $field = "school_categorization";
                                $value_of_field = $clientData->{$field};
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('leadbucket_id', $leadBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
    
                            case "Grade":
                                $field = "grade_categorization";
                                $value_of_field = $clientData->{$field};
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('leadbucket_id', $leadBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
    
                            case "Destination_country":
                                $field = "country_categorization";
                                $value_of_field = $clientData->{$field};
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('leadbucket_id', $leadBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
    
                            case "Status":
                                # ini berlaku utk menentukan hot warm and cold
                                # bisa dikonfirmasi kembali ke ka Hafidz
                                $field = "tbl_status_categorization_lead";
    
                                $value_of_field = 2;
                                if (isset($clientData->register_as)) {
    
                                    switch ($clientData->register_as) {
                                        case 'student':
                                            $value_of_field = 2; # Student
                                            break;
                                        case 'parent':
                                            $value_of_field = 1; # Parent
                                            break;
                                    }
                                }
    
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('leadbucket_id', $leadBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
    
                            case "Major":
                                $field = "major_categorization";
                                $value_of_field = $clientData->{$field};
    
                                # find value from library
                                $value_from_library = DB::table('tbl_program_lead_library')->where('leadbucket_id', $leadBucketId)->where('value_category', $value_of_field)->pluck($type)->first();
    
                                $sub_result = ($weight / 100) * $value_from_library;
                                break;
                        }
    
                        $total_result += $sub_result / 2;
    
                        if ($programScore <= 0.34) {
                            $total_result = 0;
                        } else if ($programScore >= 0.35 && $clientData->lead_source == 'Referral') {
                            $total_result = 1;
                        }
    
                        $leadScore = $total_result;
    
                        // $this->info($initProgramName . ' dengan param : ' . $paramName . ' menghasilkan : ' . $value_from_library . ' in percent : ' . $sub_result . '%');
                    }
    
                    $leadBucketDetails = [
                        'group_id' => $group_id_with_label,
                        'client_id' => $clientData->id,
                        'initialprogram_id' => $initProgramId,
                        'type' => 'Lead',
                        'total_result' => $total_result,
                        'status' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
    
                    # end check Lead
    
                    $this->info($programLeadTracking);
                    if ($recalculate == true) {
                        if ($this->comparison($statusLeadTracking->total_result, $leadScore) || $this->comparison($programLeadTracking->total_result, $programScore)) {
    
                            # Program
                            $this->clientLeadTrackingRepository->updateClientLeadTrackingById($programLeadTracking->id, ['status' => 0, 'reason_id' => 122]);
                            $this->clientLeadTrackingRepository->createClientLeadTracking($programBucketDetails);
    
                            #lead
                            $this->clientLeadTrackingRepository->updateClientLeadTrackingById($statusLeadTracking->id, ['status' => 0, 'reason_id' => 122]);
                            $this->clientLeadTrackingRepository->createClientLeadTracking($leadBucketDetails);
                        }
                    } else {
                        if($triggerUpdate == true && $newClient == false){
                            
                            if($this->comparison($statusLeadTracking->total_result, $leadScore) || $this->comparison($programLeadTracking->total_result, $programScore)){
                                
                                # Program
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($programLeadTracking->id, ['status' => 0, 'reason_id' => 123]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($programBucketDetails);
    
                                #lead
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($statusLeadTracking->id, ['status' => 0, 'reason_id' => 123]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($leadBucketDetails);                            
                            }else{
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($programLeadTracking->id, ['status' => 1, 'updated_at' => Carbon::now()]);
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($statusLeadTracking->id, ['status' => 1, 'updated_at' => Carbon::now()]);
                            }
                        }else{
                            $this->clientLeadTrackingRepository->createClientLeadTracking($programBucketDetails);
                            $this->clientLeadTrackingRepository->createClientLeadTracking($leadBucketDetails);
                        }
                    }
                }

                $progressBar->advance();
            }
            $progressBar->finish();

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            $this->info($e->getMessage().' | Line '.$e->getLine());
            Log::info('Cron automated determine hot leads not working normal. Error : '.$e->getMessage().' | Line '.$e->getLine());

        }



        return Command::SUCCESS;
    }

    public function comparison($a, $b)
    {
        if ($a == 0 || $b == 0) {
            if (abs(($a - $b)) == 0) {
                return false;
            } else {
                return true;
            }
        }
        if (abs(($a - $b) / $b) < 0.00001) {
            return false;
        }
        return true;
    }
}
