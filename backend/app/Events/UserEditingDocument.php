<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
// class UserEditingDocument implements ShouldBroadcastNow
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $documentId;
//     public $user;
//     public $isEditing;

//     public function __construct($documentId, $user, $isEditing)
//     {
//         $this->documentId = $documentId;
//         $this->user = ['id' => $user->id, 'name' => $user->name];
        
//         $this->isEditing = $isEditing;
//     }

//     public function broadcastOn()
//     {
//         return new PresenceChannel("document.{$this->documentId}");
//     }

//     // public function broadcastAs()
//     // {
//     //     return 'UserEditing';
//     // }
//     public function broadcastAs()
//     {
//         return 'UserEditingDocument';
//     }
// }

class UserEditingDocument implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $isEditing;

    public function __construct($user, $isEditing)
    {
        $this->user = $user;
        $this->isEditing = $isEditing;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('document.' . request()->route('id'));
    }

    public function broadcastAs()
    {
        return 'UserEditingDocument';
    }
}

