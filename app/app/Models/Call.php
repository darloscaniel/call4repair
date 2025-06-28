<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Call extends Model
{
    use HasFactory;


    protected $fillable = [
        'customer_name',
        'description',
        'status',
    ];

    /**
     * Relacionamento N:N com funcionários.
     * Um chamado pode ter vários funcionários.
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'call_employee')
                    ->withPivot('assigned_at', 'status')
                    ->withTimestamps();
    }
}
