<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\User;
use App\Jobs\SendInvitationJob;

class CollaborationController extends Controller
{
     public function invite(Request $req, Document $document) {
        $this->authorize('invite',$document);
        $data = $req->validate(['email'=>'required|email','role'=>'required|in:viewer,editor']);
        $invitee = User::where('email',$data['email'])->first();
        if (!$invitee) {
            // Option: create a placeholder/invitation record (or ask them to register)
            // For simplicity, we'll just send an email invite using job; the job can create user if required.
        } else {
            // attach pivot row (if exists, update role)
            $document->collaborators()->syncWithoutDetaching([
                $invitee->id => ['role'=>$data['role'],'invited_by'=>$req->user()->id,'accepted_at'=>null]
            ]);
        }
        SendInvitationJob::dispatch($document, $req->user(), $data['email'], $data['role']);
        return response()->json(['message'=>'invitation sent']);
    }

    public function accept(Request $req, Document $document) {
        $user = $req->user();
        $document->collaborators()->updateExistingPivot($user->id,['accepted_at'=>now()]);
        return response()->json(['message'=>'accepted']);
    }

    public function revoke(Request $req, Document $document, User $user) {
        $this->authorize('invite',$document); // only owner
        $document->collaborators()->detach($user->id);
        return response()->json(['message'=>'revoked']);
    }
}
