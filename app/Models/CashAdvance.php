<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashAdvance extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'reason',
        'status',
        'approved_by',
        'is_deducted',
        'deducted_at',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
