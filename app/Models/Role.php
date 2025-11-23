<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name', // atau 'label', sesuaikan dengan nama kolom di database Anda
        'description',
    ];

    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id'); // pastikan foreign key nya 'role_id'
    }

    /**
     * Get role by name
     */
    public static function getByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    /**
     * Get role ID by name
     */
    public static function getIdByName(string $name): ?int
    {
        return self::where('name', $name)->value('id');
    }

    /**
     * Get all role names as array
     */
    public static function getAllNames(): array
    {
        return self::pluck('name')->toArray();
    }
}