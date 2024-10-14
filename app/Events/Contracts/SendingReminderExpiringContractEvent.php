<?php

namespace App\Events\Contracts;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendingReminderExpiringContractEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private array $list_contracts_expired_soon;
    private string $title_for_mail_data;
    /**
     * Create a new event instance.
     */
    public function __construct($list_contracts_expired_soon, $title_for_mail_data)
    {
        $this->list_contracts_expired_soon = $list_contracts_expired_soon;
        $this->title_for_mail_data = $title_for_mail_data;
    }

}
