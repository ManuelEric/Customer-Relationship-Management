<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;

class AddCustomHeader
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(MessageSending $event)
    {
        $message = $event->message;
        $message->getHeaders()->addTextHeader('X-MT-Category', 'EduALL CRM');
    }
}
