<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Call extends Model
{
    use HasFactory;


    protected $fillable = [
        'customer_name',
        'phone',
        'description',
        'status',
    ];

    /**
     * Many-to-many relationship with employees.
     * A call can have many assigned employees.
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'call_employee')
                    ->withPivot('assigned_at', 'status')
                    ->withTimestamps();
    }
}
