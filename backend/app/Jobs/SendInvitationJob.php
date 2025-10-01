<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;
use App\Models\User;
use App\Notifications\CollaboratorInvitedNotification;

class SendInvitationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected Document $document;
    protected User $inviter;
    protected string $email;
    protected string $role;

    /**
     * Create a new job instance.
     */
    public function __construct(Document $document, User $inviter, string $email, string $role) {
        $this->document = $document;
        $this->inviter = $inviter;
        $this->email = $email;
        $this->role = $role;
    }

    /**
     * Execute the job.
     */
    public function handle() {
        // If user exists, notify them; otherwise email a magic link (left for implementation)
        $user = \App\Models\User::where('email',$this->email)->first();
        if ($user) {
            $user->notify(new CollaboratorInvitedNotification($this->document, $this->inviter, $this->role));
        } else {
            // send mail with invitation link (we use Notification to mail)
            \Notification::route('mail', $this->email)
                ->notify(new CollaboratorInvitedNotification($this->document, $this->inviter, $this->role));
        }
    }
}
