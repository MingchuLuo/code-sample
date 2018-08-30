<?php

namespace App\Listeners;

use App\Events\UserSigndIn;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogUserLastLogin
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
     * @param  UserSigndIn  $event
     * @return void
     */
    public function handle(UserSigndIn $event)
    {
        //
        $user = $event->user;
        $user->last_login = new Carbon();
        $user->save();
    }
}
