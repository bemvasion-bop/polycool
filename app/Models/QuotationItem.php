<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'line_total',
        'sort_order',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
