<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'badge',
        'password',
        'peran',
        'jumlah_login',
        'pertanyaan_rahasia',
        'jawaban_rahasia',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'jawaban_rahasia',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the activity logs performed by the user.
     */
    public function riwayatAnggota()
    {
        return $this->hasMany(RiwayatAnggota::class, 'pelaku_id');
    }

    // Accessors for backward compatibility with English views
    public function getNameAttribute() { return $this->nama; }
    public function getRoleAttribute() { return $this->peran; }
    public function getLoginCountAttribute() { return $this->jumlah_login; }
}
