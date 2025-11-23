<?php
// app/Models/Dosen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dosen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nidn',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    // ✅ Add relationships
    public function mataKuliahs()
    {
        return $this->hasMany(MataKuliah::class);
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }
}