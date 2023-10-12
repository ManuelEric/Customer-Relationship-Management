<?php
namespace App\Http\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;

trait SendBotTelegramTrait {

    public function sendMessageTele($tokenBot, $teleId, $message)
    {
        $client  = new Client();
        $url = "https://api.telegram.org/".$tokenBot."/sendMessage";
        
        $client->request('GET', $url, [
            'json' =>[
            "chat_id" => $teleId, 
            "text" => 'CRM_' . date('y/m/d') .' '. substr($message, 0, 100), # Limit message telegram
            ]
        ]);
    }
}