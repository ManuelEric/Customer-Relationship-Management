<?php

namespace App\Observers;

use App\Models\UserClient;

class UserClientObserver
{
    /**
     * Handle the UserClient "created" event.
     */
    public function created(UserClient $userClient): void
    {
        // update application year to be one year before graduation year        
        $userClient->application_year = $userClient->graduation_year-1;
        $userClient->save();
    }

    /**
     * Handle the UserClient "updated" event.
     */
    public function updated(UserClient $userClient): void
    {
        //
    }

    /**
     * Handle the UserClient "deleted" event.
     */
    public function deleted(UserClient $userClient): void
    {
        //
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
