<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Materi;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\Pengumpulan;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // ✅ DEBUG: Log user info
        \Log::info('Dashboard Access', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_id' => $user->role_id,
            'role_name' => $user->getRoleName(),
            'is_admin' => $user->isAdmin(),
            'is_dosen' => $user->isDosen()
        ]);
        
        // ✅ Cek role dan redirect ke dashboard yang sesuai
        if ($user->isAdmin()) {
            \Log::info('Loading Admin Dashboard');
            return $this->adminDashboard();
        }
        
        if ($user->isDosen()) {
            \Log::info('Loading Dosen Dashboard');
            return $this->dosenDashboard();
        }
        
        \Log::info('Loading Student Dashboard');
        return $this->studentDashboard();
    }
    
    /**
     * Admin Dashboard - Menggantikan Administrator Page
     */
    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admin' => User::byRole('admin')->count(),
            'total_dosen' => User::byRole('dosen')->count(),
            'total_mahasiswa' => User::byRole('mahasiswa')->count(),
            'total_materi' => Materi::count(),
            'total_tugas' => Tugas::count(),
        ];

        $recentUsers = User::with('role')->latest()->take(10)->get();
        $recentMateri = Materi::with('dosen')->latest()->take(5)->get();
        $recentTugas = Tugas::latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recentUsers', 'recentMateri', 'recentTugas'));
    }
    
    /**
     * Dosen Dashboard - Dashboard khusus untuk dosen
     */
    private function dosenDashboard()
    {
        $user = auth()->user();
        $dosen = $user->dosen;
        
        // Jika user dosen belum punya profile dosen, redirect
        if (!$dosen) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile dosen belum lengkap. Silakan hubungi administrator.');
        }
        
        // Statistik untuk dosen
        $stats = [
            'total_materi' => Materi::where('dosen_id', $dosen->id)->count(),
            'total_tugas' => Tugas::where('dosen_id', $dosen->id)->count(),
            'tugas_belum_dinilai' => Pengumpulan::whereHas('tugas', function($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id);
            })->where('status', 'submitted')->count(),
            'total_mahasiswa' => Mahasiswa::count(),
        ];
        
        // Materi terbaru yang dibuat dosen ini
        $recentMateri = Materi::where('dosen_id', $dosen->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Pengumpulan yang belum dinilai
        $pengumpulanBelumDinilai = Pengumpulan::with(['mahasiswa.user', 'tugas'])
            ->whereHas('tugas', function($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id);
            })
            ->where('status', 'submitted')
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboard.dosen', compact('stats', 'recentMateri', 'pengumpulanBelumDinilai'));
    }
    
    /**
     * Student/Dosen Dashboard
     */
    private function studentDashboard()
    {
        // ===== STATISTIK CARDS =====
        $totalMateri = Materi::count();
        $tugasAktif = Tugas::where('status', 'pending')->count();
        $pesertaKelas = 32;
        $hariIni = Carbon::now()->locale('id')->dayName;
        $jadwalHariIni = Jadwal::where('hari', ucfirst($hariIni))->count();
        
        // ===== INFORMASI TERBARU =====
        $informasiTerbaru = collect();
        
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
        
        $informasiTerbaru = $tugasBaru->merge($materiBaru)
            ->sortByDesc('created_at')
            ->take(4)
            ->values();
        
        $jadwalList = Jadwal::where('hari', ucfirst($hariIni))
            ->orderBy('jam_mulai')
            ->take(3)
            ->get();
        
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
        
        $deadlineDekat = Tugas::deadlineDekat()->count();
        $tugasDikumpulkan = Tugas::where('status', 'submitted')->count();
        $rataRataNilai = Tugas::where('status', 'graded')->avg('nilai');
        $materiSelesai = Materi::where('status', 'completed')->count();
        
        return view('dashboard', compact(
            'totalMateri',
            'tugasAktif',
            'pesertaKelas',
            'jadwalHariIni',
            'informasiTerbaru',
            'jadwalList',
            'hariIni',
            'progressMateri',
            'deadlineDekat',
            'tugasDikumpulkan',
            'rataRataNilai',
            'materiSelesai'
        ));
    }
}