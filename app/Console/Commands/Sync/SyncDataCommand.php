<?php

namespace App\Console\Commands\Sync;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use App\Models\ClientProgram;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\Major;
use App\Models\Program;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use App\Repositories\ClientRepository;
use App\Repositories\SchoolRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Revolution\Google\Sheets\Facades\Sheets;

class SyncDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data CRM to google sheet';

    protected SchoolRepository $schoolRepository;
    protected ClientRepository $clientRepository;

    public function __construct(SchoolRepository $schoolRepository, ClientRepository $clientRepository)
    {
        parent::__construct();

        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
    }

    # Purpose:
    # This function getting data from database CRM and update to google sheet (Sync Data CRM) => https://docs.google.com/spreadsheets/d/1VjPEf8HiRY_dHTKUfXsV3KNaQ44wJpriw2sty5fZVwc/edit?gid=0#gid=0
    # Using argument type (string)

    # Outcome:
    # This function update data google sheet (Sync Data CRM)
    public function handle()
    {
        $type = $this->argument('type');
        
        Log::info('Cron sync data '.$type.' to google sheet works fine.');

        try {
            
            $i = 0;
            $index = 2;
            
            # get argument type
            $query = $this->fnSetQueryByType($type);
            
            # clear google sheet based on sheet name
            Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_SYNC_DATA'))->sheet($query['sheet_name'])->range('A2:Z'. $query['query']->count() + 1)->clear();

            $query['query']
            ->chunk(200, function($models) use(&$i, &$index, $query, $type){
                
                $result = [];
                
                foreach ($models as $key => $val) {
                    
                    switch ($type) {
                        case 'tutor':
                            # Info Adjustment:
                            # deleted $val->extended_id
                            $result[$key] = [$val->id, $val->fullname, $val->fullname . ' | ' . $val->id, $val->roles->first()->pivot->tutor_subject];
                            break;
                            
                        case 'school':
                            $result[$key] = [$val->sch_id, $val->sch_name];
                            break;

                        case 'partner':
                            $result[$key] = [$val->corp_id, $val->corp_name];
                            break;

                        case 'event':
                            $result[$key] = [$val->event_id, $val->event_title];
                            break;

                        case 'program_b2b':
                        case 'program_b2c':
                            $result[$key] = [$val->prog_id, $val->program_name];
                            break;

                        case 'program':
                        case 'admission':
                            $result[$key] = [$val->prog_id, $val->program_name, $val->program_name . ' | ' . $val->prog_id];
                            break;

                        case 'sales':
                        case 'employee':
                            # Info Adjustment:
                            # deleted $val->extended_id
                            $result[$key] = [$val->fullname, $val->id, $val->fullname . ' | ' . $val->id];
                            break;

                        case 'mentor':
                            # Info Adjustment:
                            # deleted $val->extended_id
                            $result[$key] = [$val->id, $val->fullname, $val->fullname . ' | ' . $val->id];
                            break;

                        case 'lead':
                        case 'kol':
                            # Info Adjustment:
                            # deleted $val->id
                            $result[$key] = [$val->lead_name, $val->lead_id, $val->department_name];
                            break;

                        case 'major':
                            $result[$key] = [$val->id, $val->name];
                            break;

                        case 'edufair':
                            $result[$key] = [$val->id, $val->organizerName];
                            break;

                        case 'university':
                            # Info Adjustment:
                            # Add relation to country
                            $result[$key] = [$val->univ_id, $val->univ_name, $val->tags->name];
                            break;

                        case 'mentee':
                        case 'alumni-mentee':
                            $result[$key] = [$val->id, $val->full_name, $val->id . ' | ' . $val->full_name];
                            break;

                        case 'tutoring-student':
                            $subjects = [];
                            if(isset($val->clientMentor) && $val->clientMentor()->where('type', 5)->count() > 0){
                                $tutors = $val->clientMentor->pluck('id');
                                if(count($tutors) > 0){
                                    $users = User::whereIn('id', $tutors)->get();
                                    foreach ($users as $user) {
                                        if($user->user_subjects()->count() > 0){
                                            foreach ($user->user_subjects as $user_subject) {
                                                $subjects[] = $user_subject->subject->name;
                                            }
                                        }
                                    }
                                }
                            }

                            $result[$key] = [$val->client->id, $val->client->full_name, $val->client->id . ' | ' . $val->client->full_name, count($subjects) > 0 ? implode(", ", $subjects) : ''];
                            break;                 
                    }

                }
                   
                if($i == 0){
                    $index = 2;
                }else{
                    $index += 200;
                }

                # Update google sheet based on sheet name
                Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_SYNC_DATA'))->sheet($query['sheet_name'])->range('A'. $index)->update($result);
                $i++;

            });

            # Update column updated_at google sheet
            Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_SYNC_DATA'))->sheet($query['sheet_name'])->range($query['column_updated_at'] . '2')->update([[Carbon::now()->format('d-m-Y H:i:s')]]);
            

        } catch (Exception $e) {
            Log::error('Failed sync data '.$query['sheet_name'] . ': '. $e->getMessage());
        }

        return Command::SUCCESS;
    }

    # Purpose:
    # This functtion getting data from database and mapping data
    # Using argument type

    # Outcome:
    # This function will return mapped data
    # return type array ['query', 'sheet_name', 'column_updated_at']
    private function fnSetQueryByType($type): array
    {
        $query = $sheet_name = $column_updated_at = null;
        switch ($type) {
            case 'school':
                $query = School::select('sch_id', 'sch_name')
                ->where('status', 1)
                ->where('is_verified', 'Y')
                ->orderBy('sch_id', 'asc')
                ->groupBy('sch_name');

                $sheet_name = 'Schools';
                $column_updated_at = 'C';
                break;
            
            case 'partner':
                $query = Corporate::select('corp_id', 'corp_name');

                $sheet_name = 'Partners';
                $column_updated_at = 'C';
                break;
                
            case 'event':
                $query = Event::select('event_id', 'event_title')
                ->orderBy('created_at', 'desc');
                    
                $sheet_name = 'Events';
                $column_updated_at = 'C';
                break;

            case 'program_b2b':
                $query = Program::
                where('prog_type', 'b2b');
                    
                $sheet_name = 'Program B2B';
                $column_updated_at = 'C';
                break;

            case 'program_b2c':
                $query = Program::
                where('prog_type', 'b2c');
                    
                $sheet_name = 'Program B2C';
                $column_updated_at = 'C';
                break;

            case 'program':
                $query = Program::query();
                    
                $sheet_name = 'Programs';
                $column_updated_at = 'D';
                break;

            case 'admission':
                $query = Program::whereHas('main_prog', function ($query) {
                    $query->where('tbl_main_prog.id', 1);
                });
                    
                $sheet_name = 'Admissions Only';
                $column_updated_at = 'D';
                break;

            case 'sales':
                $query = User::whereHas('roles', function ($query) {
                    $query->where('role_name', 'like', '%Employee%');
                })->whereHas('department', function ($query)  {
                    $query->where('dept_name', 'like', '%Client Management%');
                })->where('active', 1)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                    
                $sheet_name = 'Sales';
                $column_updated_at = 'E';
                break;

            case 'tutor':
                $query = User::withAndWhereHas('roles', function ($sub_query) {
                    $sub_query->where('role_name', 'Tutor');
                })->whereNotNull('email')->where('active', 1);

                $sheet_name = 'Tutors';
                $column_updated_at = 'F';
                break;

            case 'mentor':
                $query = User::with('educations')->withAndWhereHas('roles', function ($sub_query) {
                    $sub_query->where('role_name', 'Mentor');
                })->whereNotNull('email')->where('active', 1);

                $sheet_name = 'Mentors';
                $column_updated_at = 'E';
                break;

            case 'employee':
                $query = User::with('department')->whereHas('roles', function ($query) {
                    $query->where('role_name', 'like', '%Employee%');
                })->where('active', 1)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');

                $sheet_name = 'Employees';
                $column_updated_at = 'E';
                break;
            
            case 'lead':
                $query = Lead::where('status', 1);

                $sheet_name = 'Leads';
                $column_updated_at = 'E';
                break;

            case 'major':
                $query = Major::query();

                $sheet_name = 'Majors';
                $column_updated_at = 'C';
                break;

            case 'edufair':
                $query = EdufLead::query();

                $sheet_name = 'Edufair';
                $column_updated_at = 'C';
                break;

            case 'kol':
                $query = Lead::where('main_lead', 'KOL');

                $sheet_name = 'KOL';
                $column_updated_at = 'E';
                break;

            case 'university':
                $query = University::query();

                $sheet_name = 'Universities';
                $column_updated_at = 'D';
                break;

            case 'mentee':
                $query = Client::where('client.category', 'mentee');

                $sheet_name = 'Active Mentees';
                $column_updated_at = 'D';
                break;

            case 'alumni-mentee':
                $query = Client::where('client.category', 'alumni-mentee');

                $sheet_name = 'Alumni Mentees';
                $column_updated_at = 'D';
                break;

            case 'tutoring-student':
                $query = ClientProgram::whereHas('program', function ($sub_query) {
                    $sub_query->where('main_prog_id', 4);
                })->where('status', 1)->groupBy('client_id');

                $sheet_name = 'Tutoring Students';
                $column_updated_at = 'E';
                break;

        }

        return ['query' => $query, 'sheet_name' => $sheet_name, 'column_updated_at' => $column_updated_at];
    }
}
