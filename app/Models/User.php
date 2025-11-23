<?php
// app/Models/User.php

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
        'role_id',  // ✅ Add role_id instead of role
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

    // app/Models/User.php

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id'); // pastikan foreign key benar
    }

    public function getRoleName(): string
    {
        return $this->role?->name ?? 'guest';
    }

    public function getRoleDisplayName(): string
    {
        return $this->role?->display_name ?? 'Guest';
    }

    // ✅ Role Check Methods - FIXED with proper relationship checking
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