<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'dosen_id',
        'sks',
        'semester',
        'deskripsi',
        'kategori',
        'status',
        'icon',
        'warna',
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
    public function getRibbonColorAttribute()
    {
        return match($this->status) {
            'aktif' => 'green',
            'non-aktif' => 'orange',
            default => 'blue'
        };
    }

    public function getRibbonTextAttribute()
    {
        return match($this->status) {
            'aktif' => 'Aktif',
            'non-aktif' => 'Non-Aktif',
            default => 'Status'
        };
    }

    // Count total materi
    public function getTotalMateriAttribute()
    {
        return $this->materis()->count();
    }

    // Count total tugas
    public function getTotalTugasAttribute()
    {
        return $this->tugas()->count();
    }

    // Scope
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByKategori($query, $kategori)
    {
        if ($kategori && $kategori !== 'all') {
            return $query->where('kategori', $kategori);
        }
        return $query;
    }

    public function scopeByDosen($query, $dosenId)
    {
        if ($dosenId) {
            return $query->where('dosen_id', $dosenId);
        }
        return $query;
    }
}