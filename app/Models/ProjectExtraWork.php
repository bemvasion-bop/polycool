<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExtraWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'description',
        'volume_bdft',
        'rate_per_bdft',
        'amount',
        'added_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
