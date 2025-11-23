<?php
// app/Models/MataKuliah.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'dosen_id',
        'sks',
        'semester',
        'deskripsi',
        'kategori',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    // Accessor
    public function getFullNameAttribute()
    {
        return "{$this->kode_mk} - {$this->nama_mk}";
    }
}