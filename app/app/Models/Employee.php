<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser atribuídos em massa.
     * Isso protege seu modelo contra Mass Assignment.
     */
    protected $fillable = [
        'name',
        'age',
        'phone',
        'email',
    ];

    /**
     * Relacionamento N:N com chamados (calls)
     * Um funcionário pode estar em vários chamados.
     */
    public function calls()
    {
        return $this->belongsToMany(Call::class, 'call_employee')
                    ->withPivot('assigned_at', 'status')
                    ->withTimestamps();
    }
}

