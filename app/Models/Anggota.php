<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anggota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anggota';

    protected $fillable = [
        'karyawan_id',
        'jabatan_anggota_id',
        'status',
        'disetujui_pada',
        'tanda_tangan',
    ];

    protected $casts = [
        'disetujui_pada' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(JabatanAnggota::class, 'jabatan_anggota_id');
    }

    public function riwayatAnggota()
    {
        return $this->hasMany(RiwayatAnggota::class, 'anggota_id')->orderBy('created_at', 'asc');
    }

    // Accessors for backward compatibility with English views
    public function getEmployeeAttribute() { return $this->karyawan; }
    public function getRoleAttribute() { return $this->jabatan; }
    public function getMemberRoleIdAttribute() { return $this->jabatan_anggota_id; }
    public function getSignImageAttribute() { return $this->tanda_tangan; }
}
