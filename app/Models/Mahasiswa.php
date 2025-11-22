<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = [
        'user_id',  // IMPORTANT: Add this if not exists
        'nama',
        'nim',
        'email',
        'password',
        'jurusan',
        'kelas',
    ];

    protected $hidden = ['password'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengumpulans()
    {
        return $this->hasMany(Pengumpulan::class);
    }
}
