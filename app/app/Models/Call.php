<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Call extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'customer_name',
        'phone',
        'description',
        'status',
        'created_by',
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

    /**
     * Registered user who opened the call (null when opened anonymously
     * through the public form).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
