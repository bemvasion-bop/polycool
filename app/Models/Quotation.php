<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'created_by',
        'quotation_date',
        'project_name',
        'address',
        'system',
        'scope_of_work',
        'duration_days',
        'total_bdft',
        'rate_per_bdft',
        'discount',
        'contract_price',
        'down_payment',
        'balance',
        'conditions',
        'status',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'quotation_date' => 'date',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // creator
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

   public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'converted_project_id');
    }

    // Helpers
    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeEditedBy($user): bool
    {
        // If converted, only OWNER can edit
        if ($this->isConverted()) {
            return $user && $user->role === 'owner';
        }

        // Otherwise, allow owner + manager to edit
        return $user && in_array($user->role, ['owner', 'manager'], true);
    }
}
