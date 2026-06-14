<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Anggota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anggota';

    protected $fillable = [
        'karyawan_id',
        'jabatan_id',
        'status',
        'disetujui_pada',
        'tanda_tangan',
        'qr_token',
    ];

    protected static function booted()
    {
        static::creating(function ($anggota) {
            if (empty($anggota->qr_token)) {
                $anggota->qr_token = Str::random(12);
            }
        });
    }

    protected $casts = [
        'disetujui_pada' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function riwayatAnggota()
    {
        return $this->hasMany(RiwayatAnggota::class, 'anggota_id')->orderBy('created_at', 'asc');
    }

    // Accessors for backward compatibility with English views
    public function getEmployeeAttribute() { return $this->karyawan; }
    public function getRoleAttribute() { return $this->jabatan; }
    public function getMemberRoleIdAttribute() { return $this->jabatan_id; }
    public function getSignImageAttribute() { return $this->tanda_tangan; }

    public function getVerifyTokenAttribute()
    {
        if (!$this->qr_token) {
            $this->qr_token = Str::random(12);
            $this->saveQuietly();
        }
        return $this->qr_token;
    }
}
