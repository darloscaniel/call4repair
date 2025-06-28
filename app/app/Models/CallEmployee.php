<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CallEmployee extends Pivot
{
    protected $table = 'call_employee';

    protected $fillable = [
        'call_id',
        'employee_id',
        'assigned_at',
        'status',
    ];

    protected $dates = ['assigned_at'];
}
