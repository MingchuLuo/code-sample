<?php

namespace App\Events;

use App\Models\Activity\UserProgram;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProgramStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userProgram;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserProgram $userProgram)
    {
        $this->userProgram = $userProgram;
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
