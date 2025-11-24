<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'email_verified_at',
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

    // ✅ Relationship dengan Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // ✅ Get role name safely
    public function getRoleName(): string
    {
        return $this->role?->name ?? 'guest';
    }

    public function getRoleDisplayName(): string
    {
        return $this->role?->display_name ?? 'Guest';
    }

    // ✅ Role check methods
    public function isAdmin(): bool
    {
        return $this->getRoleName() === 'admin';
    }

    public function isDosen(): bool
    {
        return $this->getRoleName() === 'dosen';
    }

    public function isMahasiswa(): bool
    {
        return $this->getRoleName() === 'mahasiswa';
    }

    // ✅ ✅ ✅ TAMBAHKAN INI - Scope untuk filter by role
    public function scopeByRole($query, $roleName)
    {
        return $query->whereHas('role', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    // ✅ Relationships
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
}