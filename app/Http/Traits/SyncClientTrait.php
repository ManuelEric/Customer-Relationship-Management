<?php

namespace App\Http\Traits;

use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\Tag;
use App\Models\UserClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait SyncClientTrait
{
    use  SplitNameTrait;

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

    public function syncClientRelation($secondClient, $mainClient, $type)
    {
        # type (parent) = Sync from parent to student
        # type (student) = Sync from student to parent
        
        $secondClientName = $secondClientPhone = $secondClient = []; # default
        $currentSecondClient = $secondClientDetails = $secondClientMerge = new Collection();

        Log::debug('test', [$secondClient]);

        switch ($type) {
            case 'student':

                $secondClientDetails = $this->checkClientRelation('student', $secondClient);

                Log::debug('Second Client Detail: ', $secondClientDetails);

                if(isset($mainClient->parents)){
                    foreach ($mainClient->parents as $parent) {
                        $secondClientName[] = $parent->full_name;
                        $secondClientPhone[] = $parent->phone;
                    }
        
                    $currentSecondClient = $this->checkClientRelation('student', $secondClientName);
                    
                    Log::debug('Current second client: ', $secondClientDetails);

                    $secondClientMerge = $secondClientDetails->merge($currentSecondClient)->unique();
                   
                    Log::debug('Second client merge: ', $secondClientMerge);

                }else{
                    $secondClientMerge = $secondClientDetails;
                } 

                isset($secondClientMerge) ? $mainClient->parents()->sync($secondClientMerge->toArray()) : null;
                
                break;
        }


    }

    private function checkClientRelation($type, $secondClient)
    {
        # type (parent) = Sync from parent to student
        # type (student) = Sync from student to parent

        $secondClientDetails = new Collection();

        switch ($type) {
            case 'student':
                $parent = UserClient::all();
                $mapParent = $parent->map(
                    function ($item, int $key) {
                        return [
                            'id' => $item->id,
                            'full_name' => $item->fullName,
                        ];
                    }
                );

                $existParent = $mapParent->where('full_name', $secondClient)->first();

                if (!isset($existParent)) {
                    $name = $this->explodeName($secondClient);

                    $parentDetails = [
                        'first_name' => $name['first_name'],
                        'last_name' => isset($name['last_name']) ? $name['last_name'] : null,
                    ];

                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                    $parent = UserClient::create($parentDetails);
                    $parent->roles()->attach($roleId);

                    $secondClientDetails->push([
                        'id' => $parent->id,
                    ]);


                } else {
                    $secondClientDetails->push([
                        'id' => $existParent->id,
                    ]);
                  
                }
                break;

            
            
            return $secondClientDetails;

        }
        
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
