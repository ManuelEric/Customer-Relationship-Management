<?php
 
namespace App\Jobs\GoogleSheet;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\Client\ProcessDefineCategory;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
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
use Revolution\Google\Sheets\Facades\Sheets;

class ImportParent implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;

    public $parentData;
    /**
     * Create a new job instance.
     */
    public function __construct($parent)
    {
        $this->parentData = $parent;
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

        foreach ($this->parentData as $key => $val) {
            $parent = null;
            $phoneNumber = $this->setPhoneNumber($val['Phone Number']);

            $parent = $this->checkExistingClientImport($phoneNumber, $val['Email']);

            $joinedDate = isset($val['Joined Date']) ? $val['Joined Date'] : null;

            $parentName = $this->explodeName($val['Full Name']);

            if (!$parent['isExist']) {
                $parentDetails = [
                    'first_name' => $parentName['firstname'],
                    'last_name' => isset($parentName['lastname']) ? $parentName['lastname'] : null,
                    'mail' => $val['Email'],
                    'phone' => $phoneNumber,
                    'dob' => isset($val['Date of Birth']) ? $val['Date of Birth'] : null,
                    'insta' => isset($val['Instagram']) ? $val['Instagram'] : null,
                    'state' => isset($val['State']) ? $val['State'] : null,
                    'city' => isset($val['City']) ? $val['City'] : null,
                    'address' => isset($val['Address']) ? $val['Address'] : null,
                    'lead_id' => $val['Lead'],
                    'event_id' => isset($val['Event']) && $val['Lead'] == 'LS003' ? $val['Event'] : null,
                    'eduf_id' => isset($val['Edufair'])  && $val['Lead'] == 'LS017' ? $val['Edufair'] : null,
                    'st_levelinterest' => $val['Level of Interest'],
                ];


                isset($val['Joined Date']) ? $parentDetails['created_at'] = $val['Joined Date'] : null;
                
                $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                $parent = UserClient::create($parentDetails);
                $parent->roles()->attach($roleId);
            } else {
                $parent = UserClient::find($parent['id']);
            }


            $children = null;
            $checkExistChildren = null;
            if (isset($val['Children Name'])) {
                $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $val['Children Name']);
                
                if($checkExistChildren['isExist'] && $checkExistChildren['client'] != null){
                    $children = $checkExistChildren['client'];
                    $childrenIds[] = $children;
                }else if(!$checkExistChildren['isExist']){
                    $name = $this->explodeName($val['Children Name']);
                    $school = School::where('sch_name', $val['School'])->first();

                    if (!isset($school)) {
                        $school = $this->createSchoolIfNotExists($val['School']);
                    }

                    $childrenDetails = [
                        'first_name' => $name['firstname'],
                        'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                        'sch_id' => $school->sch_id,
                        'graduation_year' => isset($val['Graduation Year']) ? $val['Graduation Year'] : null,
                        'lead_id' => $val['Lead'],
                        'event_id' => isset($val['Event']) && $val['Lead'] == 'LS003' ? $val['Event'] : null,
                        'eduf_id' => isset($val['Edufair'])  && $val['Lead'] == 'LS017' ? $val['Edufair'] : null,
                    ];

                    isset($val['Joined Date']) ? $childrenDetails['created_at'] = $val['Joined Date'] : null;

                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                    $children = UserClient::create($childrenDetails);
                    $children->roles()->attach($roleId);
                    $parent->childrens()->attach($children);
                    $childrenIds[] = $children['id'];
                }

            }

            if (isset($val['Interested Program'])) {
                $this->syncInterestProgram($val['Interested Program'], $parent, $joinedDate);
                $children != null ?  $this->syncInterestProgram($val['Interested Program'], $children, $joinedDate) : null;
            }

            // Sync country of study abroad
            if (isset($val['Destination Country'])) {
                $this->syncDestinationCountry($val['Destination Country'], $parent);
                $children != null ?  $this->syncDestinationCountry($val['Destination Country'], $children) : null;
            }
        
            $parentIds[] = $parent['id'];

            $logDetails[] = [
                'client_id' => $parent['id']
            ];

            $imported_date[] = [Carbon::now()->format('d-m-Y H:i:s')];
            // $totalImported += $imported->totalUpdatedRows;
        }


        # trigger to verifying parent
        count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;
        
        # trigger to verifying children
        count($childrenIds) > 0 ? ProcessVerifyClient::dispatch($childrenIds)->onQueue('verifying-client') : null;
 
        # trigger to define category children
        count($childrenIds) > 0 ? ProcessDefineCategory::dispatch($childrenIds)->onQueue('define-category-client') : null;

        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet('Parents')->range('V'. $this->parentData->first()['No'] + 1)->update($imported_date);
        $dataJobBatches = JobBatches::find($this->batch()->id);
        
        $logDetailsCollection = Collect($logDetails);
        $logDetailsMerge = $logDetailsCollection->merge(json_decode($dataJobBatches->log_details));
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($imported_date), 'log_details' => json_encode($logDetailsMerge), 'type' => 'parent', 'category' => 'Import']); 
        

    }
   
}