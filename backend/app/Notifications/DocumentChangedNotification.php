<?php
namespace App\Notifications;
// ... (use statements)
use App\Models\Document;
use App\Models\User; // Don't forget to import User

class DocumentChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $document; // The document being edited
    public $editor;   // The user who made the edit

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document, User $editor) // <-- Add parameters
    {
        // Use property promotion (PHP 8.0+) or simply assign them
        $this->document = $document;
        $this->editor = $editor;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Added 'database' for completeness
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Use the properties to create a meaningful message
        return (new MailMessage)
            ->subject('The document "' . $this->document->title . '" has been updated!')
            ->line($this->editor->name . ' just made changes to the document you are collaborating on.')
            ->action('View Document', url('/documents/' . $this->document->id))
            ->line('Thank you for using our application!');
    }
    
    /**
     * Get the array representation for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'editor_name' => $this->editor->name,
            'editor_id' => $this->editor->id,
        ];
    }
}