<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'material_id',
        'category',
        'amount',
        'total_cost',
        'status',
        'processed_by',
        'processed_reason',
        'is_reversal',
        'original_amount',
        'corrected_by',
        'correction_reason',
        'expense_date',
        'description'
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
