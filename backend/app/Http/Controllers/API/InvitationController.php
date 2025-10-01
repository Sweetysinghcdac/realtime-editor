<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use App\Notifications\CollaboratorInvitedNotification;
use App\Jobs\SendInvitationJob;
use App\Events\InvitationCountUpdated;

class InvitationController extends Controller
{
    public function index()
    {
        return Invitation::with(['document.owner', 'inviter'])
            ->where('invitee_id', Auth::id())
            ->where('status', 'pending')
            ->get();
    }

    // Send an invitation
    public function store(Request $request, Document $document)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:viewer,editor',
        ]);

        $invitee = User::where('email', $request->email)->firstOrFail();

        $invitation = Invitation::create([
            'document_id' => $document->id,
            'inviter_id' => Auth::id(),
            'invitee_id' => $invitee->id,
            'role' => $request->role,
            'status' => 'pending',
        ]);

        // Send notification
       
        SendInvitationJob::dispatch($document, Auth::user(), $request->email, $request->role);
        // Fire broadcast event
        $count = $invitee->pendingInvitationsCount();
        event(new InvitationCountUpdated($invitee->id, $count));

        return response()->json([
            'message' => 'Invitation sent successfully.',
            'invitation' => $invitation
        ], 201);
    }

    public function accept(Invitation $invitation)
    {
        $this->authorize('respond', $invitation);

        $invitation->update(['status' => 'accepted']);

        $document = $invitation->document;
        $invitee  = $invitation->invitee;

        if (! $document->collaborators()->where('user_id', $invitee->id)->exists()) {
            $document->collaborators()->attach($invitee->id, [
                'role' => $invitation->role,
                'accepted_at' => now(),
                'invited_by' => $invitation->inviter_id,
            ]);
        }

        // Broadcast update

        $count = $invitation->invitee->pendingInvitationsCount();
        event(new InvitationCountUpdated($invitation->invitee_id, $count));
        return response()->json([
            'message' => 'Invitation accepted successfully.',
            'invitation' => $invitation
        ]);
    }

    public function decline(Request $request, Invitation $invitation)
    {
        $this->authorize('respond', $invitation);

        if ($invitation->status !== 'pending') {
            return response()->json(['message' => 'Invitation already handled.'], 422);
        }

        $invitation->update(['status' => 'declined', 'updated_at' => now()]);

        // Broadcast update
        event(new InvitationUpdated($invitation));

        return response()->json([
            'message' => 'Invitation declined.',
            'invitation' => $invitation
        ]);
    }

    public function revoke(Request $req, Document $document, User $user)
    {
        $this->authorize('invite', $document);

        // Remove from collaborators
        $document->collaborators()->detach($user->id);

        // Clean up pending invites
        Invitation::where('document_id', $document->id)
            ->where('invitee_id', $user->id)
            ->delete();

        $count = $user->pendingInvitationsCount();
        event(new InvitationCountUpdated($user->id, $count));
        return response()->json(['message' => 'Access revoked successfully.']);
    }
}
