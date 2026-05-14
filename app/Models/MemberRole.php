<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRole extends Model
{
    protected $fillable = ['uuid', 'name', 'is_single', 'is_sign'];
}
