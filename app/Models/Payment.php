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
        'payment_method',
        'payment_date',
        'notes',
        'proof_path',
        'status',
        'submitted_by',
        'approved_by',
        'added_by',
        'reversal_of',
        'corrected_by',
        'correction_reason',
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function originalPayment()
    {
        return $this->belongsTo(Payment::class, 'issued_from_payment_id');
    }

    public function correctedBy()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
