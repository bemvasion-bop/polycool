<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProgressLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'log_date',
        'bdft_completed',
        'notes'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
