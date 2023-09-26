<?php

namespace App\Console\Commands;

use App\Interfaces\ClientLeadRepositoryInterface;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Models\ClientLeadTracking;
use App\Models\ClientProgram;
use App\Models\InitialProgram;
use App\Models\ProgramLeadLibrary;
use App\Models\SeasonalProgram;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomatedDeterminedHotLeads extends Command
{
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ClientLeadRepositoryInterface $clientLeadRepository;
    use CreateCustomPrimaryKeyTrait;

    public function __construct(
        ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository,
        ClientLeadRepositoryInterface $clientLeadRepository
        )
    {
        parent::__construct();
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->clientLeadRepository = $clientLeadRepository;
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
        $rawData = $this->clientLeadRepository->getAllClientLeads();
        $progressBar = $this->output->createProgressBar($rawData->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            # initialize global variables
            $recalculate = false; # it is boolean (true if we assumed the system has already run once, false otherwise)
            $triggerUpdate = false;
            $newClient = false; # become true when the client don't have lead tracking

            foreach ($rawData as $client) {

                if ($client->active == 0)
                    continue;

                # if the client has already graduated
                # then no need to calculate hot leads
                $bypass = $client->grade > 0 ? true : false;

                # initialize client variables
                $type = $client->type; # existing client (new, existing mentee, existing non mentee)
                $weight_attribute_name = "weight_" . $type;

                //? $spesificConcerns = DB::table('tbl_interest_prog')->leftjoin('tbl_prog', 'tbl_interest_prog.prog_id', '=', 'tbl_prog.prog_id')->leftjoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')->where('client_id', $clientData->id)->get();

                $leadTracking = $client->leadStatus;

                //? $leadTracking = $this->clientLeadTrackingRepository->getAllClientLeadTrackingByClientId($client->id);
                
                $newClient = $leadTracking->count() > 0 ? false : true;
    
                # this condition is to make system run every 1 April & 1 Oktober
                # 01 April & 01 Oktober
                if (date('d-m H:i') == '01-04 00:00' || date('d-m H:i') == '01-08 00:00')
                    $recalculate = true;    
    
                # currently we have 4 initial programs
                # and every client must have point on every initial programs
                # so we have to loop the initial programs
                $initialPrograms = InitialProgram::orderBy('id', 'asc')->get();

                foreach ($initialPrograms as $initialProgram) {

                    $this->info('Program : '. $initialProgram->name);

                    $last_id = ClientLeadTracking::max('group_id');
                    $group_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 5) : '00000';
                    $group_id_with_label = 'CLT-' . $this->add_digit($group_id_without_label + 1, 5);
                        
                    $initProgramId = $initialProgram->id;
                    $initProgramName = $initialProgram->name;
                    
                    $lastGroupId = $leadTracking->where('pivot.initialprogram_id', $initProgramId)->max('pivot.group_id');
                    if (!isset($lastGroupId))
                        $lastGroupId = $group_id_with_label;
                    
                        
                    $programLeadTracking = $leadTracking->where('pivot.type', 'Program')->where('pivot.group_id', $lastGroupId)->first();

                    $statusLeadTracking = $leadTracking->where('pivot.type', 'Lead')->where('pivot.group_id', $lastGroupId)->first();

                    # check if the data on tbl_client_lead_tracking doesnt have status active (1)
                    # meaning if there is no active status
                    # then it means, set trigger update  as true so the system will running for these clients
                    $triggerUpdate = $leadTracking->where('pivot.initialprogram_id', $initProgramId)->where('pivot.status', 1)->count() < 1 ? true : false;
                    
                    if ($recalculate == false && $triggerUpdate == false && $newClient == false)
                        continue;

                    # start calculate program
                    # in order to get score for each initial program which is (adm mentoring, exp learning, sat, acad)
                    $getProgramBucketDetails = $this->getProgramBucket($initialProgram, $weight_attribute_name, $client, $type, $initProgramId, $initProgramName, $group_id_with_label, $bypass);
                    $programBucketDetails = $getProgramBucketDetails['details'];
                    $programScore = $getProgramBucketDetails['program_score'];
                    
                    $this->info('--------------------------------');

                    # start calculate leads
                    $getLeadBucketDetails = $this->getLeadBucket($initialProgram, $weight_attribute_name, $client, $type, $programScore, $initProgramId, $group_id_with_label, $bypass);
                    $leadBucketDetails = $getLeadBucketDetails['details'];
                    $leadScore = $getLeadBucketDetails['lead_score'];
                    
                    
                    # store / update the data program & lead scores information
                    if ($recalculate == true) {

                        # if they haven't any lead scores
                        if (!isset($statusLeadTracking) && !isset($programLeadTracking)) {

                            $this->clientLeadTrackingRepository->createClientLeadTracking($programBucketDetails);
                            $this->clientLeadTrackingRepository->createClientLeadTracking($leadBucketDetails);

                        } else { # if they have lead scores
                            
                            # if the scores before is different with the total scores now
                            # put the scores before to inactive, which is status 0
                            # then create a new one that has active status   
                            if ($this->comparison($statusLeadTracking->pivot->total_result, $leadScore) || $this->comparison($programLeadTracking->pivot->total_result, $programScore)) {
    
    
                                # Program
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($programLeadTracking->pivot->id, ['status' => 0, 'reason_id' => 122]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($programBucketDetails);
        
                                #lead
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($statusLeadTracking->pivot->id, ['status' => 0, 'reason_id' => 122]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($leadBucketDetails);
    
                                
                            }
                        }


                    } else {

                        if ($triggerUpdate == true && $newClient == false){

                            if ($this->comparison($statusLeadTracking->pivot->total_result, $leadScore) || $this->comparison($programLeadTracking->pivot->total_result, $programScore)){

                                # Program
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($programLeadTracking->pivot->id, ['status' => 0, 'reason_id' => 123]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($programBucketDetails);
    
                                #lead
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($statusLeadTracking->pivot->id, ['status' => 0, 'reason_id' => 123]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($leadBucketDetails);                            
                            }else{
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($programLeadTracking->pivot->id, ['status' => 1, 'updated_at' => Carbon::now()]);
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($statusLeadTracking->pivot->id, ['status' => 1, 'updated_at' => Carbon::now()]);
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
            $this->info('Failed to generate hot leads : '.$e->getMessage().' | Line '.$e->getLine());
            Log::info('Cron automated determine hot leads not working normal. Error : '.$e->getMessage().' | Line '.$e->getLine());

        }



        return Command::SUCCESS;
    }

    public function getProgramBucket(
                $initialProgram,
                $weight_attribute_name,
                $client,
                $type,
                $initProgramId,
                $initProgramName,
                $group_id_with_label,
                $bypass
            )
    {
        
        $total_result = $total_potential_point = 0;
        $isFunding = $client->is_funding;

        # get client interest programs as specific concerns
        # meaning that the initial program that exist on interest programs
        # will get 1 point (assuming they are valid to join the initial program ex: admission mentoring)
        $specificConcerns = $client->interestPrograms;

        # get all params of initial program
        $programBuckets = $initialProgram->programBucketParams()->where('value', 1)->orderBy('tbl_program_buckets_params.id', 'asc')->get();
    
        foreach ($programBuckets as $programBucket) {

            $programBucketId = $programBucket->pivot->bucket_id;
            $paramName = $programBucket->name;
            $weight = $programBucket->pivot->{$weight_attribute_name};
            
            switch ($paramName) {
                case "School":
                    $field = "school_categorization";
                    $value_of_field = $client->{$field};

                    if ($isFunding != null && $isFunding == 1) {
                        switch ($client->type_school) {
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

                    $this->info($programBucketId);
                    $this->info($value_of_field);

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('programbucket_id', $programBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    break;

                case "Grade":
                    $field = "grade_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('programbucket_id', $programBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();
                                                
                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Destination_country":
                    $field = "country_categorization";
                    $value_of_field = $client->{$field};

                    # if the client has funding but haven't decide the country destination
                    # then make value of field to be 9
                    if ($isFunding != null && $isFunding == 1 && $value_of_field == 8) 
                        $value_of_field = 9; # undecided $
                    

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('programbucket_id', $programBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Status":
                    # ini berlaku utk menentukan hot warm and cold
                    # bisa dikonfirmasi kembali ke ka Hafidz
                    break;

                case "Major":
                    $field = "major_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('programbucket_id', $programBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Priority":
                    switch ($initProgramName) {
                        case "Admissions Mentoring":
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

                case "Seasonal":
                    # pertama buat view table seasonal
                    # yg isinya adalah event / program apa saja yang akan diadakan dalam 4/6 bulan ke depan
                    # lalu apabila ada seasonal program maka scorenya 1 
                    # yg dimana 1 ini akan dikalikan dengan weight nya (contoh : 10%)
                    # masukkan 10% ini ke dalam variable sub_result
                    # find value from library

                    $checkSeasonal = SeasonalProgram::withAndWhereHas('program.sub_prog.spesificConcern', function ($query) {
                                $query->where('tbl_initial_program_lead.id', 1);
                            })->
                            where(function ($query) {
                                $query->
                                    whereBetween('start', [Carbon::now()->toDateString(), Carbon::now()->addMonths(6)->toDateString()])->
                                    orWhereBetween('end', [Carbon::now()->toDateString(), Carbon::now()->addMonths(6)->toDateString()]);
                            })->
                            first();

                    # if there are any seasonal program ahead
                    # set score to 1
                    if (!is_null($checkSeasonal)) {
                        $sub_result = ($weight / 100) * 1;
                        $value_from_library = 1;
                        break;
                    } 

                    # else
                    # if there are no seasonal program ahead
                    # set score dependeing what the initial program is used
                    switch ($initProgramName) {
                        case "Admissions Mentoring":
                            $sub_result = ($weight / 100) * 1;
                            $value_from_library = 1;
                            break;

                        case "Academic Performance (Academic Tutoring)":
                            $sub_result = ($weight / 100) * 1;
                            $value_from_library = 1;
                            break;

                        default:
                            $sub_result = ($weight / 100) * 0;
                            $value_from_library = 0;  
                    }
                    
                    break;

                case "Already_joined":
                    # buat function 
                    # utk melakukan pengecekan berdasarkan initial program dan initial sub program
                    # (contoh : sedang melakukan pengecekan di program Experiential Learning
                    # maka, gunakan id initial program dan cari melalui init sub program utk mendapatkan
                    # client program yg memiliki sub program tsb dari tbl_init_prog_sub.
                    # jika count > 0 maka asumsikan sudah pernah join maka beri nilai 0 > bisa dikonfirmasi ke ka Hafidz lagi 
                    # apakah yg sudah pernah join diberi nilai 0 apa 1

                    $subprog_id = $initialProgram->sub_prog()->pluck('tbl_sub_prog.id')->toArray();

                    $joined = ClientProgram::whereHas('program.sub_prog', function ($query) use ($subprog_id) {
                                $query->whereIn('tbl_sub_prog.id', $subprog_id);
                            })->
                            select(DB::raw('COUNT(*) as count'))->
                            pluck('count');

                    $sub_result = ($weight / 100) * 1;
                    $value_from_library = 1;

                    if ($joined->first() > 0) {
                        $sub_result = ($weight / 100) * 0;
                        $value_from_library = 0;
                    }

                    $this->info('already_joined : '.$sub_result);
                    break;
            }


            $total_result += $sub_result;
            $total_potential_point += $potential_point;

            switch ($initProgramName) {
                case "Admissions Mentoring":
                    $specificConcerns->where('main_prog_id', 1)->first() != null ? $total_result = 1 : null;
                    break;

                case "Experiential Learning":
                    $specificConcerns->where('main_prog_id', 2)->first() != null ? $total_result = 0.95 : null;
                    break;

                case "Academic Performance (SAT)":
                    // join tbl sub prog -> where sub prog name like SAT%
                    $specificConcerns->where('tbl_sub_prog.sub_prog_name', 'like', 'SAT%')->count() > 0 ? $total_result = 0.9 : null;
                    break;

                case "Academic Performance (Academic Tutoring)":
                    // join tbl sub prog -> where sub prog name = Academic Tutoring
                    $specificConcerns->where('tbl_sub_prog.sub_prog_name', 'Academic Tutoring')->first() != null ? $total_result = 0.85 : null;
                    break;
            }

            $total_result = $bypass === true ? 0 : $total_result;

            $programScore = $total_result;

        }

        return [
            'details' => [
                'group_id' => $group_id_with_label,
                'client_id' => $client->id,
                'initialprogram_id' => $initProgramId,
                'type' => 'Program',
                'total_result' => $total_result,
                'potential_point' => $total_potential_point,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            'program_score' => $programScore
        ];
    }

    public function getLeadBucket(
                $initialProgram,
                $weight_attribute_name,
                $client,
                $type,
                $programScore,
                $initProgramId,
                $group_id_with_label,
                $bypass
            )
    {
        # Check Lead
        $leadBuckets = $initialProgram->leadBucketParams()->where('value', 1)->orderBy('tbl_lead_bucket_params.id', 'asc')->get();
        
        $total_result = $total_potential_point = 0;
        foreach ($leadBuckets as $leadBucket) {
            $leadBucketId = $leadBucket->pivot->bucket_id;
            $paramName = $leadBucket->name;
            $weight = $leadBucket->pivot->{$weight_attribute_name};

            switch ($paramName) {
                case "School":
                    $field = "school_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $leadBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();
                            
                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Grade":
                    $field = "grade_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $leadBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Destination_country":
                    $field = "country_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $leadBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Status":
                    # ini berlaku utk menentukan hot warm and cold
                    # bisa dikonfirmasi kembali ke ka Hafidz
                    $field = "tbl_status_categorization_lead";

                    switch ($client->register_as) {
                        default:
                        case 'student':
                            $value_of_field = 1; # Student
                            break;
                        case 'parent':
                            $value_of_field = 2; # Parent
                            break;
                        
                    }

                    $this->info($leadBucketId);

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $leadBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = ($weight / 100) * $value_from_library;
                    $this->info("status : ".$sub_result);
                    break;

                case "Major":
                    $field = "major_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $leadBucketId)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = ($weight / 100) * $value_from_library;
                    $this->info("major : ".$sub_result);
                    break;
            }

            $total_result += $sub_result / 2;
            $total_potential_point += $potential_point / 2;

            if ($programScore <= 0.34) {
                $total_result = 0;
            } else if ($programScore >= 0.35 && $client->lead_source == 'Referral') {
                $total_result = 1;
            }

            $total_result = $bypass === true ? 0 : $total_result;
            $leadScore = $total_result;

        }

        return [
            'details' => [
                'group_id' => $group_id_with_label,
                'client_id' => $client->id,
                'initialprogram_id' => $initProgramId,
                'type' => 'Lead',
                'total_result' => $total_result,
                'potential_point' => $total_potential_point,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            'lead_score' => $leadScore
        ];
    }

    public function comparison($a, $b)
    {
        if ($a == 0 || $b == 0) {

            if (abs(($a - $b)) == 0) {
                return false;
            }
                
            return true;
            
        }

        if (abs(($a - $b) / $b) < 0.00001) {
            return false;
        }

        return true;
    }
}
