<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollEntry extends Model
{
    protected $fillable = [
        'payroll_run_id',
        'user_id',
        'employment_type',
        'commission_earnings',
        'fixed_salary_portion',
        'gross_pay',
        'cash_advance_deduction',
        'other_deductions',
        'net_pay',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function run()
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
