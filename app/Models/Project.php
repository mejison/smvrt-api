<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Project extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name', 'document_id', 'due_date',
        'summary', 'status', 'reminder_id', 'team_id',
        'members',
        'signatory',
        'external_collaborators',
        'reminder',
        'user_id',
    ];

    protected $casts = [
        'members' => 'object',
        'signatory' => 'object',
        'external_collaborators' => 'object',
    ];

    public function team() {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }

    public function document() {
        return $this->hasOne(Document::class, 'id', 'document_id');
    }
    
}
