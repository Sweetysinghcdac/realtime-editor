<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Events\UserEditingDocument;
use Illuminate\Support\Facades\Auth;

class PresenceController extends Controller
{
     public function startEditing(Request $request, $documentId)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message'=>'unauthenticated'], 401);
        $key = "document:editing:{$documentId}";
        Redis::sadd($key, $user->id);
        // optionally set TTL
        Redis::expire($key, 3600);
        return response()->json(['message'=>'editing_started']);
    }

    // Stop editing: remove user id
    public function stopEditing(Request $request, $documentId)
    {
        $user = $request->user();
        $key = "document:editing:{$documentId}";
        Redis::srem($key, $user->id);
        return response()->json(['message'=>'editing_stopped']);
    }

    // Get current editors (for debugging or initial state)
    public function editors(Request $request, $documentId)
    {
        $key = "document:editing:{$documentId}";
        $ids = array_map('intval', Redis::smembers($key) ?: []);
        $users = \App\Models\User::whereIn('id',$ids)->get(['id','name']);
        return response()->json($users);
    }
}
