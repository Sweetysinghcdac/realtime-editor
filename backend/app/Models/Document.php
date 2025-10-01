<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;
    protected $fillable = ['owner_id','title','content'];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function collaborators() {
        return $this->belongsToMany(User::class, 'document_user')
            ->withPivot('role','invited_by','accepted_at')
            ->withTimestamps();
    }

    public function versions() {
        return $this->hasMany(DocumentVersion::class);
    }
}
