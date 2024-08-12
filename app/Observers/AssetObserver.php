<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Asset;

class AssetObserver
{
    /**
     * Handle the UserClient "created" event.
     */
    public function created(Asset $asset): void
    {

        // Send to pusher
        event(New MessageSent('rt_asset', 'channel_datatable'));
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        // Send to pusher
        event(New MessageSent('rt_asset', 'channel_datatable'));
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        // Send to pusher
        event(New MessageSent('rt_asset', 'channel_datatable'));
    }

    /**
     * Handle the Asset "restored" event.
     */
    public function restored(Asset $asset): void
    {
        //
    }

    /**
     * Handle the Asset "force deleted" event.
     */
    public function forceDeleted(UserClient $asset): void
    {
        //
    }
}
