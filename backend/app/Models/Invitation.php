<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class Invitation extends Model
{
   use HasFactory;

    protected $fillable = [
        'document_id',
        'inviter_id',
        'invitee_id',
        'role',
        'status',
    ];

    // 🔹 Invitation belongs to a Document
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // 🔹 Who sent the invite
    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    // 🔹 Who received the invite
    public function invitee()
    {
        return $this->belongsTo(User::class, 'invitee_id');
    }
}
