<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'email',
        'password',
        'jurusan',
        'kelas',
    ];

    protected $hidden = [
        'password',
    ];

    // ✅ Relationship ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}