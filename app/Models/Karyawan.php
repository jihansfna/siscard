<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'badge',
        'nama',
        'departemen',
        'line',
        'jabatan',
        'tanggal_masuk',
        'tanggal_keluar',
        'foto',
        'nomor_telp',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Check if the employee is currently active.
     */
    public function getAktifAttribute(): bool
    {
        // An employee is active if they have no tanggal_keluar or their tanggal_keluar is in the future
        return !$this->tanggal_keluar || $this->tanggal_keluar->gt(now()->startOfDay());
    }

    // Accessors for backward compatibility with English views
    public function getNameAttribute() { return $this->nama; }
    public function getPositionAttribute() { return $this->jabatan; }
    public function getDepartmentAttribute() { return $this->departemen; }
    public function getBirthPlaceAttribute() { return $this->tempat_lahir; }
    public function getBirthDateAttribute() { return $this->tanggal_lahir; }
    public function getJoinDateAttribute() { return $this->tanggal_masuk; }
    public function getEndDateAttribute() { return $this->tanggal_keluar; }
    public function getAddressAttribute() { return $this->alamat; }
    public function getImageAttribute() { return $this->foto; }

    /**
     * Get the readable status of the employee.
     */
    public function getStatusAttribute(): string
    {
        return $this->aktif ? 'Aktif' : 'Non Aktif';
    }

    public function user()
    {
        return $this->hasOne(User::class, 'badge', 'badge');
    }
}
