<?php

namespace App\Interfaces;

interface ClientProgramRepositoryInterface
{
    public function getAllClientProgramDataTables_DetailUser($searchQuery);
    public function getAllClientProgramDataTables($searchQuery, $asDatatables = true);
    public function getAllProgramOnClientProgram();
    public function getAllMainProgramOnClientProgram();
    public function getAllConversionLeadOnClientProgram();
    public function getAllMentorTutorOnClientProgram();
    public function getAllPICOnClientProgram();
    public function getClientProgramById($clientProgramId);
    public function getClientProgramByClientId($clientId);
    public function getClientProgramByDetail(array $detail);
    public function createClientProgram(array $clientProgramDetails);
    public function updateClientProgram($clientProgramId, array $clientProgramDetails);
    public function updateClientPrograms($clientprogram_ids, array $clientprogram_details);
    public function updateFewField(int $clientprog_id, array $newDetails);
    public function endedClientProgram(int $clientprog_id, array $newDetails);
    public function endedClientPrograms(array $clientprog_ids, array $newDetails);
    public function deleteClientProgram($clientProgramId);
    public function checkProgramIsAdmission($clientprog_id);
    public function rnDomicileTracker($date_range, $uuid);
    public function getClientProgramAdmissionByClientId($clientId);


    # bundling
    public function getBundleProgramByUUID($uuid);
    public function getBundleProgramDetailByBundlingId($bundlingId);
    public function createBundleProgram($uuid, $clientProgramDetails);
    public function deleteBundleProgram($bundling_id);

    # sales tracking
    public function rnSummarySalesTracking(array $date_details, array $additional_filter = []): Array;
    public function getCountProgramByStatus($status, array $date_details, array $additional_filter = []);
    public function getSummaryProgramByStatus($status, array $date_details, array $additional_filter = []);
    public function rnGetInitAssessmentProgress(array $date_details, array $additional_filter = []);
    public function rnGetConversionLead(array $date_details, $cp_filter = null);
    public function getConversionLeadDetails($filter);
    public function rnGetLeadSource($date_details, $cp_filter = null);
    public function getLeadSourceDetails($filter);
    public function rnGetConversionTimeSuccessfulPrograms($date_details);

    # dashboard
    public function getClientProgramGroupByStatusAndUserArray($cp_filter);
    public function getClientProgramGroupDataByStatusAndUserArray($cp_filter);
    public function getInitialConsultationInformation($cp_filter);
    public function getInitialMaking($dateDetails, $cp_filter);
    public function getConversionTimeProgress($dateDetails, $cp_filter);
    public function getSuccessProgramByMonth($cp_filter);
    public function getDetailSuccessProgramByMonthAndProgram($cp_filter);
    public function getTotalRevenueByProgramAndMonth($cp_filter);
    public function getComparisonBetweenYears($cp_filter);
    public function getActiveClientProgramAfterProgramEnd();

    # CRM
    public function getClientProgramFromV1();
}