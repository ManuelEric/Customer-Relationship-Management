<?php

namespace App\Interfaces;

use App\Models\UserClient;

interface ClientRepositoryInterface
{
    public function getAllClients(array $selectColumns = []);
    public function getAllClientsFromViewTable();

    public function getAllClientDataTables();
    public function getAllClientByRoleAndStatusDataTables($roleName, $statusClient = null);
    public function getAllClientByRole($roleName, $month = NULL); # mentee, parent, teacher
    public function getDataTables($model, $raw = false);

    public function getMaxGraduationYearFromClient();

    public function findDeletedClientById($clientId);
    public function restoreClient($clientId);

    /* followup */
    public function getClientWithoutScheduledFollowup(array $advanced_filter);
    public function getClientWithScheduledFollowup(int $status);

    /* new */
    public function getNewLeads($asDatatables = false, $month = NULL, array $advanced_filter); # month nullable
    public function getPotentialClients($asDatatables = false, $month = NULL,  array $advanced_filter); # month nullable
    public function getExistingMentees($asDatatables = false, $month = NULL,  array $advanced_filter); # month nullable
    public function getExistingNonMentees($asDatatables = false, $month = NULL,  array $advanced_filter); # month nullable
    public function getAllClientStudent(array $advanced_filter, $asDatatables=false);
    public function getAlumniMentees($groupBy = false, $asDatatables = false, $month = NULL); # month nullable
    public function getAlumniMenteesSiblings();
    public function getAlumniNonMentees($groupBy = false, $asDatatables = false, $month = NULL); # month nullable
    public function getParents($asDatatables = false, $month = NULL, array $advanced_filter);
    public function getTeachers($asDatatables = false, $month = NULL);
    public function getClientHotLeads($initialProgram);
    public function getUnverifiedStudent($asDatatables = false, $month = NULL, array $advanced_filter);
    public function getUnverifiedParent($asDatatables = false, $month = NULL, array $advanced_filter);
    public function getUnverifiedTeacher($asDatatables = false, $month = NULL, array $advanced_filter);
    public function getInactiveStudent($asDatatables = false, $month = null, array $advanced_filter);
    public function getInactiveParent($asDatatables = false, $month = null, array $advanced_filter);
    public function getInactiveTeacher($asDatatables = false, $month = null, array $advanced_filter);
    public function getClientWithNoPicAndHaveProgram();
    public function getListReferral($selectColumns = [], $filter = []);

    public function addInterestProgram($studentId, $interestProgram);
    public function removeInterestProgram($studentId, $interstProgram, $progId);
    public function getDataParentsByChildId($childId);
    public function getClientsByCategory($category);
    public function updateClientByUUID($uuid, array $newDetails);


    /* ~ END */

    /* trash */
    public function getDeletedStudents($asDatatables);
    public function getDeletedParents($asDatatables);
    public function getDeletedTeachers($asDatatables);
    /* ~ END */

    /* API External use */
    public function getExistingMenteesAPI();
    public function getExistingMentorsAPI();
    public function getExistingAlumnisAPI();
    public function getParentMenteesAPI();

    /* ~ API External End */

    public function getAlumnisDataTables();
    public function getMenteesDataTables();
    public function getNonMenteesDataTables();
    public function getAllClientByRoleAndStatus($roleName, $statusClient);
    public function getAllChildrenWithNoParents($parentId);
    public function getClientById($clientId);
    public function getClientByUUID($clientUUID);
    public function getClientsById(array $clientIds);
    public function findHandledClient(int $clientId);
    public function getClientByMonthCreatedAt(array $createdAt);
    public function getClientByPhoneNumber($phoneNumber);
    public function getClientBySchool($schoolId);
    public function getClientInSchool(array $schoolIds);
    public function getViewClientById($clientId);
    public function getViewClientByUUID($clientUUID);
    public function checkIfClientIsMentee($clientId);
    public function deleteClient($clientId);
    public function createClient($role, array $clientDetails);
    public function createClientAdditionalInfo(array $infoDetails);
    public function addRole($clientId, $role);
    public function removeRole($clientId, $role);
    public function getParentsByStudentId($studentId);
    public function getParentByParentName($parentName);
    public function createClientRelation($parentId, $studentId);
    public function removeClientRelation($parentId, $studentId);
    public function createManyClientRelation($parentId, array $studentId);
    public function createDestinationCountry($studentId, $destinationCountryDetails);
    public function syncDestinationCountry($studentId, array $destinationCountryDetails);
    public function getInterestedProgram($studentId);
    public function createInterestProgram($studentId, array $interestProgramDetails);
    public function createInterestUniversities($studentId, array $interestUnivDetails);
    public function createInterestMajor($studentId, array $interestMajorDetails);
    public function updateClient($clientId, array $newDetails);
    public function updateClients(array $clientIds, array $newDetails);
    public function updateActiveStatus($clientId, $newStatus);
    public function checkAllProgramStatus($clientId);
    public function checkExistingByPhoneNumber($phone);
    public function checkExistingByEmail($email);
    public function storeUniversityAcceptance($client, array $acceptanceDetails);
    public function getClientHasUniversityAcceptance();

    # dashboard
    public function getCountTotalClientByStatus($status, $month = null);
    public function getClientByStatus($status, $month = null);
    public function getMenteesBirthdayMonthly($month);
    public function getStudentByStudentId($studentId);
    public function getStudentByStudentName($studentName);
    public function getAllClientByRoleAndDate($roleName, $month = null);

    # Raw Client
    public function getAllRawClientDataTables($roleName, $asDatatables = false, array $advanced_filter);
    public function getViewRawClientById($rawClientId);
    public function getRawClientById($rawClientId);
    public function deleteRawClient($rawClientId);
    public function deleteRawClientByUUID($rawClientUUID);
    public function moveBulkToTrash(array $clientIds);
    public function getClientSuggestion(array $clientIds, $roleName);

    # Pic Client
    public function checkActivePICByClient($clientId);
    public function insertPicClient($picDetails);
    public function updatePicClient($picClientId, array $picDetails);
    public function inactivePreviousPIC(UserClient $picDetails);

    # CRM
    public function getStudentFromV1();
    public function getParentFromV1();

    # API
    public function getClientByTicket($ticket_no);
    public function getClientByUUIDforAssessment($uuid);

    
}
