<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    public const STATUS_PENDING           = 'pending';
    public const STATUS_APPROVED          = 'approved';
    public const STATUS_REJECTED          = 'rejected';
    public const STATUS_REVERSED          = 'reversed';
    public const STATUS_REISSUE_REQUESTED = 'reissue_requested';
    public const STATUS_REISSUED          = 'reissued';

    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'expense_type',
        'material_id',
        'supplier_id',
        'unit_cost',
        'quantity_used',
        'total_cost',
        'amount',
        'category',
        'expense_date',
        'description',
        'status',
        'receipt_path',
    ];



    /* ================================
       RELATIONSHIPS
    ================================= */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function reversedExpense()
    {
        return $this->belongsTo(Expense::class, 'reversal_of');
    }

    public function correctedBy()
    {
    return $this->belongsTo(User::class, 'corrected_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function history()
    {
        return $this->hasMany(ExpenseHistory::class);
    }
}
