<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;


Broadcast::channel('document.{documentId}', function ($user, $documentId) {
    if (!$user) return false;

    // owner or accepted collaborator allowed
    $isOwner = \DB::table('documents')->where('id', $documentId)->where('owner_id', $user->id)->exists();
    if ($isOwner) return ['id'=>$user->id,'name'=>$user->name];

    $isCollab = \DB::table('document_user')
        ->where('document_id', $documentId)
        ->where('user_id', $user->id)
        ->whereNotNull('accepted_at')
        ->exists();

    return $isCollab ? ['id'=>$user->id,'name'=>$user->name] : false;
});



Broadcast::channel('invitations.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId; 
    // Only allow the invited user to listen
});
