<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    protected $fillable = [
        'payroll_type',
        'period_start',
        'period_end',
        'status',
        'total_gross',
        'total_deductions',
        'total_net',
        'generated_by',
        'finalized_by',
    ];


    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
    ];

    public function entries()
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function finalizer()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }


    public function employees()
    {
        return $this->hasMany(PayrollEmployee::class);
    }
}
