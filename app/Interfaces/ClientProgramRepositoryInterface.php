<?php

namespace App\Interfaces;

interface ClientProgramRepositoryInterface
{
    public function getAllClientProgramDataTables($searchQuery);
    public function getAllProgramOnClientProgram();
    public function getAllConversionLeadOnClientProgram();
    public function getAllMentorTutorOnClientProgram();
    public function getAllPICOnClientProgram();
    public function getClientProgramById($clientProgramId);
    public function getClientProgramByDetail(array $detail);
    public function createClientProgram(array $clientProgramDetails);
    public function updateClientProgram($clientProgramId, array $clientProgramDetails);
    public function endedClientProgram(int $clientprog_id, array $newDetails);
    public function deleteClientProgram($clientProgramId);

    # sales tracking
    public function getCountProgramByStatus($status, array $dateDetails);
    public function getSummaryProgramByStatus($status, array $dateDetails);
    public function getInitAssessmentProgress($dateDetails);
    public function getConversionLead($dateDetails, $cp_filter = null);
    public function getLeadSource($dateDetails, $cp_filter = null);
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