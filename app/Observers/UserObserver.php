<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // if certain user's active status was changed to 0
        // then put status in client mentor also 0
        // and deactivate user type
        if ($user->wasChanged('active') && $user->active == 0) {
            foreach ($user->mentorClient as $mentoring) { // client mentor
                $mentoring->pivot->status = 0;
                $mentoring->pivot->save();
            }
        }

    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
