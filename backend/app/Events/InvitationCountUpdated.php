<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationCountUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $count;

    public function __construct($userId, $count)
    {
        $this->userId = $userId;
        $this->count  = $count;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("invitations.{$this->userId}");
    }

    public function broadcastAs()
    {
        return 'InvitationCountUpdated';
    }
}
