<?php

namespace App\Console\Commands;

use App\Models\InitialProgram;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutomatedDeterminedHotLeads extends Command
{
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
        // TODO: Jika is funding true maka, jika sekolahnya homeschool maka pilih homeschool$

        # get raw data by the oldest client
        $rawData = DB::table('client_lead')->orderBy('id', 'asc')->get();
        foreach ($rawData as $clientData) {

            $leadTracking = DB::table('tbl_client_lead_tracking')->where('client_id', $clientData->id)->where('status', 1)->get();

            if ($leadTracking->count() > 0)
                continue;

            $this->info($clientData->name);
            $isFunding = $clientData->is_funding;
            $schoolCategorization = $clientData->school_categorization;
            $gradeCategorization = $clientData->grade_categorization;
            $countryCategorization = $clientData->country_categorization;
            $majorCategorization = $clientData->major_categorization;
            $type = $clientData->type; # existing client (new, existing mentee, existing non mentee)
            $weight_attribute_name = "weight_" . $type;


            $initialPrograms = InitialProgram::orderBy('id', 'asc')->get();
            foreach ($initialPrograms as $initialProgram) {
                $initProgramId = $initialProgram->id;
                $initProgramName = $initialProgram->name;

                # Check Program
                $programBuckets = DB::table('tbl_program_buckets_params')->leftJoin('tbl_param_lead', 'tbl_param_lead.id', '=', 'tbl_program_buckets_params.param_id')->where('tbl_program_buckets_params.initialprogram_id', $initProgramId)->where('tbl_param_lead.value', 1)->orderBy('tbl_program_buckets_params.id', 'asc')->get();

                $total_result = 0;
                foreach ($programBuckets as $programBucket) {
                    $programBucketId = $programBucket->bucket_id;
                    $paramName = $programBucket->name;
                    $weight = $programBucket->{$weight_attribute_name};

                    switch ($paramName) {
                        case "School":
                            $field = "school_categorization";
                            $value_of_field = $clientData->{$field};

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

                        case "Seasonal":
                            # pertama buat view table seasonal
                            # yg isinya adalah event / program apa saja yang akan diadakan dalam 4/6 bulan ke depan
                            # lalu apabila ada seasonal program maka scorenya 1 
                            # yg dimana 1 ini akan dikalikan dengan weight nya (contoh : 10%)
                            # masukkan 10% ini ke dalam variable sub_result
                            # find value from library


                            $checkSeasonal = DB::table('tbl_seasonal_lead')->where('initialprogram_id', $initProgramId)->whereBetween(
                                'start',
                                [Carbon::now(), Carbon::now()->addMonths(6)->toDateString()]
                            )->first();

                            $sub_result = ($weight / 100) * 0;
                            $value_from_library = 0;

                            if (isset($checkSeasonal)) {
                                $sub_result = ($weight / 100) * 1;
                                $value_from_library = 1;
                            }else{
                                switch ($initProgramName) {
                                    case "Admission Mentoring":
                                        $sub_result = ($weight / 100) * 1;
                                        $value_from_library = 1;
                                        break;
                                        
                                    case "Academic Performance (Academic Tutoring)":
                                        $sub_result = ($weight / 100) * 1;
                                        $value_from_library = 1;
                                    break;
                                }
                                break;
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

                    $this->info($initProgramName . ' dengan param : ' . $paramName . ' menghasilkan : ' . $value_from_library . ' in percent : ' . $sub_result . '%');
                }

                $this->info('Total dari program : ' . $initProgramName . ' menghasilkan score : ' . $total_result);
                $this->info('');

                $this->info('============= Lead ==========');

                $programBucketDetails = [
                    'client_id' => $clientData->id,
                    'initialprogram_id' => $initProgramId,
                    'type' => 'Program',
                    'total_result' => $total_result,
                    'status' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                // $program_tracking = DB::table('tbl_client_lead_tracking')->insert($programBucketDetails);
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
                            
                            switch ($clientData->register_as) {
                                case 'student':
                                    $value_of_field = 2; # Student
                                    break;
                                case 'parent':
                                    $value_of_field = 1; # Parent
                                    break;
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

                    $this->info($initProgramName . ' dengan param : ' . $paramName . ' menghasilkan : ' . $value_from_library . ' in percent : ' . $sub_result . '%');
                }

                $this->info('Total dari program : ' . $initProgramName . ' menghasilkan score : ' . $total_result);
                $this->info('');

                $leadBucketDetails = [
                    'client_id' => $clientData->id,
                    'initialprogram_id' => $initProgramId,
                    'type' => 'Lead',
                    'total_result' => $total_result,
                    'status' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                // $lead_tracking = DB::table('tbl_client_lead_tracking')->insert($leadBucketDetails);
                # end check Lead

            }
        }


        return Command::SUCCESS;
    }

    public function getValueFromLibrary($libDetails)
    {
    }
}
