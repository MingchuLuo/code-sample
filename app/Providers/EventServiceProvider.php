<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\UserRegistered' => [
            'App\Listeners\SendUserVerificationEmail',
        ],
        'App\Events\UserVerified' => [
            'App\Listeners\UserVerifiedEventListener',
        ],
        'App\Events\UserSigndIn' => [
            'App\Listeners\LogUserLastLogin',
        ],
        'App\Events\UserSigndOut' => [
            'App\Listeners\UserSigndOutEventListener',
        ],
        'App\Events\PasswordRequested' => [
            'App\Listeners\SendPasswordResetEmail',
        ],
        'App\Events\PasswordTokenVerified' => [
            'App\Listeners\PasswordTokenVerifiedEventListener',
        ],
        'App\Events\PasswordReset' => [
            'App\Listeners\PasswordResetEventListener',
        ],
        'App\Events\PasswordUpdated' => [
            'App\Listeners\PasswordUpdatedEventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
