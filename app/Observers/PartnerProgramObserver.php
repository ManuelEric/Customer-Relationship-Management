<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\PartnerProg;

class PartnerProgramObserver
{
     /**
     * Handle the PartnerProg "created" event.
     */
    public function created(PartnerProg $partnerProg): void
    {

        // Send to pusher
        event(New MessageSent('rt_partner_program', 'channel_datatable'));
    }

    /**
     * Handle the PartnerProg "updated" event.
     */
    public function updated(PartnerProg $partnerProg): void
    {
        // Send to pusher
        event(New MessageSent('rt_partner_program', 'channel_datatable'));
    }

    /**
     * Handle the PartnerProg "deleted" event.
     */
    public function deleted(PartnerProg $partnerProg): void
    {
        // Send to pusher
        event(New MessageSent('rt_partner_program', 'channel_datatable'));
    }

    /**
     * Handle the PartnerProg "restored" event.
     */
    public function restored(PartnerProg $partnerProg): void
    {
        //
    }

    /**
     * Handle the PartnerProg "force deleted" event.
     */
    public function forceDeleted(PartnerProg $partnerProg): void
    {
        //
    }
}
