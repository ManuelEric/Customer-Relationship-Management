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
    public function createClientProgram(array $clientProgramDetails);
    public function updateClientProgram($clientProgramId, array $clientProgramDetails);
    public function deleteClientProgram($clientProgramId);

    # sales tracking
    public function getCountProgramByStatus($status, array $dateDetails);
    public function getSummaryProgramByStatus($status, array $dateDetails);
    public function getInitAssessmentProgress($dateDetails);
    public function getConversionLead($dateDetails, $cp_filter);
    public function getLeadSource($dateDetails, $cp_filter);
    public function getConversionTimeSuccessfulPrograms($dateDetails);

    # dashboard
    public function getClientProgramGroupByStatusAndUserArray($cp_filter);
    public function getInitialConsultationInformation($cp_filter);
    public function getInitialMaking($dateDetails, $cp_filter);
    public function getConversionTimeProgress($dateDetails, $cp_filter);
    public function getSuccessProgramByMonth($cp_filter);
    public function getTotalRevenueByProgramAndMonth($cp_filter);
    public function getComparisonBetweenYears($cp_filter);
}