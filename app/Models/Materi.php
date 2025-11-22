<?php

// app/Models/Materi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'matkul_id',  // NEW
        'dosen_id',
        'judul',
        'deskripsi',
        'tipe_materi',  // NEW
        'kategori',
        'jumlah_modul',
        'durasi_jam',
        'progress',
        'status',
        'icon',
        'warna',
        'file'
    ];

    // Relationships
    public function matkul()  // NEW
    {
        return $this->belongsTo(Matkul::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    // Existing methods tetap sama...
    public function getRibbonTextAttribute()
    {
        return match($this->status) {
            'new' => 'Baru',
            'progress' => 'Sedang Dipelajari',
            'completed' => 'Selesai',
            default => 'Baru'
        };
    }

    public function getRibbonColorAttribute()
    {
        return match($this->status) {
            'new' => 'blue',
            'progress' => 'orange',
            'completed' => 'green',
            default => 'blue'
        };
    }

    // NEW: Get tipe materi icon
    public function getTipeMateriIconAttribute()
    {
        return match($this->tipe_materi) {
            'pdf' => 'fa-file-pdf',
            'ppt' => 'fa-file-powerpoint',
            'video' => 'fa-video',
            'link' => 'fa-link',
            'doc' => 'fa-file-word',
            default => 'fa-file'
        };
    }
}
