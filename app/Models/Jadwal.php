<?php

// app/Models/Jadwal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_matkul',
        'dosen',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'kode_matkul',
        'sks',
        'warna'
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    // Mendapatkan jadwal berdasarkan hari
    public static function getByDay($hari)
    {
        return self::where('hari', $hari)
                   ->orderBy('jam_mulai')
                   ->get();
    }

    // Mendapatkan semua jadwal minggu ini
    public static function getWeekSchedule()
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $schedule = [];

        foreach ($days as $day) {
            $schedule[$day] = self::where('hari', $day)
                                  ->orderBy('jam_mulai')
                                  ->get();
        }

        return $schedule;
    }

    // Hitung grid row untuk tampilan jadwal
    public function getGridRowAttribute()
    {
        $startHour = (int) Carbon::parse($this->jam_mulai)->format('H');
        $endHour = (int) Carbon::parse($this->jam_selesai)->format('H');
        
        // Mulai dari jam 8 (index 1)
        $startRow = ($startHour - 7);
        $endRow = ($endHour - 7);
        
        return "$startRow / $endRow";
    }

    // Format waktu untuk tampilan
    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->jam_mulai)->format('H:i') . ' - ' . 
               Carbon::parse($this->jam_selesai)->format('H:i');
    }
}