<?php
namespace App\Http\Traits\Modules;

trait GetClientStatusTrait {

    public function clientStatus($month)
    {
        $total_prospective_client = $this->clientRepository->getCountTotalClientByStatus(0);
        $monthly_new_prospective_client = $this->clientRepository->getCountTotalClientByStatus(0, $month);

        $total_potential_client = $this->clientRepository->getCountTotalClientByStatus(1);
        $monthly_new_potential_client = $this->clientRepository->getCountTotalClientByStatus(1, $month);

        $total_current_client = $this->clientRepository->getCountTotalClientByStatus(2);
        $monthly_new_current_client = $this->clientRepository->getCountTotalClientByStatus(2, $month);

        $total_completed_client = $this->clientRepository->getCountTotalClientByStatus(3);
        $monthly_new_completed_client = $this->clientRepository->getCountTotalClientByStatus(3, $month);

        $total_mentee = $this->clientRepository->getAllClientByRole('mentee')->count();
        $monthly_new_mentee = $this->clientRepository->getAllClientByRole('mentee', $month)->count();

        $total_alumni = $this->clientRepository->getAllClientByRole('alumni')->count();
        $monthly_new_alumni = $this->clientRepository->getAllClientByRole('alumni', $month)->count();

        $total_parent = $this->clientRepository->getAllClientByRole('parent')->count();
        $monthly_new_parent = $this->clientRepository->getAllClientByRole('parent', $month)->count();

        $total_teacher = $this->clientRepository->getAllClientByRole('Teacher/Counselor')->count();
        $monthly_new_teacher = $this->clientRepository->getAllClientByRole('Teacher/Counselor', $month)->count();

        # data at the top of dashboard
        $response['totalClientInformation'] = [
            'prospective' => [
                'old' => $total_prospective_client - $monthly_new_prospective_client,
                'new' => $monthly_new_prospective_client,
                'percentage' => $this->calculatePercentage($total_prospective_client, $monthly_new_prospective_client)
            ], # prospective
            'potential' => [
                'old' => $total_potential_client - $monthly_new_potential_client,
                'new' => $monthly_new_potential_client,
                'percentage' => $this->calculatePercentage($total_potential_client, $monthly_new_potential_client)
            ], # potential
            'current' => [
                'old' => $total_current_client - $monthly_new_current_client,
                'new' => $monthly_new_current_client,
                'percentage' => $this->calculatePercentage($total_current_client, $monthly_new_current_client)
            ], # current
            'completed' => [
                'old' => $total_completed_client - $monthly_new_completed_client,
                'new' => $monthly_new_completed_client,
                'percentage' => $this->calculatePercentage($total_completed_client, $monthly_new_completed_client)
            ], # current
            'mentee' => [
                'old' => $total_mentee - $monthly_new_mentee,
                'new' => $monthly_new_mentee,
                'percentage' => $this->calculatePercentage($total_mentee, $monthly_new_mentee)
            ],
            'alumni' => [
                'old' => $total_alumni - $monthly_new_alumni,
                'new' => $monthly_new_alumni,
                'percentage' => $this->calculatePercentage($total_alumni, $monthly_new_alumni)
            ],
            'parent' => [
                'old' => $total_parent - $monthly_new_parent,
                'new' => $monthly_new_parent,
                'percentage' => $this->calculatePercentage($total_parent, $monthly_new_parent)
            ],
            'teacher_counselor' => [
                'old' => $total_teacher - $monthly_new_teacher,
                'new' => $monthly_new_teacher,
                'percentage' => $this->calculatePercentage($total_teacher, $monthly_new_teacher)
            ]
        ];
        $response['followUpReminder'] = $this->followupRepository->getAllFollowupWithin(7);
        $response['menteesBirthday'] = $this->clientRepository->getMenteesBirthdayMonthly($month);

        return with($response);
    }
}