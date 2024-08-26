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
    
    public function index(Request $request)
    {
        $client_name = $request->get('name') ?? NULL;
        $followup_date = $followedup_date = [];

        if ($request->get('start_followup_date') || $request->get('end_followup_date')) {
            
            $followup_date = [
                'start' => $request->get('start_followup_date'),
                'end' => $request->get('end_followup_date')
            ];
        }
        
        if ($request->get('start_followedup_date') || $request->get('end_followedup_date')) {

            $followedup_date = [
                'start' => $request->get('start_followedup_date'),
                'end' => $request->get('end_followedup_date'),
            ];
        }
        
        $advanced_filter = [
            'client_name' => $client_name,
            'followup_date' => $followup_date,
            'followedup_date' => $followedup_date
        ];


        $fresh_lead = $this->clientRepository->getClientWithoutScheduledFollowup($advanced_filter);
        $scheduled = $this->followupRepository->getScheduledAppointmentsByUser($advanced_filter);
        $followed_up = $this->followupRepository->getFollowedUpAppointmentsByUser($advanced_filter);

        
        $entries = app('App\Services\ClientStudentService')->advancedFilterClient();

        return view('pages.client.board.index')->with(
            [
                'fresh_lead' => $fresh_lead,
                'scheduled' => $scheduled,
                'followed_up' => $followed_up,                
            ] + $entries
        );
    }
}
