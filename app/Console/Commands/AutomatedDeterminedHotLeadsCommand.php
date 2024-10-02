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
use App\Interfaces\InitialProgramRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomatedDeterminedHotLeadsCommand extends Command
{
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ClientLeadRepositoryInterface $clientLeadRepository;
    private InitialProgramRepositoryInterface $initialProgramRepository;
    use CreateCustomPrimaryKeyTrait;

    public function __construct(
        ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository,
        ClientLeadRepositoryInterface $clientLeadRepository,
        InitialProgramRepositoryInterface $initialProgramRepository
        )
    {
        parent::__construct();
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->clientLeadRepository = $clientLeadRepository;
        $this->initialProgramRepository = $initialProgramRepository;
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

    # Purpose:
    # This function get data client and calculate hot lead
    # Calculate based on school, grade, destination country, status, major, priority, seasonal and already joined

    # Outcome:
    # The function with update tbl_client_lead_tracking with result data from calculate hot lead
    public function handle()
    {
        Log::info('Cron automated determine hot leads works fine.');

        # get raw data by the oldest client
        $raw_data = $this->clientLeadRepository->getAllClientLeads();
        $progress_bar = $this->output->createProgressBar($raw_data->count());
        $progress_bar->start();

        DB::beginTransaction();
        try {

            # initialize global variables
            $recalculate = false; # it is boolean (true if we assumed the system has already run once, false otherwise)
            $trigger_update = false;
            $new_client = false; # become true when the client don't have lead tracking

            foreach ($raw_data as $client) {

                if ($client->active == 0)
                    continue;

                # if the client has already graduated
                # then no need to calculate hot leads
                # this grade meaning the difference between her/his actual grade and graduation year
                $bypass = $client->grade > 0 || $client->phone == null ? true : false;

                # initialize client variables
                $type = $client->type; # existing client (new, existing mentee, existing non mentee)
                $weight_attribute_name = "weight_" . $type;

                $lead_tracking = $client->leadStatus;
                
                $new_client = $lead_tracking->count() > 0 ? false : true;
    
                # this condition is to make system run every 1 April & 1 Oktober
                # 01 April & 01 Oktober
                if (date('d-m H:i') == '01-04 00:00' || date('d-m H:i') == '01-08 00:00')
                    $recalculate = true;    
    
                # currently we have 4 initial programs
                # and every client must have point on every initial programs
                # so we have to loop the initial programs
                $initial_programs = $this->initialProgramRepository->getAllInitProg();

                foreach ($initial_programs as $initial_program) {

                    $this->info('Program : '. $initial_program->name);

                    $last_id = ClientLeadTracking::max('group_id');
                    $group_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 5) : '00000';
                    $group_id_with_label = 'CLT-' . $this->add_digit($group_id_without_label + 1, 5);
                        
                    $init_program_id = $initial_program->id;
                    $init_program_name = $initial_program->name;
                    
                    $last_group_id = $lead_tracking->where('pivot.initialprogram_id', $init_program_id)->max('pivot.group_id');
                    if (!isset($last_group_id))
                        $last_group_id = $group_id_with_label;
                    
                        
                    $program_lead_tracking = $lead_tracking->where('pivot.type', 'Program')->where('pivot.group_id', $last_group_id)->first();

                    $status_lead_tracking = $lead_tracking->where('pivot.type', 'Lead')->where('pivot.group_id', $last_group_id)->first();

                    # check if the data on tbl_client_lead_tracking doesnt have status active (1) of each initial program
                    # meaning if there is no active status
                    # then it means, set trigger update as true so the system will running for these clients
                    $trigger_update = $lead_tracking->where('pivot.initialprogram_id', $init_program_id)->where('pivot.status', 1)->count() < 1 ? true : false;
                    
                    if ($recalculate == false && $trigger_update == false && $new_client == false)
                        continue;

                    # start calculate program
                    # in order to get score for each initial program which is (adm mentoring, exp learning, sat, acad)
                    $getProgram_bucket_details = $this->cnGetProgramBucket($initial_program, $weight_attribute_name, $client, $type, $init_program_id, $init_program_name, $group_id_with_label, $bypass);
                    $program_bucket_details = $getProgram_bucket_details['details'];
                    $program_score = $getProgram_bucket_details['program_score'];
                    
                    $this->info('--------------------------------');

                    # start calculate leads
                    # in order to get score for either its hot, warm, or cold
                    $get_lead_bucketDetails = $this->cnGetLeadBucket($initial_program, $weight_attribute_name, $client, $type, $program_score, $init_program_id, $group_id_with_label, $bypass);
                    $lead_bucket_details = $get_lead_bucketDetails['details'];
                    $lead_score = $get_lead_bucketDetails['lead_score'];
                    
                    
                    # store / update the data program & lead scores information
                    if ($recalculate == true) {

                        # if they haven't any lead scores
                        if (!isset($status_lead_tracking) && !isset($program_lead_tracking)) {

                            $this->clientLeadTrackingRepository->createClientLeadTracking($program_bucket_details);
                            $this->clientLeadTrackingRepository->createClientLeadTracking($lead_bucket_details);

                        } else { # if they have lead scores
                            
                            # if the scores before is different with the total scores now
                            # put the scores before to inactive, which is status 0
                            # then create a new one that has active status   
                            if ($this->cnComparison($status_lead_tracking->pivot->total_result, $lead_score) || $this->cnComparison($program_lead_tracking->pivot->total_result, $program_score)) {
    
    
                                # Program
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($program_lead_tracking->pivot->id, ['status' => 0, 'reason_id' => 122]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($program_bucket_details);
        
                                #lead
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($status_lead_tracking->pivot->id, ['status' => 0, 'reason_id' => 122]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($lead_bucket_details);
    
                                
                            }
                        }


                    } else {

                        if ($trigger_update == true && $new_client == false){

                            if ($this->cnComparison($status_lead_tracking->pivot->total_result, $lead_score) || $this->cnComparison($program_lead_tracking->pivot->total_result, $program_score)){

                                # Program
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($program_lead_tracking->pivot->id, ['status' => 0, 'reason_id' => 123]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($program_bucket_details);
    
                                #lead
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($status_lead_tracking->pivot->id, ['status' => 0, 'reason_id' => 123]);
                                $this->clientLeadTrackingRepository->createClientLeadTracking($lead_bucket_details);                            
                            }else{
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($program_lead_tracking->pivot->id, ['status' => 1, 'updated_at' => Carbon::now()]);
                                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($status_lead_tracking->pivot->id, ['status' => 1, 'updated_at' => Carbon::now()]);
                            }
                        }else{

                            $this->clientLeadTrackingRepository->createClientLeadTracking($program_bucket_details);
                            $this->clientLeadTrackingRepository->createClientLeadTracking($lead_bucket_details);
                        }
                    }
                }

                $progress_bar->advance();
            }
            $progress_bar->finish();

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            $this->info('Failed to generate hot leads : '.$e->getMessage().' | Line '.$e->getLine());
            Log::info('Cron automated determine hot leads not working normal. Error : '.$e->getMessage().' | Line '.$e->getLine());

        }



        return Command::SUCCESS;
    }

    public function cnGetProgramBucket(
                $initial_program,
                $weight_attribute_name,
                $client,
                $type,
                $init_program_id,
                $init_program_name,
                $group_id_with_label,
                $bypass
            ): array
    {
        
        $total_result = $total_potential_point = 0;
        $is_funding = $client->is_funding;

        # get client interest programs as specific concerns
        # meaning that the initial program that exist on interest programs
        # will get 1 point (assuming they are valid to join the initial program ex: admission mentoring)
        $specific_concerns = $client->interestPrograms;

        # get all params of initial program
        $program_buckets = $initial_program->program_bucketParams()->where('value', 1)->orderBy('tbl_program_buckets_params.id', 'asc')->get();
    
        foreach ($program_buckets as $program_bucket) {

            $program_bucket_id = $program_bucket->pivot->bucket_id;
            $param_name = $program_bucket->name;
            $weight = $program_bucket->pivot->{$weight_attribute_name};
            
            switch ($param_name) {
                case "School":
                    $field = "school_categorization";
                    $value_of_field = $client->{$field};

                    if ($is_funding != null && $is_funding == 1) {
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

                    $this->info($program_bucket_id);
                    $this->info($value_of_field);

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('programbucket_id', $program_bucket_id)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    break;

                case "Grade":
                    $field = "grade_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('programbucket_id', $program_bucket_id)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();
                                                
                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Destination_country":
                    $field = "country_categorization";
                    $value_of_field = $client->{$field};

                    # if the client has funding but haven't decide the country destination
                    # then make value of field to be 9
                    if ($is_funding != null && $is_funding == 1 && $value_of_field == 8) 
                        $value_of_field = 9; # undecided $
                    

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('programbucket_id', $program_bucket_id)->
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
                                                where('programbucket_id', $program_bucket_id)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Priority":
                    switch ($init_program_name) {
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

                    $check_seasonal = SeasonalProgram::withAndWhereHas('program.sub_prog.spesificConcern', function ($query) {
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
                    if (!is_null($check_seasonal)) {
                        $sub_result = ($weight / 100) * 1;
                        $value_from_library = 1;
                        break;
                    } 

                    # else
                    # if there are no seasonal program ahead
                    # set score dependeing what the initial program is used
                    switch ($init_program_name) {
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

                    $subprog_id = $initial_program->sub_prog()->pluck('tbl_sub_prog.id')->toArray();

                    $joined = ClientProgram::whereHas('program.sub_prog', function ($query) use ($subprog_id) {
                                $query->whereIn('tbl_sub_prog.id', $subprog_id);
                            })->
                            where('client_id', $client->id)->
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

            $bypass = false;
            switch ($init_program_name) {
                case "Admissions Mentoring":
                    if ($client->type == "existing_mentee")
                        $bypass = true;
                    
                    $specific_concerns->where('main_prog_id', 1)->first() != null ? $total_result = 1 : null;
                    break;

                case "Experiential Learning":
                    $specific_concerns->where('main_prog_id', 2)->first() != null ? $total_result = 0.95 : null;
                    break;

                case "Academic Performance (SAT)":
                    # join tbl sub prog -> where sub prog name like SAT%
                    $specific_concerns->where('tbl_sub_prog.sub_prog_name', 'like', 'SAT%')->count() > 0 ? $total_result = 0.9 : null;
                    break;

                case "Academic Performance (Academic Tutoring)":
                    # join tbl sub prog -> where sub prog name = Academic Tutoring
                    $specific_concerns->where('tbl_sub_prog.sub_prog_name', 'Academic Tutoring')->first() != null ? $total_result = 0.85 : null;
                    break;
            }

            $total_result = $bypass === true ? 0 : $total_result;

            $program_score = $total_result;

        }

        return [
            'details' => [
                'group_id' => $group_id_with_label,
                'client_id' => $client->id,
                'initialprogram_id' => $init_program_id,
                'type' => 'Program',
                'total_result' => $total_result,
                'potential_point' => $total_potential_point,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            'program_score' => $program_score
        ];
    }

    public function cnGetLeadBucket(
                $initial_program,
                $weight_attribute_name,
                $client,
                $type,
                $program_score,
                $init_program_id,
                $group_id_with_label,
            ): array
    {
        # Check Lead
        $lead_buckets = $initial_program->lead_bucketParams()->where('value', 1)->orderBy('tbl_lead_bucket_params.id', 'asc')->get();
        
        $total_result = $total_potential_point = 0;
        foreach ($lead_buckets as $lead_bucket) {
            $lead_bucket_id = $lead_bucket->pivot->bucket_id;
            $param_name = $lead_bucket->name;
            $weight = $lead_bucket->pivot->{$weight_attribute_name};

            switch ($param_name) {
                case "School":
                    $field = "school_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $lead_bucket_id)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();
                            
                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Grade":
                    $field = "grade_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $lead_bucket_id)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Destination_country":
                    $field = "country_categorization";
                    $value_of_field = $client->{$field};

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $lead_bucket_id)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = $potential_point = ($weight / 100) * $value_from_library;
                    
                    break;

                case "Status":
                    # ini berlaku utk menentukan hot warm and cold
                    # bisa dikonfirmasi kembali ke ka Hafidz
                    $field = "tbl_status_categorization_lead";

                    switch ($client->register_by) {
                        default:
                        case 'student':
                            $value_of_field = 1; # Student
                            break;
                        case 'parent':
                            $value_of_field = 2; # Parent
                            break;
                        
                    }

                    $this->info($lead_bucket_id);

                    # find value from library
                    $value_from_library = ProgramLeadLibrary::
                                                where('leadbucket_id', $lead_bucket_id)->
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
                                                where('leadbucket_id', $lead_bucket_id)->
                                                where('value_category', $value_of_field)->
                                                pluck($type)->first();

                    $sub_result = ($weight / 100) * $value_from_library;
                    $this->info("major : ".$sub_result);
                    break;
            }

            $total_result += $sub_result / 2;
            $total_potential_point += $potential_point / 2;

            if ($program_score <= 0.34) {
                $total_result = 0;
            } else if ($program_score >= 0.35 && $client->lead_source == 'Referral') {
                $total_result = 1;
            }

            $lead_score = $total_result;

        }

        return [
            'details' => [
                'group_id' => $group_id_with_label,
                'client_id' => $client->id,
                'initialprogram_id' => $init_program_id,
                'type' => 'Lead',
                'total_result' => $total_result,
                'potential_point' => $total_potential_point,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            'lead_score' => $lead_score
        ];
    }

    public function cnComparison($a, $b)
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