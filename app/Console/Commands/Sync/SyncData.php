<?php

namespace App\Console\Commands\Sync;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
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

class SyncData extends Command
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->argument('type');
        
        Log::info('Cron sync data '.$type.' to google sheet works fine.');

        try {
            
            $i = 0;
            $index = 2;
            
            $query = $this->setQueryByType($type);


            $query['query']
            ->chunk(200, function($models) use(&$i, &$index, $query, $type){
                
                $data = [];
                
                foreach ($models as $key => $val) {
                    
                    switch ($type) {
                        case 'tutor':
                            $data[$key] = [$val->id, $val->fullname, $val->extended_id, $val->fullname . ' | ' . $val->id, $val->roles->first()->pivot->tutor_subject];
                            break;
                            
                        case 'school':
                            $data[$key] = [$val->sch_id, $val->sch_name];
                            break;

                        case 'partner':
                            $data[$key] = [$val->corp_id, $val->corp_name];
                            break;

                        case 'event':
                            $data[$key] = [$val->event_id, $val->event_title];
                            break;

                        case 'program_b2b':
                        case 'program_b2c':
                            $data[$key] = [$val->prog_id, $val->program_name];
                            break;

                        case 'program':
                        case 'admission':
                            $data[$key] = [$val->prog_id, $val->program_name, $val->program_name . ' | ' . $val->prog_id];
                            break;

                        case 'sales':
                        case 'employee':
                            $data[$key] = [$val->fullname, $val->id, $val->extended_id, $val->fullname . ' | ' . $val->id];
                            break;

                        case 'mentor':
                            $data[$key] = [$val->id, $val->fullname, $val->extended_id, $val->fullname . ' | ' . $val->id];
                            break;

                        case 'lead':
                        case 'kol':
                            $data[$key] = [$val->id, $val->lead_name, $val->lead_id, $val->department_name];
                            break;

                        case 'major':
                            $data[$key] = [$val->id, $val->name];
                            break;

                        case 'edufair':
                            $data[$key] = [$val->id, $val->organizerName];
                            break;

                        case 'university':
                            $data[$key] = [$val->univ_id, $val->univ_name, $val->univ_country];
                            break;

                        case 'mentee':
                            $data[$key] = [$val->id, $val->full_name, $val->id . ' | ' . $val->full_name];
                            break;
                 
                    }

                }
                   
                // $this->info($index);
                if($i == 0){
                    $index = 2;
                }else{
                    $index += 200;
                }

                Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_SYNC_DATA'))->sheet($query['sheetName'])->range('A'. $index)->update($data);
                $i++;

            });

            Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_SYNC_DATA'))->sheet($query['sheetName'])->range($query['colUpdatedAt'] . '2')->update([[Carbon::now()->format('d-m-Y H:i:s')]]);
            

        } catch (Exception $e) {
            Log::error('Failed sync data '.$query['sheetName'], $e->getMessage());
        }

        return Command::SUCCESS;
    }

    protected function setQueryByType($type)
    {
        $query = $sheetName = $colUpdatedAt = null;
        switch ($type) {
            case 'school':
                $query = School::select('sch_id', 'sch_name')
                ->where('status', 1)
                ->where('is_verified', 'Y')
                ->orderBy('sch_id', 'asc')
                ->groupBy('sch_name');

                $sheetName = 'Schools';
                $colUpdatedAt = 'C';
                break;
            
            case 'partner':
                $query = Corporate::select('corp_id', 'corp_name');

                $sheetName = 'Partners';
                $colUpdatedAt = 'C';
                break;
                
            case 'event':
                $query = Event::select('event_id', 'event_title')
                ->orderBy('created_at', 'desc');
                    
                $sheetName = 'Events';
                $colUpdatedAt = 'C';
                break;

            case 'program_b2b':
                $query = Program::
                where('prog_type', 'b2b');
                    
                $sheetName = 'Program B2B';
                $colUpdatedAt = 'C';
                break;

            case 'program_b2c':
                $query = Program::
                where('prog_type', 'b2c');
                    
                $sheetName = 'Program B2C';
                $colUpdatedAt = 'C';
                break;

            case 'program':
                $query = Program::query();
                    
                $sheetName = 'Programs';
                $colUpdatedAt = 'D';
                break;

            case 'admission':
                $query = Program::whereHas('main_prog', function ($query) {
                    $query->where('tbl_main_prog.id', 1);
                });
                    
                $sheetName = 'Admissions Only';
                $colUpdatedAt = 'D';
                break;

            case 'sales':
                $query = User::whereHas('roles', function ($query) {
                    $query->where('role_name', 'like', '%Employee%');
                })->whereHas('department', function ($query)  {
                    $query->where('dept_name', 'like', '%Client Management%');
                })->where('active', 1)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                    
                $sheetName = 'Sales';
                $colUpdatedAt = 'E';
                break;

            case 'tutor':
                $query = User::withAndWhereHas('roles', function ($subQuery) {
                    $subQuery->where('role_name', 'Tutor');
                })->whereNotNull('email')->where('active', 1);

                $sheetName = 'Tutors';
                $colUpdatedAt = 'F';
                break;

            case 'mentor':
                $query = User::with('educations')->withAndWhereHas('roles', function ($subQuery) {
                    $subQuery->where('role_name', 'Mentor');
                })->whereNotNull('email')->where('active', 1);

                $sheetName = 'Mentors';
                $colUpdatedAt = 'E';
                break;

            case 'employee':
                $query = User::with('department')->whereHas('roles', function ($query) {
                    $query->where('role_name', 'like', '%Employee%');
                })->where('active', 1)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');

                $sheetName = 'Employees';
                $colUpdatedAt = 'E';
                break;
            
            case 'lead':
                $query = Lead::where('status', 1);

                $sheetName = 'Leads';
                $colUpdatedAt = 'E';
                break;

            case 'major':
                $query = Major::query();

                $sheetName = 'Majors';
                $colUpdatedAt = 'C';
                break;

            case 'edufair':
                $query = EdufLead::query();

                $sheetName = 'Edufair';
                $colUpdatedAt = 'C';
                break;

            case 'kol':
                $query = Lead::where('main_lead', 'KOL');

                $sheetName = 'KOL';
                $colUpdatedAt = 'E';
                break;

            case 'university':
                $query = University::query();

                $sheetName = 'Universities';
                $colUpdatedAt = 'D';
                break;

            case 'mentee':
                $query = Client::withAndWhereHas('clientProgram', function ($subQuery) {
                            $subQuery->with(['clientMentor', 'clientMentor.roles' => function ($subQuery_2) {
                                $subQuery_2->where('role_name', 'Mentor');
                            }])->whereHas('program', function ($subQuery_2) {
                                $subQuery_2->whereHas('main_prog', function ($subQuery_3) {
                                    $subQuery_3->where('prog_name', 'Admissions Mentoring');
                                });
                            })->where('status', 1)->where('prog_running_status', '!=', 2); # 1 success, 2 done
                        })->whereHas('roles', function ($subQuery) {
                            $subQuery->where('role_name', 'student');
                        });

                $sheetName = 'Active Mentees';
                $colUpdatedAt = 'D';
                break;

        }

        return ['query' => $query, 'sheetName' => $sheetName, 'colUpdatedAt' => $colUpdatedAt];
    }
}
