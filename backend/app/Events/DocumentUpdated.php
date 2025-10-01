<?php
namespace App\Events;
use App\Models\Document;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class DocumentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $documentId;
    public string $content;
    public ?array $editor;

    public function __construct(Document $document, ?User $editor = null)
    {
        $this->documentId = $document->id;
        $this->content = $document->content ?? '';
        $this->editor = $editor ? ['id'=>$editor->id,'name'=>$editor->name] : null;
    }

    /**
     * Get the channels the event should broadcast on.
     * * IMPORTANT FIX: Changed channel name to include 'presence-' 
     * to match the channel authorized by Echo's .join() and routes/channels.php.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel("document.{$this->documentId}");
    }

    public function broadcastAs(): string
    {
        return 'DocumentUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'document_id' => $this->documentId,
            'content' => $this->content,
            'updated_by' => $this->editor,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
