<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;
    protected FollowupRepositoryInterface $followupRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->followupRepository = $followupRepository;
    }
    
    public function index()
    {

        $fresh_lead = $this->clientRepository->getClientWithoutScheduledFollowup();
        $scheduled = $this->followupRepository->getScheduledAppointmentsByUser();
        $followed_up = $this->followupRepository->getFollowedUpAppointmentsByUser();
        // $to_be_invoiced = 
        // $awaiting_payment = 

        return view('pages.client.board.index')->with(
            [
                'fresh_lead' => $fresh_lead,
                'scheduled' => $scheduled,
                'followed_up' => $followed_up
            ]
        );
    }
}
