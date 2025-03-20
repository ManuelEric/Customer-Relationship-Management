<?php

namespace App\Logging;

use App\Http\Traits\SendBotTelegramTrait;
use Illuminate\Support\Facades\Log;

class AddInstructionsProcessor
{
    use SendBotTelegramTrait;
    public function __invoke($record)
    {
        if ($record['level_name'] === 'ERROR') {
            // $record['extra']['instructions'] = $this->SendMessageTele(env("TOKEN_BOT_TELEGRAM"), env("TELE_ID"), 'log-error', $record['message']);
        }

        return $record;
    }

}