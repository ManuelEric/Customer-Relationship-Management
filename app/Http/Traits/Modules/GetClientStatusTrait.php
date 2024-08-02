<?php
namespace App\Http\Traits\Modules;

trait GetClientStatusTrait {

    public function clientStatus($month)
    {
        $asDatatables = $groupBy = false;
        $total_newLeads = $this->clientRepository->countClientByCategory('new-lead');
        $monthly_new_newLeads = $this->clientRepository->countClientByCategory('new-lead', $month);

        $total_potential_client = $this->clientRepository->countClientByCategory('potential');
        $monthly_new_potential_client = $this->clientRepository->countClientByCategory('potential', $month);

        $total_existingMentees = $this->clientRepository->countClientByCategory('mentee');
        $monthly_new_existingMentees = $this->clientRepository->countClientByCategory('mentee', $month);

        $total_existingNonMentees = $this->clientRepository->countClientByCategory('non-mentee');
        $monthly_new_existingNonMentees = $this->clientRepository->countClientByCategory('non-mentee', $month);

        $total_alumniMentees = $this->clientRepository->countClientByCategory('alumni-mentee');
        $monthly_new_alumniMentees = $this->clientRepository->countClientByCategory('alumni-mentee', $month);

        $total_alumniNonMentees = $this->clientRepository->countClientByCategory('alumni-non-mentee');
        $monthly_new_alumniNonMentees = $this->clientRepository->countClientByCategory('alumni-non-mentee', $month);

        $total_parent = $this->clientRepository->countClientByRole('Parent');
        $monthly_new_parent = $this->clientRepository->countClientByRole('Parent', $month);

        $total_teacher = $this->clientRepository->countClientByRole('Teacher/Counselor');
        $monthly_new_teacher = $this->clientRepository->countClientByRole('Teacher/Counselor', $month);

        # data at the top of dashboard
        $response['totalClientInformation'] = [
            'newLeads' => [
                'old' => $total_newLeads - $monthly_new_newLeads,
                'new' => $monthly_new_newLeads,
                'percentage' => $this->calculatePercentage($total_newLeads, $monthly_new_newLeads)
            ], # prospective
            'potential' => [
                'old' => $total_potential_client - $monthly_new_potential_client,
                'new' => $monthly_new_potential_client,
                'percentage' => $this->calculatePercentage($total_potential_client, $monthly_new_potential_client)
            ], # potential
            'existingMentees' => [
                'old' => $total_existingMentees - $monthly_new_existingMentees,
                'new' => $monthly_new_existingMentees,
                'percentage' => $this->calculatePercentage($total_existingMentees, $monthly_new_existingMentees)
            ], # current
            'existingNonMentees' => [
                'old' => $total_existingNonMentees - $monthly_new_existingNonMentees,
                'new' => $monthly_new_existingNonMentees,
                'percentage' => $this->calculatePercentage($total_existingNonMentees, $monthly_new_existingNonMentees)
            ], # current
            'alumniMentees' => [
                'old' => $total_alumniMentees - $monthly_new_alumniMentees,
                'new' => $monthly_new_alumniMentees,
                'percentage' => $this->calculatePercentage($total_alumniMentees, $monthly_new_alumniMentees)
            ],
            'alumniNonMentees' => [
                'old' => $total_alumniNonMentees - $monthly_new_alumniNonMentees,
                'new' => $monthly_new_alumniNonMentees,
                'percentage' => $this->calculatePercentage($total_alumniNonMentees, $monthly_new_alumniNonMentees)
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
            ],
            'raw' => [
                'student' => $this->clientRepository->getAllRawClientDataTables('student', $asDatatables, [])->count(),
                'parent' => $this->clientRepository->getAllRawClientDataTables('parent', $asDatatables, [])->count(),
                'teacher' => $this->clientRepository->getAllRawClientDataTables('teacher/counselor', $asDatatables, [])->count(),
            ],
            'inactive' => [
                'student' => $this->clientRepository->getInactiveStudent(false)->count(),
                'parent' => $this->clientRepository->getInactiveParent(false)->count(),
                'teacher' => $this->clientRepository->getInactiveTeacher(false)->count(),
            ]
        ];
        $response['followUpReminder'] = $this->followupRepository->getAllFollowupWithin(7);
        $response['menteesBirthday'] = $this->clientRepository->getMenteesBirthdayMonthly($month);

        return with($response);
    }
}