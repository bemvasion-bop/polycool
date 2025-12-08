<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'changed_by',
        'old_quantity',
        'new_quantity',
        'old_amount',
        'new_amount',
        'change_reason',
        'type',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
