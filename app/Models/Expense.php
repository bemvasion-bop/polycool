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

        // MATERIAL EXPENSE FIELDS
        'material_id',
        'supplier_id',
        'unit_cost',
        'quantity_used',
        'total_cost',

        // CUSTOM CATEGORY EXPENSE FIELDS
        'category',
        'amount',
        'description',

        'expense_date',
        'receipt_path',
        'status',

        'reversal_of',
        'corrected_by',
        'correction_reason',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // An expense belongs to one project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // An expense is submitted by one user (employee/manager)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function correctedBy()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
