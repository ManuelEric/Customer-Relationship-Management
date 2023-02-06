<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;
    protected FollowupRepositoryInterface $followupRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->followupRepository = $followupRepository;
    }

    public function index(Request $request)
    {   
        return $this->indexSales($request);
    }

    # sales dashboard

    public function indexSales($request)
    {

        $totalClientByStatus = [
            'prospective' => $this->clientRepository->getCountTotalClientByStatus(0), # prospective
            'potential' => $this->clientRepository->getCountTotalClientByStatus(1), # potential
            'current' => $this->clientRepository->getCountTotalClientByStatus(2), # current
            'completed' => $this->clientRepository->getCountTotalClientByStatus(3), # current
            'mentee' => $this->clientRepository->getAllClientByRole('mentee')->count(),
            'alumni' => $this->clientRepository->getAllClientByRole('alumni')->count(),
            'parent' => $this->clientRepository->getAllClientByRole('parent')->count(),
            'teacher_counselor' => $this->clientRepository->getAllClientByRole('Teacher/Counselor')->count()
        ];

        $followUpReminder = $this->followupRepository->getAllFollowupWithin(7);
        $menteesBirthday = $this->clientRepository->getMenteesBirthdayMonthly(date('m'));

        return view('pages.dashboard.index')->with(
            [
                'totalClientInformation' => $totalClientByStatus,
                'followUpReminder' => $followUpReminder,
                'menteesBirthday' => $menteesBirthday
            ]
        );
    }

}
