<?php

// app/Models/Materi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'dosen_id',
        'judul',
        'deskripsi',
        'kategori',
        'jumlah_modul',
        'durasi_jam',
        'progress',
        'status',
        'icon',
        'warna',
        'file'
    ];

    // Relasi ke Dosen
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    // Accessor untuk ribbon text
    public function getRibbonTextAttribute()
    {
        return match($this->status) {
            'new' => 'Baru',
            'progress' => 'Sedang Dipelajari',
            'completed' => 'Selesai',
            default => 'Baru'
        };
    }

    // Accessor untuk ribbon color
    public function getRibbonColorAttribute()
    {
        return match($this->status) {
            'new' => 'blue',
            'progress' => 'orange',
            'completed' => 'green',
            default => 'blue'
        };
    }

    // Accessor untuk progress text
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

    // Scope untuk filter kategori
    public function scopeByKategori($query, $kategori)
    {
        if ($kategori && $kategori !== 'all') {
            return $query->where('kategori', $kategori);
        }
        return $query;
    }

    // Scope untuk filter status
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