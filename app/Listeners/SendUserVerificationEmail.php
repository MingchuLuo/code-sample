<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\UserVerificationMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendUserVerificationEmail implements ShouldQueue
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
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        //
        $user = $event->user;
        $token = $event->token;
        Mail::to($user)->send(new UserVerificationMail($user, $token));
    }
}
