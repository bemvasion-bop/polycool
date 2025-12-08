<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'action',
        'details',
        'performed_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
