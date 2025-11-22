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

    public function getProgressTextAttribute()
    {
        if ($this->progress == 0) {
            return 'Belum Dimulai';
        } elseif ($this->progress == 100) {
            return '100% Selesai';
        } else {
            return $this->progress . '% Selesai';
        }
    }

    // Accessor untuk button text
    public function getButtonTextAttribute()
    {
        return match($this->status) {
            'new' => 'Mulai',
            'progress' => 'Lanjutkan',
            'completed' => 'Ulangi',
            default => 'Mulai'
        };
    }

    // Accessor untuk button icon
    public function getButtonIconAttribute()
    {
        return match($this->status) {
            'completed' => 'fa-redo',
            default => 'fa-play'
        };
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

    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            if ($status === 'recent') {
                return $query->latest();
            }
            return $query->where('status', $status);
        }
        return $query;
    }

    // Static method untuk hitung statistik
    public static function getStats()
    {
        return [
            'total' => self::count(),
            'progress' => self::where('status', 'progress')->count(),
            'completed' => self::where('status', 'completed')->count(),
        ];
    }
}