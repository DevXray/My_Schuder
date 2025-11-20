<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Materi;
use App\Models\Jadwal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== STATISTIK CARDS =====
        
        // Total Materi
        $totalMateri = Materi::count();
        
        // Tugas Aktif (belum dikumpulkan)
        $tugasAktif = Tugas::where('status', 'pending')->count();
        
        // Peserta Kelas (hardcoded dulu, nanti bisa diganti jika ada model Mahasiswa)
        $pesertaKelas = 32; // Atau bisa dari User::where('role', 'mahasiswa')->count();
        
        // Jadwal Hari Ini
        $hariIni = Carbon::now()->locale('id')->dayName;
        $jadwalHariIni = Jadwal::where('hari', ucfirst($hariIni))->count();
        
        
        // ===== INFORMASI TERBARU =====
        
        // Ambil 4 update terbaru dari berbagai sumber
        $informasiTerbaru = collect();
        
        // Tugas terbaru (2 hari terakhir)
        $tugasBaru = Tugas::with('materi')
            ->where('tanggal_diberikan', '>=', Carbon::now()->subDays(2))
            ->latest('tanggal_diberikan')
            ->take(2)
            ->get()
            ->map(function($tugas) {
                return [
                    'type' => 'assignment',
                    'icon' => 'fa-clipboard-check',
                    'title' => 'Tugas: ' . $tugas->judul,
                    'description' => 'Deadline: ' . Carbon::parse($tugas->deadline)->format('d M Y'),
                    'time' => Carbon::parse($tugas->tanggal_diberikan)->diffForHumans(),
                    'created_at' => $tugas->tanggal_diberikan
                ];
            });
        
        // Materi terbaru (3 hari terakhir)
        $materiBaru = Materi::with('dosen')
            ->where('created_at', '>=', Carbon::now()->subDays(3))
            ->latest()
            ->take(2)
            ->get()
            ->map(function($materi) {
                return [
                    'type' => 'material',
                    'icon' => 'fa-book-open',
                    'title' => 'Materi Baru: ' . $materi->judul,
                    'description' => 'Materi pembelajaran baru telah ditambahkan ke kelas.',
                    'time' => $materi->created_at->diffForHumans(),
                    'created_at' => $materi->created_at
                ];
            });
        
        // Gabungkan semua informasi dan urutkan berdasarkan waktu
        $informasiTerbaru = $tugasBaru->merge($materiBaru)
            ->sortByDesc('created_at')
            ->take(4)
            ->values();
        
        
        // ===== JADWAL HARI INI =====
        
        $jadwalList = Jadwal::where('hari', ucfirst($hariIni))
            ->orderBy('jam_mulai')
            ->take(3)
            ->get();
        
        
        // ===== PROGRESS PEMBELAJARAN =====
        
        // Ambil semua materi dengan progress
        $progressMateri = Materi::with('dosen')
            ->select('judul', 'jumlah_modul', 'progress')
            ->orderByDesc('progress')
            ->take(4)
            ->get()
            ->map(function($materi) {
                return [
                    'title' => $materi->judul,
                    'completed' => round($materi->jumlah_modul * ($materi->progress / 100)),
                    'total' => $materi->jumlah_modul,
                    'percentage' => $materi->progress
                ];
            });
        
        
        // ===== STATISTIK TAMBAHAN =====
        
        // Deadline dekat (3 hari ke depan)
        $deadlineDekat = Tugas::deadlineDekat()->count();
        
        // Tugas sudah dikumpulkan
        $tugasDikumpulkan = Tugas::where('status', 'submitted')->count();
        
        // Rata-rata nilai
        $rataRataNilai = Tugas::where('status', 'graded')->avg('nilai');
        
        // Materi completed
        $materiSelesai = Materi::where('status', 'completed')->count();
        
        
        // Return data ke view
        return view('dashboard', compact(
            // Stats Cards
            'totalMateri',
            'tugasAktif',
            'pesertaKelas',
            'jadwalHariIni',
            
            // Informasi Terbaru
            'informasiTerbaru',
            
            // Jadwal
            'jadwalList',
            'hariIni',
            
            // Progress
            'progressMateri',
            
            // Stats Tambahan
            'deadlineDekat',
            'tugasDikumpulkan',
            'rataRataNilai',
            'materiSelesai'
        ));
    }
}