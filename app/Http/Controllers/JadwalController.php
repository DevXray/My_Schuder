<?php

// app/Http/Controllers/JadwalController.php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index()
    {
        // Ambil semua jadwal dikelompokkan per hari
        $weekSchedule = Jadwal::getWeekSchedule();
        
        // Hitung statistik
        $today = Carbon::now()->locale('id')->dayName;
        $todayClasses = Jadwal::where('hari', ucfirst($today))->count();
        $totalWeekHours = Jadwal::all()->sum(function($jadwal) {
            $start = Carbon::parse($jadwal->jam_mulai);
            $end = Carbon::parse($jadwal->jam_selesai);
            return $start->diffInHours($end);
        });
        $totalSubjects = Jadwal::distinct('nama_matkul')->count();

        // Jadwal hari ini untuk upcoming section
        $todaySchedule = Jadwal::where('hari', ucfirst($today))
                               ->orderBy('jam_mulai')
                               ->get();

        // Jadwal besok
        $tomorrow = Carbon::tomorrow()->locale('id')->dayName;
        $tomorrowSchedule = Jadwal::where('hari', ucfirst($tomorrow))
                                   ->orderBy('jam_mulai')
                                   ->first();

        // Cari kelas yang sedang berlangsung
        $currentTime = Carbon::now()->format('H:i:s');
        $currentClass = Jadwal::where('hari', ucfirst($today))
                              ->where('jam_mulai', '<=', $currentTime)
                              ->where('jam_selesai', '>=', $currentTime)
                              ->first();

        // Kelas selanjutnya hari ini
        $nextClass = Jadwal::where('hari', ucfirst($today))
                           ->where('jam_mulai', '>', $currentTime)
                           ->orderBy('jam_mulai')
                           ->first();

        return view('jadwal.index', compact(
            'weekSchedule',
            'todayClasses',
            'totalWeekHours',
            'totalSubjects',
            'todaySchedule',
            'tomorrowSchedule',
            'currentClass',
            'nextClass'
        ));
    }

    public function create()
    {
        return view('jadwal.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_matkul' => 'required|string|max:255',
            'dosen' => 'required|string|max:255',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'required|string|max:100',
            'kode_matkul' => 'nullable|string|max:50',
            'sks' => 'nullable|integer|min:1|max:6',
            'warna' => 'nullable|in:blue,orange,green'
        ]);

        // Set default warna jika tidak ada
        if (!isset($validated['warna'])) {
            $validated['warna'] = 'blue';
        }

        Jadwal::create($validated);

        return redirect()->route('jadwal.index')
                       ->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        return view('jadwal.edit', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_matkul' => 'required|string|max:255',
            'dosen' => 'required|string|max:255',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'required|string|max:100',
            'kode_matkul' => 'nullable|string|max:50',
            'sks' => 'nullable|integer|min:1|max:6',
            'warna' => 'nullable|in:blue,orange,green'
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($validated);

        return redirect()->route('jadwal.index')
                       ->with('success', 'Jadwal berhasil diupdate');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('jadwal.index')
                       ->with('success', 'Jadwal berhasil dihapus');
    }

    // API untuk pencarian
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $results = Jadwal::where('nama_matkul', 'LIKE', "%{$query}%")
                        ->orWhere('dosen', 'LIKE', "%{$query}%")
                        ->orWhere('ruangan', 'LIKE', "%{$query}%")
                        ->get();

        return response()->json($results);
    }
}