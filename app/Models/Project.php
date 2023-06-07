<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'document_id', 'due_date', 'summary', 'status', 'reminder_id', 'team_id'];

    public function team() {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }

    public function document() {
        return $this->hasOne(Document::class, 'id', 'document_id');
    }
}
