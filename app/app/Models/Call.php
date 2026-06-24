<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Restrict a query to the calls a given user is allowed to see.
     * Users with the "view all calls" permission see everything; everyone
     * else only sees calls assigned to their linked employee record.
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if ($user && $user->can('view all calls')) {
            return $query;
        }

        $employeeId = optional($user?->employee)->id;

        return $query->whereHas('employees', function (Builder $q) use ($employeeId) {
            $q->where('employees.id', $employeeId);
        });
    }

    /**
     * Whether the given user is allowed to see this specific call.
     */
    public function isVisibleTo(?User $user): bool
    {
        if ($user && $user->can('view all calls')) {
            return true;
        }

        $employeeId = optional($user?->employee)->id;

        return $employeeId
            && $this->employees()->where('employees.id', $employeeId)->exists();
    }
}
