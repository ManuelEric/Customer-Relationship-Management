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
        $updated_application_year = false;
        // update application year to be one year before graduation year
        if ($userClient->graduation_year != null) {
            $userClient->application_year = $userClient->graduation_year - 1;
            $userClient->save();

            $updated_application_year = true;
        }

        if ($userClient->graduation_year_now != null && $updated_application_year == false) {
            $userClient->application_year = $userClient->graduation_year_now - 1;
            $userClient->save();
        }
    }

    /**
     * Handle the UserClient "updated" event.
     */
    public function updated(UserClient $userClient): void
    {
        if ($userClient->application_year == null) { // if application year is null then we need to set it
            $updated_application_year = false;
            // update application year to be one year before graduation year
            if ($userClient->graduation_year != null) {
                $userClient->application_year = $userClient->graduation_year - 1;
                $userClient->save();

                $updated_application_year = true;
            }

            if ($userClient->graduation_year_now != null && $updated_application_year == false) {
                $userClient->application_year = $userClient->graduation_year_now - 1;
                $userClient->save();
            }
        }
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
