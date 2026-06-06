<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanAnggota extends Model
{
    protected $table = 'jabatan_anggota';

    protected $fillable = ['uuid', 'nama', 'tunggal', 'penandatangan'];

    // Accessors for backward compatibility with English views
    public function getNameAttribute() { return $this->nama; }
}
