<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'amount',
        'original_amount',
        'payment_method',
        'reference_number',
        'payment_date',
        'notes',
        'status',
        'reversal_of',
        'added_by',
        'submitted_by',
        'approved_by',
        'corrected_by',
        'corrected_at',
        'correction_reason',
        'cancel_reason',
        'proof_path',
        'receipt_path',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }


    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function correctedBy()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
