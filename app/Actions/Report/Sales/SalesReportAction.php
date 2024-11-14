<?php

namespace App\Actions\Report\Sales;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;

class SalesReportAction 
{
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private MainProgRepositoryInterface $mainProgRepository;
    private UserRepositoryInterface $userRepository;
    private SalesTargetRepositoryInterface $salesTargetRepository;
    
    public function __construct(
        ClientProgramRepositoryInterface $clientProgramRepository,
        MainProgRepositoryInterface $mainProgRepository,
        UserRepositoryInterface $userRepository,
        SalesTargetRepositoryInterface $salesTargetRepository,
        )
    {
        $this->clientProgramRepository = $clientProgramRepository;
        $this->mainProgRepository = $mainProgRepository;
        $this->userRepository = $userRepository;
        $this->salesTargetRepository = $salesTargetRepository;
    }

    public function execute(Array $validated)
    {
        $date_details = [
            'start' => $validated['start'],
            'end' => $validated['end'],
        ];

        $additional_filter = [
            'main_prog_id' => $validated['main'],
            'prog_id' => $validated['program'],
            'pic' => $validated['pic'],
        ];

        # contains client program detail [data] & [count] by status (pending, failed, refund, success)
        $sales_report_by_status = $this->clientProgramRepository->rnSummarySalesTracking($date_details, $additional_filter);

        # to report which program has contributed to the company with the amount of their program
        $actual_sales = $this->salesTargetRepository->rnGetSalesDetailFromClientProgram($date_details, $additional_filter);

        # to report the progression time of each program that being followed-up by the sales team
        $init_assessment_progress = $this->clientProgramRepository->rnGetInitAssessmentProgress($date_details, $additional_filter);

        # to report which lead_source and conversion_lead that produces high percentage of incoming client
        $list_of_lead_source_being_used = $this->clientProgramRepository->rnGetLeadSource($date_details);
        $list_of_conversion_lead_being_used = $this->clientProgramRepository->rnGetConversionLead($date_details);

        # to report average conversion time after initial consult til join the program
        $average_conversion_success = $this->clientProgramRepository->rnGetConversionTimeSuccessfulPrograms($date_details);

        # for master data purposes that being displayed to help user use the searching features
        $list_of_main_program = $this->mainProgRepository->rnGetAllMainProg();
        $list_of_pic = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');

        return compact(
            'sales_report_by_status', 
            'init_assessment_progress', 
            'list_of_lead_source_being_used', 
            'list_of_conversion_lead_being_used',
            'average_conversion_success',
            'list_of_main_program',
            'list_of_pic',
            'actual_sales',
            'date_details'
        );
    }
}