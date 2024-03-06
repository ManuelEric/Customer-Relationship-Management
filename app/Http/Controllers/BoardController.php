<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function index()
    {

        $new_opportunity = $this->clientRepository->getClientWithoutScheduledFollowup();
        $scheduled = $this->clientRepository->getClientWithScheduledFollowup(0);
        $followed_up = $this->clientRepository->getClientWithScheduledFollowup(1);
        $delayed = $this->clientRepository->getClientWithScheduledFollowup(2);

        return view('pages.client.board.index')->with(
            [
                'new_opportunity' => $new_opportunity,
                'scheduled' => $scheduled,
                'followed_up' => $followed_up,
                'delayed' => $delayed
            ]
        );
    }
}
