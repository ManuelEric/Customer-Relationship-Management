<?php

namespace App\Interfaces;

interface ClientProgramRepositoryInterface
{
    public function getAllClientProgramDataTables_DetailUser($searchQuery);
    public function getAllClientProgramDataTables($searchQuery);
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
    public function updateFewField(int $clientprog_id, array $newDetails);
    public function endedClientProgram(int $clientprog_id, array $newDetails);
    public function endedClientPrograms(array $clientprog_ids, array $newDetails);
    public function deleteClientProgram($clientProgramId);

    # bundling
    public function getBundleProgramByUUID($uuid);
    public function getBundleProgramDetailByBundlingId($bundlingId);
    public function createBundleProgram($uuid, $clientProgramDetails);
    public function deleteBundleProgram($bundling_id);

    # sales tracking
    public function getCountProgramByStatus($status, array $dateDetails, array $additionalFilter = []);
    public function getSummaryProgramByStatus($status, array $dateDetails, array $additionalFilter);
    public function getInitAssessmentProgress($dateDetails, array $additionalFilter);
    public function getConversionLead($dateDetails, $cp_filter = null);
    public function getConversionLeadDetails($filter);
    public function getLeadSource($dateDetails, $cp_filter = null);
    public function getLeadSourceDetails($filter);
    public function getConversionTimeSuccessfulPrograms($dateDetails);

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