<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge',
        'name',
        'department',
        'line',
        'position',
        'join_date',
        'end_date',
        'image',
        'birth_place',
        'birth_date',
        'address',
    ];

    protected $casts = [
        'join_date' => 'date',
        'end_date' => 'date',
        'birth_date' => 'date',
    ];

    /**
     * Check if the employee is currently active.
     */
    public function getIsActiveAttribute(): bool
    {
        // An employee is active if they have no end_date or their end_date is in the future
        return !$this->end_date || $this->end_date->gt(now()->startOfDay());
    }

    /**
     * Get the readable status of the employee.
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}
