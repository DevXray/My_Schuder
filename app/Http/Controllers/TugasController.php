<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Dosen;
use App\Models\Materi;
use App\Models\Mahasiswa;
use App\Models\Pengumpulan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // ✅ FIXED: Get mahasiswa_id properly
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa && $user->isMahasiswa()) {
            return redirect()->route('dashboard')
                ->with('error', 'Data mahasiswa tidak ditemukan. Hubungi administrator.');
        }
        
        $query = Tugas::with(['dosen', 'materi']);

        // ✅ FIXED: Filter by status dengan mahasiswa_id yang benar
        if ($request->has('status') && $request->status != 'all') {
            if ($mahasiswa) {
                if ($request->status == 'pending') {
                    $query->whereDoesntHave('pengumpulans', function($q) use ($mahasiswa) {
                        $q->where('mahasiswa_id', $mahasiswa->id); // ✅ Gunakan mahasiswa->id
                    });
                } elseif ($request->status == 'submitted') {
                    $query->whereHas('pengumpulans', function($q) use ($mahasiswa) {
                        $q->where('mahasiswa_id', $mahasiswa->id); // ✅ Gunakan mahasiswa->id
                    });
                } elseif ($request->status == 'graded') {
                    $query->whereHas('pengumpulans', function($q) use ($mahasiswa) {
                        $q->where('mahasiswa_id', $mahasiswa->id)
                          ->whereNotNull('nilai');
                    });
                }
            }
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority != 'all') {
            $query->byPriority($request->priority);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'LIKE', "%{$request->search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$request->search}%");
            });
        }

        // Sort
        $query->orderBy('deadline', 'asc');

        $tugas = $query->get()->map(function($item) use ($mahasiswa) {
            if ($mahasiswa) {
                $pengumpulan = $item->pengumpulans()
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->first();
                
                $item->status = $pengumpulan ? 'submitted' : 'pending';
                $item->pengumpulan_data = $pengumpulan;
            } else {
                $item->status = 'pending';
                $item->pengumpulan_data = null;
            }
            
            return $item;
        });

        $stats = $this->getStats($mahasiswa);
        $counts = $this->getCounts($mahasiswa);

        return view('tugas.index', compact('tugas', 'stats', 'counts'));
    }

    public function create()
    {
        $dosens = Dosen::all();
        $materis = Materi::all();
        return view('tugas.create', compact('dosens', 'materis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'materi_id' => 'required|exists:materis,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_diberikan' => 'required|date',
            'deadline' => 'required|date|after:tanggal_diberikan',
            'bobot' => 'required|integer|min:0|max:100',
            'priority' => 'required|in:low,medium,high',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240'
        ]);

        if ($request->hasFile('file_soal')) {
            $validated['file_soal'] = $request->file('file_soal')->store('tugas', 'public');
        }

        Tugas::create($validated);

        return redirect()->route('tugas.index')
                       ->with('success', 'Tugas berhasil ditambahkan');
    }

    public function show($id)
    {
        $tugas = Tugas::with(['dosen', 'materi'])->findOrFail($id);
        
        // ✅ Get data pengumpulan jika mahasiswa
        $pengumpulan = null;
        if (auth()->user()->isMahasiswa()) {
            $mahasiswas = Mahasiswa::where('user_id', auth()->id())->first();
            if ($mahasiswas) {
                $pengumpulan = Pengumpulan::where('tugas_id', $id)
                                          ->where('mahasiswa_id', $mahasiswa->id)
                                          ->first();
            }
        }
        
        return view('tugas.show', compact('tugas', 'pengumpulan'));
    }

    public function edit($id)
    {
        $tugas = Tugas::findOrFail($id);
        $dosens = Dosen::all();
        $materis = Materi::all();
        return view('tugas.edit', compact('tugas', 'dosens', 'materis'));
    }

    public function update(Request $request, $id)
    {
        $tugas = Tugas::findOrFail($id);

        $validated = $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'materi_id' => 'required|exists:materis,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_diberikan' => 'required|date',
            'deadline' => 'required|date|after:tanggal_diberikan',
            'bobot' => 'required|integer|min:0|max:100',
            'priority' => 'required|in:low,medium,high',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240'
        ]);

        if ($request->hasFile('file_soal')) {
            if ($tugas->file_soal) {
                Storage::disk('public')->delete($tugas->file_soal);
            }
            $validated['file_soal'] = $request->file('file_soal')->store('tugas', 'public');
        }

        $tugas->update($validated);

        return redirect()->route('tugas.index')
                       ->with('success', 'Tugas berhasil diupdate');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);

        if ($tugas->file_soal) {
            Storage::disk('public')->delete($tugas->file_soal);
        }

        $tugas->delete();

        return redirect()->route('tugas.index')
                       ->with('success', 'Tugas berhasil dihapus');
    }

    // Submit tugas (mahasiswa mengumpulkan)
   // app/Http/Controllers/TugasController.php

