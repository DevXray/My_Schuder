<?php
// app/Http/Controllers/SPAController.php
// OPTIONAL: Untuk optimize SPA dengan JSON API

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\Jadwal;

class SPAController extends Controller
{
    /**
     * Return page data as JSON for SPA
     * OPTIONAL: Bisa digunakan untuk optimize response size
     */
    public function getPageData(Request $request, $page)
    {
        // Check if it's an AJAX request
        if (!$request->ajax() && !$request->wantsJson()) {
            return abort(400, 'This endpoint only accepts AJAX requests');
        }

        switch($page) {
            case 'dashboard':
                return $this->getDashboardData();
                
            case 'materi':
                return $this->getMateriData($request);
                
            case 'tugas':
                return $this->getTugasData($request);
                
            case 'jadwal':
                return $this->getJadwalData($request);
                
            default:
                return response()->json(['error' => 'Page not found'], 404);
        }
    }

    private function getDashboardData()
    {
        // Return dashboard stats dan data
        return response()->json([
            'html' => view('partials.dashboard-content')->render(),
            'stats' => [
                'totalMateri' => Materi::count(),
                'tugasAktif' => Tugas::where('status', 'pending')->count(),
                // ... other stats
            ]
        ]);
    }

    private function getMateriData(Request $request)
    {
        $materis = Materi::with('dosen')
            ->when($request->kategori, function($q) use ($request) {
                return $q->where('kategori', $request->kategori);
            })
            ->get();

        return response()->json([
            'html' => view('partials.materi-content', compact('materis'))->render(),
            'stats' => Materi::getStats()
        ]);
    }

    private function getTugasData(Request $request)
    {
        $tugas = Tugas::with(['dosen', 'materi'])
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->get();

        return response()->json([
            'html' => view('partials.tugas-content', compact('tugas'))->render(),
            'stats' => Tugas::getStats()
        ]);
    }

    private function getJadwalData(Request $request)
    {
        $jadwalList = Jadwal::orderBy('jam_mulai')->get();

        return response()->json([
            'html' => view('partials.jadwal-content', compact('jadwalList'))->render(),
        ]);
    }
}