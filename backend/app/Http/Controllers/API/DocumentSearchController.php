<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;

class DocumentSearchController extends Controller
{
     public function __invoke(Request $request)
    {
        $query = $request->validate(['q' => 'required|string|max:100'])['q'];

        return Document::query()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->where(function ($q) {
                $q->where('owner_id', auth()->id())
                  ->orWhereHas('collaborators', fn($c) => $c->where('user_id', auth()->id()));
            })
            ->get();
    }
}
