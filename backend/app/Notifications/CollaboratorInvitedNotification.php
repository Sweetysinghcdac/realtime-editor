<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Document;
use App\Models\User;

class CollaboratorInvitedNotification extends Notification
{
    use Queueable;
    protected Document $document;
    protected User $inviter;
    protected string $role;

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document, User $inviter, string $role) {
        $this->document = $document;
        $this->inviter = $inviter;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
     public function via($notifiable) {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    
    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject("You were invited to collaborate on '{$this->document->title}'")
            ->line("{$this->inviter->name} invited you as {$this->role}.")
            ->action('Open Document', url("/documents/{$this->document->id}"))
            ->line('If you don\'t have an account, register with this email to accept the invite.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable) {
        return [
            'document_id' => $this->document->id,
            'title' => $this->document->title,
            'inviter' => $this->inviter->only(['id','name','email']),
            'role' => $this->role,
        ];
    }
}
