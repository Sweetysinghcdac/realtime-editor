<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    // public function view(User $user, Document $document): bool {
    //     if ($document->owner_id === $user->id) return true;
    //     $row = $document->collaborators()->where('user_id', $user->id)->first();
    //     if ($row && $row->pivot->accepted_at) return true;

    //     return false;
    //     // return $document->collaborators()->where('user_id',$user->id)->whereNotNull('accepted_at')->exists();
    // }
    public function view(User $user, Document $document)
    {
        // Owner can always view
        if ($user->id === $document->owner_id) {
            return true;
        }

        // Collaborators with any role can view
        return $document->collaborators()
            ->where('user_id', $user->id)
            ->exists();
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    // public function update(User $user, Document $document): bool {
    //     if ($document->owner_id === $user->id) return true;

    //     $collab = $document->collaborators()->where('user_id', $user->id)->first();
    //     if ($collab && $collab->pivot->accepted_at && $collab->pivot->role === 'editor') return true;

    //     return false;
    // }

    public function update(User $user, Document $document)
    {
        // Owner always can edit
        if ($user->id === $document->owner_id) {
            return true;
        }

        // Collaborators with editor role can edit
        return $document->collaborators()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'editor')
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool {
        return $document->owner_id === $user->id;
    }
    public function invite(User $user, Document $document): bool {
        return $document->owner_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }
}
