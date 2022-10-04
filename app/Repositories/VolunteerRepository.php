<?php

namespace App\Repositories;

use App\Interfaces\VolunteerRepositoryInterface;
use App\Models\Volunteer;

class VolunteerRepository implements VolunteerRepositoryInterface 
{
    public function getAllVolunteer()
    {
        return Volunteer::orderBy('volunt_firstname', 'asc')->orderBy('volunt_lastname', 'asc')->get();
    }

    public function getVolunteerById($volunteerId) 
    {
        return Volunteer::findOrFail($volunteerId);
    }

    public function deleteVolunteer($volunteerId) 
    {
        Volunteer::destroy($volunteerId);
    }

    public function createVolunteer(array $volunteerDetails) 
    {
        return Volunteer::create($volunteerDetails);
    }

    public function updateVolunteer($volunteerId, array $newDetails) 
    {
        return Volunteer::whereVolunteerId($volunteerId)->update($newDetails);
    }

    public function cleaningVolunteer()
    {
        Volunteer::where('volunt_lastname', '=', '')->update(
            [
                'volunt_lastname' => null
            ]
        );

        Volunteer::where('volunt_address', '=', '')->update(
            [
                'volunt_address' => null
            ]
        );

        Volunteer::where('volunt_mail', '=', '')->update(
            [
                'volunt_mail' => null
            ]
        );

        Volunteer::where('volunt_graduatedfr', '=', '')->update(
            [
                'volunt_graduatedfr' => null
            ]
        );

        Volunteer::where('volunt_major', '=', '')->update(
            [
                'volunt_major' => null
            ]
        );

        Volunteer::where('volunt_idcard', '=', '')->update(
            [
                'volunt_idcard' => null
            ]
        );

        Volunteer::where('volunt_npwp', '=', '')->update(
            [
                'volunt_idcard' => null
            ]
        );
    }
}