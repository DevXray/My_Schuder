<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'dosen_id',
        'materi_id',
        'judul',
        'deskripsi',
        'tanggal_diberikan',
        'deadline',
        'bobot',
        'status',
        'priority',
        'nilai',
        'grade',
        'feedback',
        'file_soal',
        'file_jawaban',
        'waktu_pengumpulan',
        'tepat_waktu'
    ];

    protected $casts = [
        'tanggal_diberikan' => 'date',
        'deadline' => 'date',
        'waktu_pengumpulan' => 'datetime',
        'tepat_waktu' => 'boolean'
    ];

    // Relasi
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    // Accessor - Hitung sisa hari deadline
    public function getSisaHariAttribute()
    {
        $now = Carbon::now();
        $deadline = Carbon::parse($this->deadline);
        
        if ($deadline->isPast()) {
            return 'Terlambat';
        }
        
        $diff = $now->diffInDays($deadline);
        
        if ($diff == 0) {
            return 'Hari ini';
        } elseif ($diff == 1) {
            return '1 hari lagi';
        } else {
            return $diff . ' hari lagi';
        }
    }

    // Accessor - Status badge text
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Belum Dikumpulkan',
            'submitted' => 'Sudah Dikumpulkan',
            'graded' => 'Sudah Dinilai',
            default => 'Belum Dikumpulkan'
        };
    }

    // Accessor - Status badge icon
    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'pending' => 'fa-clock',
            'submitted' => 'fa-check',
            'graded' => 'fa-star',
            default => 'fa-clock'
        };
    }

    // Accessor - Grade category (excellent, good, average, poor)
    public function getGradeCategoryAttribute()
    {
        if (!$this->nilai) return null;
        
        if ($this->nilai >= 90) return 'excellent';
        if ($this->nilai >= 80) return 'good';
        if ($this->nilai >= 70) return 'average';
        return 'poor';
    }

    // Accessor - Deadline dekat (3 hari atau kurang)
    public function getIsDeadlineDekatAttribute()
    {
        if ($this->status !== 'pending') return false;
        
        $now = Carbon::now();
        $deadline = Carbon::parse($this->deadline);
        
        return $deadline->isFuture() && $now->diffInDays($deadline) <= 3;
    }

    // Scope - Filter by status
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    // Scope - Filter by priority
    public function scopeByPriority($query, $priority)
    {
        if ($priority && $priority !== 'all') {
            return $query->where('priority', $priority);
        }
        return $query;
    }

    // Scope - Deadline dekat
    public function scopeDeadlineDekat($query)
    {
        $threeDaysLater = Carbon::now()->addDays(3);
        return $query->where('status', 'pending')
                    ->where('deadline', '<=', $threeDaysLater)
                    ->where('deadline', '>=', Carbon::now());
    }

    // Static method - Get statistics
    public static function getStats()
    {
        $deadlineDekat = self::deadlineDekat()->count();
        $belumDikumpulkan = self::where('status', 'pending')->count();
        $sudahDikumpulkan = self::where('status', 'submitted')->count();
        $rataRata = self::where('status', 'graded')->avg('nilai');

        return [
            'deadline_dekat' => $deadlineDekat,
            'belum_dikumpulkan' => $belumDikumpulkan,
            'sudah_dikumpulkan' => $sudahDikumpulkan,
            'rata_rata' => $rataRata ? round($rataRata) : 0,
        ];
    }

    // Static method - Get count by status
    public static function getCountByStatus()
    {
        return [
            'all' => self::count(),
            'pending' => self::where('status', 'pending')->count(),
            'submitted' => self::where('status', 'submitted')->count(),
            'graded' => self::where('status', 'graded')->count(),
        ];
    }
}