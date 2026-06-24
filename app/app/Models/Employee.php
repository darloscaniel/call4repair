<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass-assignable attributes (guards against mass assignment).
     */
    protected $fillable = [
        'name',
        'age',
        'phone',
        'email',
    ];

    /**
     * Many-to-many relationship with calls.
     * An employee can be assigned to many calls.
     */
    public function calls()
    {
        return $this->belongsToMany(Call::class, 'call_employee')
                    ->withPivot('assigned_at', 'status')
                    ->withTimestamps();
    }
}

