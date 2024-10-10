<?php

namespace App\Listeners\Contracts;

use App\Events\Contracts\SendingReminderExpiringContractEvent;
use App\Mail\ContractExpirationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
