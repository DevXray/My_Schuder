<?php

// ============================================
// app/Models/User.php (UPDATE)
// ============================================
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class);
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    // Role Checking Methods
    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isDosen(): bool
    {
        return $this->role && $this->role->name === 'dosen';
    }

    public function isMahasiswa(): bool
    {
        return $this->role && $this->role->name === 'mahasiswa';
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    // Get specific profile
    public function getProfile()
    {
        if ($this->isAdmin()) return $this->admin;
        if ($this->isDosen()) return $this->dosen;
        if ($this->isMahasiswa()) return $this->mahasiswa;
        return null;
    }
}