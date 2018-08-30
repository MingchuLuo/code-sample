<?php

namespace App\Events;

use App\Models\Activity\UserSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SessionSkipped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userSession;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserSession $userSession)
    {
        $this->userSession = $userSession;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
