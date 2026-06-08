<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saran extends Model
{
    protected $table = 'saran';

    protected $fillable = [
        'uuid',
        'anggota_id',
        'berkas',
        'deskripsi',
        'anonim',
        'status',
        'catatan',
    ];

    protected $casts = [
        'anonim' => 'boolean',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }
}
