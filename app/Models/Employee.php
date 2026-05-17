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
}
