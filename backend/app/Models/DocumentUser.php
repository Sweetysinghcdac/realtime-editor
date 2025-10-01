<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentUser extends Model
{
    protected $table = 'document_users';

    protected $fillable = [
        'document_id',
        'user_id',
        'role',
        'invited_by',
        'accepted_at',
    ];
    protected $casts = [
        'accepted_at' => 'datetime',
    ];

     public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
