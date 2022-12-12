<?php

namespace App\Interfaces;

interface ClientRepositoryInterface
{
    public function getAllClientDataTables();
    public function getAllClientByRoleAndStatusDataTables($roleName, $statusClient);
    public function getAllClientByRole($roleName); # mentee, parent, teacher
    public function getAllClientByRoleAndStatus($roleName, $statusClient);
    public function getClientById($clientId);
    public function deleteClient($clientId);
    public function createClient($role, array $clientDetails);
    public function createClientRelation($parentId, $studentId);
    public function createDestinationCountry($studentId, $destinationCountryDetails);
    public function createInterestProgram($studentId, array $interestProgramDetails);
    public function createInterestUniversities($studentId, array $interestUnivDetails);
    public function createInterestMajor($studentId, array $interestMajorDetails);
    public function updateClient($clientId, array $newDetails);
}