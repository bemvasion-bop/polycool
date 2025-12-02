<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'given_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'gender',
        'date_of_birth',
        'date_hired',
        'street_address',
        'city',
        'province',
        'postal_code',
        'employee_status',

        // System Role (owner/manager/employee/accounting/audit)
        'system_role',

        // QR for attendance
        'qr_code',

        // Employment info
        'employment_type',       // field_worker | office_staff
        'monthly_salary',        // office staff
        'payroll_type',          // salary | commission
        'daily_rate',            // field worker (optional)
        'commission_rate',       // for commission logic if needed
        'allowance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'date_hired' => 'date',
        'monthly_salary' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'allowance' => 'decimal:2',
    ];

    // ---------- FULL NAME ----------
    public function getFullNameAttribute()
    {
        return trim(
            "{$this->given_name} " .
            ($this->middle_name ? "{$this->middle_name} " : "") .
            "{$this->last_name}"
        );
    }

    // ---------- RELATIONSHIPS ----------
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function assignedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_user')
                    ->withPivot(['role_in_project', 'assigned_at'])
                    ->withTimestamps();
    }

    // ---------- PAYROLL RELATIONSHIPS ----------
    public function payrollEntries()
    {
        return $this->hasMany(PayrollEntry::class);
    }

    // ---------- PAYROLL HELPERS ----------
    public function isFieldWorker()
    {
        return $this->employment_type === 'field_worker';
    }

    public function isOfficeStaff()
    {
        return $this->employment_type === 'office_staff';
    }

    public function isSalaryBased()
    {
        return $this->payroll_type === 'salary';
    }

    public function isCommissionBased()
    {
        return $this->payroll_type === 'commission';
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')
                    ->withPivot(['role_in_project', 'assigned_at'])
                    ->withTimestamps();
    }
}
