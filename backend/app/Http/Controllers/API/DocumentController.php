<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Events\DocumentUpdated;
use App\Jobs\NotifyCollaboratorsJob;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Redis;
use App\Events\UserEditingDocument;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
{
    $user = auth()->user();

    $documents = Document::where('owner_id', $user->id)
        ->orWhereHas('collaborators', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['owner', 'collaborators'])
        ->get();

    return response()->json($documents);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $req) {
        $data = $req->validate(['title'=>'required|string|max:255','content'=>'nullable|string']);
        $doc = Document::create([
            'owner_id' => $req->user()->id,
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
        ]);
        // Attach owner as editor in pivot
        $doc->collaborators()->attach($req->user()->id, ['role'=>'editor','accepted_at'=>now()]);
        return response()->json($doc,201);
    }

   
    public function show(Document $document)
    {
        $this->authorize('view', $document);
        $canEdit = auth()->user()->can('update', $document);

        return response()->json($document->load('collaborators','owner'));
    }


   
 public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $data = $request->validate(['title'=>'sometimes|string|max:255','content'=>'nullable|string']);

        $oldContent = $document->content;
        $document->update($data);

        if (array_key_exists('content', $data) && $data['content'] !== $oldContent) {
            $document->versions()->create([
                'user_id' => $request->user()?->id,
                'content' => $data['content'],
                'summary' => 'Auto-save ' . now()->toDateTimeString(),
            ]);
        }
        
        
        broadcast(new DocumentUpdated($document->fresh(), $request->user()))
            ->toOthers(); // Event dispatch happens here, using channel from the event class.
        

        // queue notifications (pass IDs)
        NotifyCollaboratorsJob::dispatch($document->id, $request->user()->id);

        return response()->json($document->fresh('collaborators','owner'));
    }


    public function versions(Document $document)
    {
        $this->authorize('view', $document);

        return $document->versions()->with('user')->latest()->get();
    }

    public function revert(Document $document, DocumentVersion $version)
    {
        $this->authorize('update', $document);

        $document->update(['content' => $version->content]);

        return response()->json($document);
    }


    public function destroy(Request $req, Document $document) {
        $this->authorize('delete',$document);
        $document->delete();
        return response()->json(['message'=>'deleted']);
    }




 
    public function shared(Request $request)
    {
        $user = $request->user();

        $documents = Document::whereHas('collaborators', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->whereNotNull('accepted_at');
        })
        ->with(['owner', 'collaborators'])
        ->get();

        return response()->json($documents);
    }

}