<?php

namespace App\Observers;

use App\Events\UpdateDatatableEvent;
use App\Models\UserClient;
use Illuminate\Support\Facades\Log;

class ClientObserver
{
    /**
     * Handle the UserClient "created" event.
     */
    public function created(UserClient $userClient): void
    {
        event(New UpdateDatatableEvent(
            tableName: 'rt_client'
        ));
    }

    /**
     * Handle the UserClient "updated" event.
     */
    public function updated(UserClient $userClient): void
    {
        event(New UpdateDatatableEvent(
            tableName: 'rt_client'
        ));
    }

    /**
     * Handle the UserClient "deleted" event.
     */
    public function deleted(UserClient $userClient): void
    {
        event(New UpdateDatatableEvent(
            tableName: 'rt_client'
        ));
    }

    /**
     * Handle the UserClient "restored" event.
     */
    public function restored(UserClient $userClient): void
    {
        //
    }

    /**
     * Handle the UserClient "force deleted" event.
     */
    public function forceDeleted(UserClient $userClient): void
    {
        //
    }
}
