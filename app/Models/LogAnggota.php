<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAnggota extends Model
{
    use HasFactory;

    protected $table = 'log_anggota';

    protected $guarded = ['id'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function pelaku()
    {
        return $this->belongsTo(User::class, 'pelaku_id');
    }
}
