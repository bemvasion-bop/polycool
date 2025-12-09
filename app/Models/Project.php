<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    /* =========================================================================
     |  STATIC COST ALLOCATION (still available if needed)
     ========================================================================= */
    public const COMPANY_ALLOCATION = [
        'materials'     => 0.40,
        'labor'         => 0.25,
        'overhead'      => 0.10,
        'admin'         => 0.10,
        'companyProfit' => 0.10,
        'ownerProfit'   => 0.05,
    ];

    public const ROLE_SHARES = [
        'Spray Operator' => 0.40,
        'Technician'     => 0.30,
        'Helper 1'       => 0.15,
        'Helper 2'       => 0.15,
    ];

    /* =========================================================================
     |  RELATIONSHIPS
     ========================================================================= */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot(['role_in_project', 'assigned_at'])
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function extraWorks()
    {
        return $this->hasMany(ProjectExtraWork::class);
    }

    /* =========================================================================
     |  ACCESSORS & FINANCIAL HELPERS
     ========================================================================= */

    /** Sum of APPROVED payments only */
    public function getApprovedPaymentsTotalAttribute()
    {
        return $this->payments()
            ->where('status', 'approved')
            ->sum('amount');
    }

    /** Total extra work amount */
    public function getExtraWorkTotalAttribute()
    {
        return $this->extraWorks()->sum('amount');
    }

    /** Base contract price */
    public function getBaseContractPriceAttribute()
    {
        return $this->contract_price ?? 0;
    }

    /** FINAL amount = base + extra works */
    public function getFinalProjectPriceAttribute()
    {
        return ($this->base_contract_price ?? 0) + ($this->extra_work_total ?? 0);
    }

    /** Remaining balance based on approved payments only */
    public function getRemainingBalanceAttribute()
    {
        $final = $this->final_project_price ?? 0;
        return max(0, $final - $this->approved_payments_total);
    }

    /* =========================================================================
     |  PROJECT PROGRESS SYSTEM
     |  Weight-based calculation
     ========================================================================= */

    public function getProgressAttribute()
    {
        $budget = $this->final_project_price ?: 1;

        // Payment Progress (40%)
        $paymentScore = min(100, ($this->approved_payments_total / $budget) * 100);

        // Timeline (30%)
        $timeline = $this->time_progress_percentage ?? 0;
        $timelineScore = min(100, $timeline);

        // Expense Progress (30%)
        $spent = $this->expenses->sum('amount');
        $expenseScore = min(100, ($spent / $budget) * 100);

        return round(
            ($paymentScore * 0.40) +
            ($timelineScore * 0.30) +
            ($expenseScore * 0.30),
            1
        );
    }

    /**
     * Legacy calculateProgress() — kept for compatibility.
     * The new version above is cleaner, but this one remains working.
     */
    public function calculateProgress()
    {
        $budget = $this->final_project_price ?: 1;
        $progress = 0;

        // (40%) Work completion
        $workProgress = ($this->work_completion ?? 0) / 100;
        $progress += $workProgress * 40;

        // (30%) Payments
        $paymentProgress = ($this->approved_payments_total ?? 0) / $budget;
        $progress += $paymentProgress * 30;

        // (15%) Expense
        $spent = $this->expenses->sum('amount');
        $expenseProgress = max(0, ($budget - $spent) / $budget);
        $progress += $expenseProgress * 15;

        // (10%) Attendance
        $expectedDays = $this->attendance_expected_days ?? 1;
        $presentDays = $this->attendance_present_days ?? 0;
        $attendance = min($presentDays / $expectedDays, 1);
        $progress += $attendance * 10;

        // (5%) Weather Risk
        $risk = $this->weatherData['risk'] ?? null;
        $riskScore = match ($risk) {
            'low'      => 5,
            'moderate' => 2,
            'high'     => 0,
            default    => 3
        };
        $progress += $riskScore;

        return min(100, round($progress));
    }

    /* =========================================================================
     |  WARNING SYSTEM
     ========================================================================= */


     public function getWarningsAttribute()
    {
        $warnings = [];

        // Payment too low (<50%)
        if ($this->final_project_price > 0 &&
            $this->approved_payments_total < ($this->final_project_price * 0.5)) {
            $percent = round(($this->approved_payments_total / $this->final_project_price) * 100);
            $warnings[] = "Low client payment ($percent% received)";
        }

        // Over budget
        if ($this->expenses->sum('amount') > ($this->final_project_price * 0.80)) {
            $warnings[] = "Expenses exceeded 80% of total budget";
        }

        // Deadline vs progress
        if ($this->deadline &&
            now()->diffInDays($this->deadline, false) <= 3 &&
            $this->progress < 70) {
            $warnings[] = "Deadline is near but project progress is low";
        }

        if ($this->status === 'delayed') {
            $warnings[] = "This project is marked as delayed";
        }

        return $warnings;
    }

    /** More detailed budget-warning label */
    public function getExpenseWarningAttribute()
    {
        $budget = $this->final_project_price ?: 1;
        $spent  = $this->expenses->sum('amount');
        $percent = ($spent / $budget) * 100;

        if ($percent >= 90) {
            return ['type' => 'danger',  'text' => 'Expenses exceeded 90% of total budget'];
        }

        if ($percent >= 80) {
            return ['type' => 'warning', 'text' => 'Expenses exceeded 80% of total budget'];
        }

        return ['type' => 'ok', 'text' => 'No issues'];
    }


    public function progressLogs()
    {
        return $this->hasMany(ProjectProgressLog::class);
    }


    public function getBdftProgressAttribute()
    {
        $total = $this->quotation->total_bdft ?? 0;
        if ($total == 0) return 0;

        $logged = $this->progressLogs()->sum('bdft_completed');
        $percentage = ($logged / $total) * 100;

        return round(min($percentage, 100), 2);
    }


    public function approvedExtraWorks()
    {
        return $this->extraWorks()->where('status', 'approved');
    }


    public function approvedPayments()
    {
        return $this->payments()->where('status', 'approved');
    }


    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'project_id');
    }

    public static function employeeHasActiveProject($employeeId)
    {
        return self::whereHas('users', function($q) use ($employeeId) {
            $q->where('user_id', $employeeId);
        })
        ->where('status', '!=', 'completed')
        ->exists();
    }




}
