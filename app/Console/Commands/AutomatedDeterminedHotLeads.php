<?php

namespace App\Console\Commands;

use App\Models\InitialProgram;
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
        # get raw data by the oldest client
        $rawData = DB::table('client_lead')->orderBy('id', 'asc')->get();
        foreach ($rawData as $clientData) {

            if ($clientData->id != 23)
                continue;
            
            $this->info($clientData->name);
            $isFunding = $clientData->is_funding;
            $schoolCategorization = $clientData->school_categorization;
            $gradeCategorization = $clientData->grade_categorization;
            $countryCategorization = $clientData->country_categorization;
            $majorCategorization = $clientData->major_categorization;
            $type = $clientData->type; # existing client (new, existing mentee, existing non mentee)
            $weight_attribute_name = "weight_".$type;


            $initialPrograms = InitialProgram::orderBy('id', 'asc')->get();
            foreach ($initialPrograms as $initialProgram) {
                $initProgramId = $initialProgram->id;
                $initProgramName = $initialProgram->name;
    
                $programBuckets = DB::table('tbl_program_buckets_params')->
                                        leftJoin('tbl_param_lead', 'tbl_param_lead.id', '=', 'tbl_program_buckets_params.param_id')->
                                        where('tbl_program_buckets_params.initialprogram_id', $initProgramId)->
                                        where('tbl_param_lead.value', 1)->
                                        orderBy('tbl_program_buckets_params.id', 'asc')->get();
    
                foreach ($programBuckets as $programBucket) {
                    $programBucketId = $programBucket->bucket_id;
                    $paramName = $programBucket->name;
                    $weight = $programBucket->{$weight_attribute_name};

                    switch ($paramName) {
                        case "School" :
                            $field = "school_categorization";
                            break;

                        case "Grade" :
                            $field = "grade_categorization";
                            break;

                        case "Destination_country" :
                            $field = "country_categorization";
                            break;

                        case "Status" :

                            break;

                        case "Major" :
                            $field = "major_categorization";
                            break;

                        case "Priority" :

                            break;

                        case "Seasonal" :

                            break;

                        case "Already_joined" :

                            break;
                    }

                    $value_of_field = $clientData->{$field};

                    # find value from library
                    $value_from_library = DB::table('tbl_program_lead_library')->
                                    where('programbucket_id', $programBucketId)->
                                    where('value_category', $value_of_field)->
                                    pluck($type)->
                                    first();

                    $sub_result = ($weight/100) * $value_from_library;

                    $this->info($initProgramName.' dengan param : '.$paramName.' menghasilkan : '.$value_from_library. ' in percent : '.$sub_result.'%');
                    
    
                }
    
            }
        }


        return Command::SUCCESS;
    }
}
