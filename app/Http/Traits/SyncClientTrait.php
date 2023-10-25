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

    public function syncInterestProgram($interestPrograms, $client)
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
                    'country_name' => $regionName == 'Other' ? $countryName : null,
                ]);
                $destinationCountryDetails->push([
                    'tag_id' => $tagFromDB->id,
                    'country_name' => $regionName == 'Other' ? $countryName : null,
                ]);
    
            } else {
                // $newCountry = Tag::create(['name' => $regionName]);
                $destinationCountryDetails->push([
                    'tag_id' => 7,
                    'country_name' => $countryName,
                ]);
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

    public function syncClientRelation($mainClient, $secondClient, $type)
    {
        # type (parent) = Sync from parent to student
        # type (student) = Sync from student to parent
        
        $secondClientDetails = []; # default
        $currentSecondClient = $secondClientMerge = new Collection();

        switch ($type) {
            case 'parent':

                $secondClientDetails = $this->checkClientRelation('parent', $mainClient, $secondClient);
                
                isset($secondClientDetails) ? $mainClient->childrens()->attach($secondClientDetails) : null;
                
                break;
        }


    }

    private function checkClientRelation($type, $mainClient, $secondClient)
    {
        # type (parent) = Sync from parent to student
        # type (student) = Sync from student to parent

        $secondClientDetails = null;

        switch ($type) {
            case 'parent':

                $name = $this->explodeName($secondClient['children_name']);
                $school = School::where('sch_name', $secondClient['school'])->first();

                if (!isset($school)) {
                    $school = $this->createSchoolIfNotExists($secondClient['school']);
                }

                $childrenDetails = [
                    'first_name' => $name['firstname'],
                    'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                    'sch_id' => $school->sch_id,
                    'graduation_year' => isset($secondClient['graduation_year']) ? $secondClient['graduation_year'] : null,
                    'lead_id' => $secondClient['lead'],
                    'event_id' => isset($secondClient['event']) && $secondClient['lead'] == 'LS004' ? $secondClient['event'] : null,
                    'eduf_id' => isset($secondClient['edufair'])  && $secondClient['lead'] == 'LS018' ? $secondClient['edufair'] : null,
                ];
            
                # Check existing children
                # If parent have children
                if(isset($mainClient->childrens)){
                    $mapChildren = $mainClient->childrens->map(
                        function ($item, int $key) {
                            return [
                                'id' => $item->id,
                                'full_name' => $item->fullName,
                            ];
                        }
                    );
            
                    $existChildren = $mapChildren->where('full_name', $secondClient['children_name'])->first();
                 
                    # if children existing from this parent
                    if(!isset($existChildren)){
                        $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                        $children = UserClient::create($childrenDetails);
                        $children->roles()->attach($roleId);
                    }

                # Parent no have children
                }else{
                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                    $children = UserClient::create($childrenDetails);
                    $children->roles()->attach($roleId);

                }

                // Sync country of study abroad
                // if (isset($secondClient['destination_country'])) {
                //     $this->syncDestinationCountry($secondClient['destination_country'], $children);
                // }

                
                if(isset($children)){
                    $secondClientDetails = $children->id;
                }

                break;




                // $parent = UserClient::all();
                // $mapParent = $parent->map(
                //     function ($item, int $key) {
                //         return [
                //             'id' => $item->id,
                //             'full_name' => $item->fullName,
                //         ];
                //     }
                // );

                // $existParent = $mapParent->where('full_name', $secondClient)->first();

                // if (!isset($existParent)) {
                //     $name = $this->explodeName($secondClient);

                //     $parentDetails = [
                //         'first_name' => $name['first_name'],
                //         'last_name' => isset($name['last_name']) ? $name['last_name'] : null,
                //     ];

                //     $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                //     $parent = UserClient::create($parentDetails);
                //     $parent->roles()->attach($roleId);

                //     $secondClientDetails->push([
                //         'id' => $parent->id,
                //     ]);


                // } else {
                //     $secondClientDetails->push([
                //         'id' => $existParent->id,
                //     ]);
                  
                // }

            
            

        }

        return $secondClientDetails;

        
    }

    private function createSchoolIfNotExists($sch_name)
    {
        $last_id = School::max('sch_id');
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
