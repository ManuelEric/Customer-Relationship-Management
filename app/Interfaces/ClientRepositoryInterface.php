<?php

namespace App\Interfaces;

interface ClientRepositoryInterface
{
    public function getAllClients();
    public function getAllClientDataTables();
    public function getAllClientByRoleAndStatusDataTables($roleName, $statusClient = null);
    public function getAllClientByRole($roleName, $month = NULL); # mentee, parent, teacher
    public function getAllClientByRoleAndStatus($roleName, $statusClient);
    public function getAllChildrenWithNoParents($parentId);
    public function getClientById($clientId);
    public function checkIfClientIsMentee($clientId);
    public function deleteClient($clientId);
    public function createClient($role, array $clientDetails);
    public function createClientAdditionalInfo(array $infoDetails);
    public function addRole($clientId, $role);
    public function removeRole($clientId, $role);
    public function getParentsByStudentId($studentId);
    public function getParentByParentName($parentName);
    public function createClientRelation($parentId, $studentId);
    public function createManyClientRelation($parentId, array $studentId);
    public function createDestinationCountry($studentId, $destinationCountryDetails);
    public function getInterestedProgram($studentId);
    public function createInterestProgram($studentId, array $interestProgramDetails);
    public function createInterestUniversities($studentId, array $interestUnivDetails);
    public function createInterestMajor($studentId, array $interestMajorDetails);
    public function updateClient($clientId, array $newDetails);
    public function updateActiveStatus($clientId, $newStatus);
    public function checkAllProgramStatus($clientId);
    # dashboard
    public function getCountTotalClientByStatus($status, $month = null);
    public function getClientByStatus($status, $month = null);
    public function getMenteesBirthdayMonthly($month);
    public function getStudentByStudentId($studentId);
    public function getStudentByStudentName($studentName);
    public function getAllClientByRoleAndDate($roleName, $month = null);

    # CRM
    public function getStudentFromV1();
    public function getParentFromV1();
}