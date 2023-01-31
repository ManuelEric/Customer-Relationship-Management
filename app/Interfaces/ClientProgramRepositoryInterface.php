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
    public function getCountProgramByStatus($status);
    public function getSummaryProgramByStatus($status);
    public function getInitAssessmentProgress();
    public function getConversionLead();
    public function getLeadSource();
}