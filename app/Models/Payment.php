<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $fillable = [
        'project_id',
        'payment_date',
        'amount',
        'reference',
        'remarks',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
