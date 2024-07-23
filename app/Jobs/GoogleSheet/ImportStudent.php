<?php
 
namespace App\Jobs\GoogleSheet;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\Client\ProcessDefineCategory;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Models\Client;
use App\Models\JobBatches;
use App\Models\Role;
use App\Models\School;
use App\Models\UserClient;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Revolution\Google\Sheets\Facades\Sheets;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ImportStudent implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;
    use IsMonitored;

    public $studentData;
    /**
     * Create a new job instance.
     */
    public function __construct($student)
    {
        $this->studentData = $student;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
 
            return;
        }

        foreach ($this->studentData as $key => $val) {
            $student = null;
            $phoneNumber = isset($val['Phone Number']) ? $this->setPhoneNumber($val['Phone Number']) : null;
            isset($val['Parents Phone']) ? $parentPhone = $this->setPhoneNumber($val['Parents Phone']) : $parentPhone = null;

            $studentName = $val['Full Name'] != null ? $this->explodeName($val['Full Name']) : null;
            $parentName = $val['Parents Name'] != null ? $this->explodeName($val['Parents Name']) : null;

            $joinedDate = isset($val['Joined Date']) ? $val['Joined Date'] : null;


            // $last_id = UserClient::max('st_id');
            // $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
            // $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);

            // Check existing school
            $school = School::where('sch_name', $val['School'])->get()->pluck('sch_id')->first();

            if (!isset($school)) {
                $newSchool = $this->createSchoolIfNotExists($val['School']);
            }

            $mail = isset($val['Email']) ? $val['Email'] : null;
            $student = $this->checkExistingClientImport($phoneNumber, $mail);

            if (!$student['isExist']) {
                $studentDetails = [
                    // 'st_id' => $studentId,
                    'first_name' => $studentName != null ? $studentName['firstname'] : ($parentName != null ? $parentName['firstname'] . ' ' . $parentName['lastname'] : null),
                    'last_name' =>  $studentName != null && isset($studentName['lastname']) ? $studentName['lastname'] : ($parentName != null ? 'Child' : null),
                    'mail' => $mail,
                    'phone' => $phoneNumber,
                    'dob' => isset($val['Date of Birth']) ? $val['Date of Birth'] : null,
                    'insta' => isset($val['Instagram']) ? $val['Instagram'] : null,
                    'state' => isset($val['State']) ? $val['State'] : null,
                    'city' => isset($val['City']) ? $val['City'] : null,
                    'address' => isset($val['Address']) ? $val['Address'] : null,
                    'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                    'st_grade' => isset($val['Grade']) ? $val['Grade'] : null,
                    'lead_id' => $val['Lead'] == 'KOL' ? $val['kol'] : $val['Lead'],
                    'event_id' => isset($val['Event']) && $val['Lead'] == 'LS003' ? $val['Event'] : null,
                    // 'partner_id' => isset($val['partner']) && $val['Lead'] == 'LS015' ? $val['partner'] : null,
                    'eduf_id' => isset($val['Edufair'])  && $val['Lead'] == 'LS017' ? $val['Edufair'] : null,
                    'st_levelinterest' => $val['Level of Interest'],
                    'graduation_year' => isset($val['Graduation Year']) ? $val['Graduation Year'] : null,
                    'st_abryear' => isset($val['Year of Study Abroad']) ? $val['Year of Study Abroad'] : null,
                ];

                isset($val['Joined Date']) ? $studentDetails['created_at'] = Carbon::parse($val['Joined Date'] . ' ' . date('H:i:s')) : null;
                isset($val['Joined Date']) ? $studentDetails['updated_at'] = Carbon::parse($val['Joined Date'] . ' ' . date('H:i:s')) : null;
                
                $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                $student = UserClient::create($studentDetails);
                $student->roles()->attach($roleId);

            } else {
                $student = UserClient::find($student['id']);

            }

            # Connecting student with parent
            $checkExistParent = null;
            $parent = null;
            $parentIds = [];
            if (isset($val['Parents Name'])) {
                // $this->createParentsIfNotExists($val['Parents Name'], $parentPhone, $student);
                $checkExistParent = $this->checkExistClientRelation('student', $student, $val['Parents Name']);
                if($checkExistParent['isExist'] && $checkExistParent['client'] != null){
                    $parent = $checkExistParent['client'];
                }else if(!$checkExistParent['isExist']){
                    $name = $this->explodeName($val['Parents Name']);

                    if(isset($parentPhone)){
                        $checkParent = $this->checkExistingClientImport($parentPhone, null);
                        
                        if(!$checkParent['isExist']){

                            $parentDetails = [
                                'first_name' => $name['firstname'],
                                'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                                'phone' => isset($parentPhone) ? $parentPhone : null,
                            ];

                            $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                            $parent = UserClient::create($parentDetails);
                            $parent->roles()->attach($roleId);
                        }else{
                            $parent = UserClient::find($checkParent['id']);
                        }
                    }else{
                        
                        $parentDetails = [
                            'first_name' => $name['firstname'],
                            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                            'phone' => isset($parentPhone) ? $parentPhone : null,
                        ];

                        $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                        $parent = UserClient::create($parentDetails);
                        $parent->roles()->attach($roleId);
                    }

                    $student->parents()->attach($parent);

                }
                $parentIds[] = $parent['id'];
            }

            // Sync interest program
            if (isset($val['Interested Program'])) {
                $this->syncInterestProgram($val['Interested Program'], $student, $joinedDate);
            }

            // Sync country of study abroad
            if (isset($val['Country of Study Abroad'])) {
                $this->syncDestinationCountry($val['Country of Study Abroad'], $student);
            }

            // Sync interest major
            if (isset($val['Interest Major'])) {
                $this->syncInterestMajor($val['Interest Major'], $student);
            }

            $logDetails[] = [
                'client_id' => $student['id']
            ];

            $childIds[] = $student['id'];

            $imported_date[] = [Carbon::now()->format('d-m-Y H:i:s')];
            // $totalImported += $imported->totalUpdatedRows;
        }

        # trigger to verifying children
        count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds)->onQueue('verifying-client') : null;

        # trigger to define category children
        count($childIds) > 0 ? ProcessDefineCategory::dispatch($childIds)->onQueue('define-category-client') : null;

        # trigger to verifying parent
        count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;

        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet('Students')->range('Z'. $this->studentData->first()['No'] + 1)->update($imported_date);
        $dataJobBatches = JobBatches::find($this->batch()->id);
        
        $logDetailsCollection = Collect($logDetails);
        $logDetailsMerge = $logDetailsCollection->merge(json_decode($dataJobBatches->log_details));
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($imported_date), 'log_details' => json_encode($logDetailsMerge), 'type' => 'student', 'category' => 'Import']);
        

    }
   
}