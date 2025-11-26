<?php
// app/Models/Pengumpulan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengumpulan extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'mahasiswa_id',
        'file_tugas',
        'waktu_pengumpulan',
    ];

    protected $casts = [
        'waktu_pengumpulan' => 'datetime',
    ];

    // ✅ Relasi ke Tugas
    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    // ✅ Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    // ✅ Relasi ke User (jika menggunakan user_id)
    public function user()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    // ✅ Check apakah tepat waktu
    public function isTepatWaktu()
    {
        if (!$this->waktu_pengumpulan || !$this->tugas) {
            return false;
        }
        
        return $this->waktu_pengumpulan <= $this->tugas->deadline;
    }

    // ✅ Get status pengumpulan
    public function getStatusAttribute()
    {
        if (!$this->waktu_pengumpulan) {
            return 'belum';
        }
        
        return $this->isTepatWaktu() ? 'tepat_waktu' : 'terlambat';
    }
}