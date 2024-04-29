<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    private $default_points = 100;
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->ledgers()->create(['debit'=> $this->default_points]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
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