// app/Http/Controllers/TugasController.php - METHOD SUBMIT (FIXED)
// ✅ Helper method untuk stats
    private function getStats($mahasiswa)
    {
        if (!$mahasiswa) {
            return [
                'deadline_dekat' => 0,
                'belum_dikumpulkan' => 0,
                'sudah_dikumpulkan' => 0,
                'rata_rata' => 0,
            ];
        }

        $threeDaysLater = Carbon::now()->addDays(3);
        
        $deadlineDekat = Tugas::where('deadline', '<=', $threeDaysLater)
            ->where('deadline', '>=', Carbon::now())
            ->whereDoesntHave('pengumpulans', function($q) use ($mahasiswa) {
                $q->where('mahasiswa_id', $mahasiswa->id);
            })
            ->count();

        $belumDikumpulkan = Tugas::whereDoesntHave('pengumpulans', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->count();

        $sudahDikumpulkan = Tugas::whereHas('pengumpulans', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->count();

        $rataRata = Pengumpulan::where('mahasiswa_id', $mahasiswa->id)
            ->whereNotNull('nilai')
            ->avg('nilai');

        return [
            'deadline_dekat' => $deadlineDekat,
            'belum_dikumpulkan' => $belumDikumpulkan,
            'sudah_dikumpulkan' => $sudahDikumpulkan,
            'rata_rata' => $rataRata ? round($rataRata) : 0,
        ];
    }

    // ✅ Helper method untuk counts
    private function getCounts($mahasiswa)
    {
        if (!$mahasiswa) {
            return [
                'all' => Tugas::count(),
                'pending' => 0,
                'submitted' => 0,
                'graded' => 0,
            ];
        }

        $all = Tugas::count();
        
        $pending = Tugas::whereDoesntHave('pengumpulans', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->count();

        $submitted = Tugas::whereHas('pengumpulans', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id)
              ->whereNull('nilai');
        })->count();

        $graded = Tugas::whereHas('pengumpulans', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id)
              ->whereNotNull('nilai');
        })->count();

        return [
            'all' => $all,
            'pending' => $pending,
            'submitted' => $submitted,
            'graded' => $graded,
        ];
    }

// ✅ FIXED: Submit method dengan AJAX response
    public function submit(Request $request, $id)
    {
        \Log::info('Submit request received', [
            'tugas_id' => $id,
            'user_id' => auth()->id(),
            'has_file' => $request->hasFile('file_jawaban'),
            'request_data' => $request->except('file_jawaban'),
        ]);

        $tugas = Tugas::findOrFail($id);
        
        // ✅ Validasi input
        $validated = $request->validate([
            'file_jawaban' => 'required|file|mimes:pdf,doc,docx,zip|max:20480',
            'catatan' => 'nullable|string|max:1000'
        ], [
            'file_jawaban.required' => 'File jawaban harus diupload',
            'file_jawaban.mimes' => 'Format file harus PDF, DOC, DOCX, atau ZIP',
            'file_jawaban.max' => 'Ukuran file maksimal 20MB'
        ]);

        DB::beginTransaction();
        
        try {
            $user = auth()->user();
            
            // ✅ Get mahasiswa
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            
            if (!$mahasiswa) {
                $mahasiswa = Mahasiswa::where('email', $user->email)->first();
            }
            
            if (!$mahasiswa) {
                \Log::error('Mahasiswa not found', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                DB::rollBack();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data mahasiswa tidak ditemukan. Hubungi administrator.'
                    ], 404);
                }
                
                return back()->withErrors(['error' => 'Data mahasiswa tidak ditemukan.']);
            }

            \Log::info('Mahasiswa found', [
                'mahasiswa_id' => $mahasiswa->id,
                'nama' => $mahasiswa->nama
            ]);

            // ✅ Cek existing
            $existingPengumpulan = Pengumpulan::where('tugas_id', $tugas->id)
                                              ->where('mahasiswa_id', $mahasiswa->id)
                                              ->first();
            
            // ✅ Upload file
            $filePath = null;
            if ($request->hasFile('file_jawaban')) {
                if ($existingPengumpulan && $existingPengumpulan->file_tugas) {
                    Storage::disk('public')->delete($existingPengumpulan->file_tugas);
                }
                
                $file = $request->file('file_jawaban');
                $fileName = time() . '_' . $mahasiswa->id . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('pengumpulan_tugas', $fileName, 'public');
                
                \Log::info('File uploaded', [
                    'path' => $filePath,
                    'size' => $file->getSize()
                ]);
            }

            $waktuPengumpulan = Carbon::now();
            
            if ($existingPengumpulan) {
                $existingPengumpulan->update([
                    'file_tugas' => $filePath,
                    'waktu_pengumpulan' => $waktuPengumpulan,
                ]);
                $message = 'Tugas berhasil diperbarui!';
            } else {
                Pengumpulan::create([
                    'tugas_id' => $tugas->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'file_tugas' => $filePath,
                    'waktu_pengumpulan' => $waktuPengumpulan,
                ]);
                $message = 'Tugas berhasil dikumpulkan!';
            }

            DB::commit();
            
            \Log::info('Submission successful', [
                'tugas_id' => $tugas->id,
                'mahasiswa_id' => $mahasiswa->id
            ]);

            $tepatWaktu = $waktuPengumpulan->lte($tugas->deadline);
            $statusMessage = $tepatWaktu ? '' : ' (Terlambat)';

            // ✅ Return JSON untuk AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message . $statusMessage
                ]);
            }

            return redirect()->route('tugas.index')
                           ->with('success', $message . $statusMessage);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($filePath) && $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            
            \Log::error('Submit error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengumpulkan tugas: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Gagal mengumpulkan tugas: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * ✅ SHOW TUGAS - Include data pengumpulan
     */
    

    /**
     * ✅ DOWNLOAD FILE JAWABAN
     */
    public function downloadJawaban($id)
    {
        $pengumpulan = Pengumpulan::findOrFail($id);
        
        if (!$pengumpulan->file_tugas || !Storage::disk('public')->exists($pengumpulan->file_tugas)) {
            return back()->with('error', 'File tidak ditemukan');
        }
        
        return Storage::disk('public')->download($pengumpulan->file_tugas);
    }
}