<?php

namespace App\Listeners\Contracts;

use App\Events\Contracts\SendingReminderExpiringContractEvent;
use App\Mail\ContractExpirationEmail;

class ReminderOfExpiringContractListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendingReminderExpiringContractEvent $event): void
    {
        // $event->notify(new ContractExpirationEmail());
    }
}
