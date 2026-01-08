<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollEmployee extends Model
{
    //

    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class);
    }
}
