<?php

namespace App\Interfaces;

interface ClientRepositoryInterface
{
    public function getAllClientDataTables();
    public function getAllClientByRoleAndStatusDataTables($roleName, $statusClient = null);
    public function getAllClientByRole($roleName); # mentee, parent, teacher
    public function getAllClientByRoleAndStatus($roleName, $statusClient);
    public function getAllChildrenWithNoParents($parentId);
    public function getClientById($clientId);
    public function deleteClient($clientId);
    public function createClient($role, array $clientDetails);
    public function addRole($clientId, $role);
    public function getParentsByStudentId($studentId);
    public function createClientRelation($parentId, $studentId);
    public function createManyClientRelation($parentId, array $studentId);
    public function createDestinationCountry($studentId, $destinationCountryDetails);
    public function createInterestProgram($studentId, array $interestProgramDetails);
    public function createInterestUniversities($studentId, array $interestUnivDetails);
    public function createInterestMajor($studentId, array $interestMajorDetails);
    public function updateClient($clientId, array $newDetails);
    public function updateActiveStatus($clientId, $newStatus);
    public function checkAllProgramStatus($clientId);
}