<?php

namespace App\Http\Traits;

// use App\Interfaces\ClientEventRepositoryInterface;
use App\Models\ClientEvent;
use App\Models\UserClient;
// use App\Repositories\ClientEventRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

trait RegisterExpressTrait
{
    // private ClientEventRepositoryInterface $clientEventRepository;

    // public function _construct(ClientEventRepository $clientEventRepository){
    //     $this->clientEventRepository = $clientEventRepository;
    // }

    public function register($email, $event_id, $notes)
    {
        $data = [
            'success' => false,
            'already_join' => false,
        ];

        $isSuccess = false;
        
        DB::beginTransaction();

        try {
            $client = UserClient::where('mail', $email)->first();
            $client_id = $client->id;


            // $checkJoined = $this->clientEventRepository->getAllClientEvents()->where('client_id', $client_id)->where('event_id', $event_id)->first();
            $checkJoined = ClientEvent::where('client_id', $client_id)->where('event_id', $event_id)->first();


            $clientEvents = [
                'client_id' => $client_id,
                'child_id' => $client->childrens->count() > 0 ? $client->childrens[0]->id : null,
                'event_id' => $event_id,
                'lead_id' => 'LS012',
                'status' => $notes == 'VVIP' ? 1 : 0,
                'notes' => $notes,
                'joined_date' => Carbon::now(),
            ];

            $data['email'] = $client->mail;
            $data['client'] = [
                'name' => $client->full_name
            ];
            $data['title'] = "You have Successfully registered STEM+ WONDERLAB";
            $data['notes'] = $notes;
            $data['event'] = [
                'eventName' => 'STEM+ Wonderlab',
                'eventDate' => Carbon::now()
            ];
            
            $data['success'] = true;
            $data['already_join'] = true;

            if (!isset($checkJoined)) {
                ClientEvent::create($clientEvents);
                // $this->clientRepository->updateClient($client_id, ['register_as' => 'parent']);
                UserClient::whereId($client_id)->update(['register_as' => 'parent']);
                if ($client->childrens->count() > 0) {
                    UserClient::whereId($client->childrens[0]->id)->update(['register_as' => 'parent']);
                    // $this->clientRepository->updateClient($client->childrens[0]->id, ['register_as' => 'parent']);
                }
                // $this->clientEventRepository->createClientEvent($clientEvents);

                Mail::send('mail-template.thanks-email', $data, function ($message) use ($data) {
                    $message->to($data['email'], $data['client']['name'])
                        ->subject($data['title']);
                });

                $data['success'] = true;
                $data['already_join'] = false;  
            }
            
            
            Log::info('Client ' . $data['email'] . ' successfully register express');
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            Log::error('Register express client event failed : ' . $e->getMessage());

            $data['success'] = false;
        }

        return $data;
    }
}
