<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;

trait GenerateTicketIdTrait
{
    public function tnGenerateTicketId(){
        do {

            $ticket_id = Str::random(4);
            $isUnique = $this->clientEventRepository->isTicketIDUnique($ticket_id);
        } while ($isUnique === false);

        return $ticket_id;
    }
}