<?php

namespace App\Listeners;

use App\Events\PasswordRequested;
use App\Mail\PasswordResetMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetEmail implements ShouldQueue
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
     * @param  PasswordRequested  $event
     * @return void
     */
    public function handle(PasswordRequested $event)
    {
        //
        $user = $event->user;
        $token = $event->token;
        Mail::to($user)->send(new PasswordResetMail($user, $token));
    }
}
