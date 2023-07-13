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
    
                $total_result = 0;
                foreach ($programBuckets as $programBucket) {
                    $programBucketId = $programBucket->bucket_id;
                    $paramName = $programBucket->name;
                    $weight = $programBucket->{$weight_attribute_name};

                    switch ($paramName) {
                        case "School" :
                            $field = "school_categorization";
                            $value_of_field = $clientData->{$field};

                            # find value from library
                            $value_from_library = DB::table('tbl_program_lead_library')->
                                            where('programbucket_id', $programBucketId)->
                                            where('value_category', $value_of_field)->
                                            pluck($type)->
                                            first();

                            $sub_result = ($weight/100) * $value_from_library;
                            break;

                        case "Grade" :
                            $field = "grade_categorization";
                            $value_of_field = $clientData->{$field};

                            # find value from library
                            $value_from_library = DB::table('tbl_program_lead_library')->
                                            where('programbucket_id', $programBucketId)->
                                            where('value_category', $value_of_field)->
                                            pluck($type)->
                                            first();

                            $sub_result = ($weight/100) * $value_from_library;
                            break;

                        case "Destination_country" :
                            $field = "country_categorization";
                            $value_of_field = $clientData->{$field};

                            # find value from library
                            $value_from_library = DB::table('tbl_program_lead_library')->
                                            where('programbucket_id', $programBucketId)->
                                            where('value_category', $value_of_field)->
                                            pluck($type)->
                                            first();

                            $sub_result = ($weight/100) * $value_from_library;
                            break;

                        case "Status" :
                            # ini berlaku utk menentukan hot warm and cold
                            # bisa dikonfirmasi kembali ke ka Hafidz
                            break;

                        case "Major" :
                            $field = "major_categorization";
                            $value_of_field = $clientData->{$field};

                            # find value from library
                            $value_from_library = DB::table('tbl_program_lead_library')->
                                            where('programbucket_id', $programBucketId)->
                                            where('value_category', $value_of_field)->
                                            pluck($type)->
                                            first();

                            $sub_result = ($weight/100) * $value_from_library;
                            break;

                        case "Priority" :
                            switch ($initProgramName) {
                                case "Admissions Mentoring":
                                    $sub_result = ($weight/100) * 1;
                                    break;

                                case "Experiential Learning":
                                    $sub_result = ($weight/100) * 0.75;
                                    break;

                                case "Academic Performance (SAT)":
                                    $sub_result = ($weight/100) * 0.50;
                                    break;

                                case "Academic Performance (Academic Tutoring)":
                                    $sub_result = ($weight/100) * 0.25;
                                    break;
                            }
                            break;

                        case "Seasonal" :
                            # pertama buat view table seasonal
                            # yg isinya adalah event / program apa saja yang akan diadakan dalam 4/6 bulan ke depan
                            # lalu apabila ada seasonal program maka scorenya 1 
                            # yg dimana 1 ini akan dikalikan dengan weight nya (contoh : 10%)
                            # masukkan 10% ini ke dalam variable sub_result
                            break;

                        case "Already_joined" :
                            # buat function 
                            # utk melakukan pengecekan berdasarkan initial program dan initial sub program
                            # (contoh : sedang melakukan pengecekan di program Experiential Learning
                            # maka, gunakan id initial program dan cari melalui init sub program utk mendapatkan
                            # client program yg memiliki sub program tsb dari tbl_init_prog_sub.
                            # jika count > 0 maka asumsikan sudah pernah join maka beri nilai 0 > bisa dikonfirmasi ke ka Hafidz lagi 
                            # apakah yg sudah pernah join diberi nilai 0 apa 1
                            break;
                    }

                    $total_result += $sub_result;

                    $this->info($initProgramName.' dengan param : '.$paramName.' menghasilkan : '.$value_from_library. ' in percent : '.$sub_result.'%');
                    
    
                }

                $this->info('Total dari program : '.$initProgramName.' menghasilkan score : '.$total_result);
                $this->info('');
    

                # buat 1 table dengan nama tbl_client_lead_tracking
                # yg fungsinya utk menyimpan total_result dari sistem sesuai programnya
                # contoh label : client_id, initialprogram_id, total_result, status, created_at, updated_at
                # contoh row 1 : 2002, 1, 0.6, 1, 2023-07-13 19:00:00
                # contoh row 2 : 2002, 2, 0.325, 1, 2023-07-13 19:00:00
                # dst
                # sehingga kita bisa tau client 2002 direkomendasikan program apa saja.
                # yang perlu diperhatikan adalah
                # ketika sistem digenerate, maka status yg sebelumnya 1 harus di 0 kan dulu sebelum insert data yang baru.
                # tujuannya agar kita bisa melihat history perubahannya per tanggal berapa
                # berlaku jg apabila tim sales melakukan perubahan manual, maka akan masuknya ke dalam table ini
                # dengan syarat, yg sebelumnya statusnya di 0 kan dulu.
                # contoh : experiential learning dari client id 2002 di ubah ke score hot dari yg sebelumnya warm
                # jadi khusus utk program experiential learning saja yg sebelumnya di ubah ke 0 lalu yg hasil update oleh tim sales diberi status 1

                # perlu dikonfirmasi jg ke tim sales
                # sistem perlu di generate tiap berapa bulan
                # contoh per 6 bulan sekali.

            }
        }


        return Command::SUCCESS;
    }

    public function getValueFromLibrary($libDetails)
    {
        
    }
}
