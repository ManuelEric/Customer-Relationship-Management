<?php

namespace App\Http\Traits;

use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\School;
use App\Models\Tag;
use App\Models\UserClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait SyncClientTrait
{
    use SplitNameTrait;
    use CheckExistingClientImport;

    public function syncInterestProgram($interestPrograms, $client, $joinedDate)
    {
        $programDetails = []; # default
        $programs = explode(', ', $interestPrograms);
        foreach ($programs as $program) {

            $programFromDB = Program::all();

            $mapProgram = $programFromDB->map(
                function ($item, int $key) {
                    return [
                        'prog_id' => $item->prog_id,
                        'program_name' => $item->programName,
                    ];
                }
            );

            $existProgram = $mapProgram->where('program_name', $program)->first();
            if ($existProgram) {
                $programDetails[] = [
                    'prog_id' => $existProgram['prog_id'],
                    'created_at' => $joinedDate,
                    'updated_at' => $joinedDate,
                ];
            }
        }

        isset($programDetails) ? $client->interestPrograms()->syncWithoutDetaching($programDetails) : null;

    }

    public function syncDestinationCountry($destinationCountry, $client)
    {
        $countryName = $arrayCountry = []; # default
        $destinationCountryDetails = $destinationCountryMerge = $currentDestinationCountry = new Collection();
        
        $arrayCountry = explode(", ", $destinationCountry);

        $destinationCountryDetails = $this->checkCountry($arrayCountry);
        if(isset($client->destinationCountries)){
            foreach ($client->destinationCountries as $country) {
                $countryName[] = $country->name;
            }

            $currentDestinationCountry = $this->checkCountry($countryName);
            
            $destinationCountryMerge = $destinationCountryDetails->merge($currentDestinationCountry)->unique();
           
        }else{
            $destinationCountryMerge = $destinationCountryDetails;
        }
        
        isset($destinationCountryMerge) ? $client->destinationCountries()->sync($destinationCountryMerge->toArray()) : null;

    }

    private function checkCountry($arrayCountry)
    {
        $destinationCountryDetails = new Collection();
     
        foreach ($arrayCountry as $key => $value) {

            $countryName = $value;

            switch ($countryName) {

                case preg_match('/australia/i', $countryName) == 1:
                    $regionName = "Australia";
                    break;

                case preg_match("/United State|State|US/i", $countryName) == 1:
                    $regionName = "US";
                    break;

                case preg_match('/United Kingdom|Kingdom|UK/i', $countryName) == 1:
                    $regionName = "UK";
                    break;

                case preg_match('/canada/i', $countryName) == 1:
                    $regionName = "Canada";
                    break;

                case preg_match('/asia/i', $countryName) == 1:
                    $regionName = "Asia";
                    break;

                default:
                    $regionName = "Other";
            }

            $tagFromDB = Tag::where('name', $regionName)->first();
            if (isset($tagFromDB)) {
                
                $destinationCountryDetails->push([
                    'tag_id' => $tagFromDB->id,
                    'country_name' => $countryName,
                ]);
    
            } else {
                // $newCountry = Tag::create(['name' => $regionName]);
                $destinationCountryDetails->push([
                    'tag_id' => 7,
                    'country_name' => $countryName,
                ]);
            }
        }

        return $destinationCountryDetails;
    }

    public function syncInterestMajor($interestMajors, $client)
    {
        $majorName = $arrayMajor = []; # default
        $currentInterestMajor = $interestMajorDetails = $interestMajorMerge = new Collection();

        $arrayMajor = explode(", ", $interestMajors);

        $interestMajorDetails = $this->checkMajor($arrayMajor);

        if(isset($client->interestMajor)){
            foreach ($client->interestMajor as $major) {
                $majorName[] = $major->name;
            }

            $currentInterestMajor = $this->checkMajor($majorName);
            
            $interestMajorMerge = $interestMajorDetails->merge($currentInterestMajor)->unique();
           
        }else{
            $interestMajorMerge = $interestMajorDetails;
        }


        isset($interestMajorMerge) ? $client->interestMajor()->sync($interestMajorMerge->toArray()) : null;

    }

    private function checkMajor($arrayMajor)
    {
        $majorDetails = new Collection(); # default

        foreach ($arrayMajor as $major) {
            $majorFromDB = Major::where('name', $major)->first();
            if (isset($majorFromDB)) {
                $majorDetails->push([
                    'major_id' => $majorFromDB->id,
                ]);
            } else {
                $newMajor = Major::create(['name' => $major]);
                $majorDetails->push([
                    'major_id' => $newMajor->id,
                ]);
            }
        }

        return $majorDetails;
    }

    public function checkExistClientRelation($type, $mainClient, $secondClient)
    {
        # type (parent) = Sync from parent to student
        # type (student) = Sync from student to parent

        $secondClientDetails = [
            'isExist' => true,
            'client' => null
        ];
        // $isExist = true;

        switch ($type) {
            case 'parent':            
                # Check existing children
                # If parent have children
                if(isset($mainClient->childrens)){
                    $mapChildren = $mainClient->childrens->map(
                        function ($item, int $key) {
                            return [
                                'id' => $item->id,
                                'full_name' => strtolower($item->fullName),
                                'deleted_at' => $item->deleted_at,
                            ];
                        }
                    );
            
                    $existChildren = $mapChildren->where('deleted_at', null)->where('full_name', strtolower($secondClient))->first();

                    # if children not existing from this parent
                    if(!isset($existChildren)){
                        $secondClientDetails['isExist'] = false;
                    }else{
                        $children = UserClient::find($existChildren['id']);
                        $secondClientDetails['isExist'] = true;
                        $secondClientDetails['client'] = $children;
                    }

                # Parent no have children
                }else{
                    $secondClientDetails['isExist'] = false;
                }
                break;


            case 'student':

                # Check existing parent
                # If child have parent
                if(isset($mainClient->parents)){
                    $mapParent = $mainClient->parents->map(
                        function ($item, int $key) {
                            return [
                                'id' => $item->id,
                                'full_name' => strtolower($item->fullName),
                                'deleted_at' => $item->deleted_at,
                            ];
                        }
                    );
                    
            
                    $existParent = $mapParent->where('deleted_at', null)->where('full_name', strtolower($secondClient))->first();
                 
                    # if parent not existing from this child
                    if(!isset($existParent)){
                        $secondClientDetails['isExist'] = false;
                    }else{
                        $children = UserClient::find($existParent['id']);
                        $secondClientDetails['isExist'] = true;
                        $secondClientDetails['client'] = $children;
                    }

                # Child no have parent
                }else{
                    $secondClientDetails['isExist'] = false;
                }

                break;

        }

        return $secondClientDetails;

        
    }

    private function createSchoolIfNotExists($sch_name)
    {
        $last_id = School::withTrashed()->max('sch_id');
        $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

        $newSchool = School::create(['sch_id' => $school_id_with_label, 'sch_name' => $sch_name]);

        return $newSchool;
    }

    private function explodeName($name)
    {

        $fullname = explode(' ', $name);
        $limit = count($fullname);

        $data = [];

        if ($limit > 1) {
            $data['lastname'] = $fullname[$limit - 1];
            unset($fullname[$limit - 1]);
            $data['firstname'] = implode(" ", $fullname);
        } else {
            $data['firstname'] = implode(" ", $fullname);
        }

        return $data;
    }
    
}
