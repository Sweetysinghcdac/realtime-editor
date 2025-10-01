<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentChangedNotification;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\DocumentUpdated;

class NotifyCollaboratorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $documentId;
    public int $userId;

    public function __construct(int $documentId, int $userId)
    {
        $this->documentId = $documentId;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $document = Document::find($this->documentId);
        $user = User::find($this->userId);

        broadcast(new DocumentUpdated($document, $user));
    }
}

