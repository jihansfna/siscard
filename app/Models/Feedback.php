<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'uuid',
        'member_id',
        'file',
        'description',
        'status',
        'remark',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
