<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Dosen;
use App\Models\Materi;
use App\Models\Mahasiswa;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = Tugas::with(['dosen', 'materi']);

        // Filter by status (pending/submitted)
        if ($request->has('status') && $request->status != 'all') {
            if ($request->status == 'pending') {
                $query->whereDoesntHave('pengumpulan', function($q) use ($userId) {
                    $q->where('mahasiswa_id', $userId);
                });
            } elseif ($request->status == 'submitted') {
                $query->whereHas('pengumpulan', function($q) use ($userId) {
                    $q->where('mahasiswa_id', $userId);
                });
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
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'deadline':
                    $query->orderBy('deadline', 'asc');
                    break;
                case 'terbaru':
                    $query->latest();
                    break;
                case 'bobot':
                    $query->orderBy('bobot', 'desc');
                    break;
                default:
                    $query->orderBy('deadline', 'asc');
            }
        } else {
            $query->orderBy('deadline', 'asc');
        }

        $tugas = $query->get()->map(function($item) use ($userId) {
            $pengumpulan = $item->pengumpulans()
                ->where('mahasiswa_id', $userId)
                ->first();
            
            $item->status = $pengumpulan ? 'submitted' : 'pending';
            $item->pengumpulan_data = $pengumpulan;
            
            return $item;
        });

        $stats = Tugas::getStats();
        $counts = Tugas::getCountByStatus();

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
            if ($mahasiswa) {
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
    public function submit(Request $request, $id)
    {
        $tugas = Tugas::findOrFail($id);
        
        // ✅ Validasi input
        $validated = $request->validate([
            'file_jawaban' => 'required|file|mimes:pdf,doc,docx,zip|max:20480', // Max 20MB
            'catatan' => 'nullable|string|max:1000'
        ], [
            'file_jawaban.required' => 'File jawaban harus diupload',
            'file_jawaban.mimes' => 'Format file harus PDF, DOC, DOCX, atau ZIP',
            'file_jawaban.max' => 'Ukuran file maksimal 20MB'
        ]);

        DB::beginTransaction();
        
        try {
            // ✅ 1. Get mahasiswa_id dari user yang sedang login
            $user = auth()->user();
            
            // Cari mahasiswa berdasarkan user_id
            $mahasiswas = Mahasiswa::where('user_id', $user->id)->first();
            
            if (!$mahasiswa) {
                return back()->withErrors(['error' => 'Data mahasiswa tidak ditemukan. Hubungi administrator.']);
            }

            // ✅ 2. Cek apakah sudah pernah mengumpulkan
            $existingPengumpulan = Pengumpulan::where('tugas_id', $tugas->id)
                                              ->where('mahasiswa_id', $mahasiswa->id)
                                              ->first();
            
            // ✅ 3. Upload file
            $filePath = null;
            if ($request->hasFile('file_jawaban')) {
                // Hapus file lama jika ada
                if ($existingPengumpulan && $existingPengumpulan->file_tugas) {
                    Storage::disk('public')->delete($existingPengumpulan->file_tugas);
                }
                
                // Upload file baru
                $file = $request->file('file_jawaban');
                $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('pengumpulan_tugas', $fileName, 'public');
            }

            // ✅ 4. Simpan/Update ke tabel pengumpulan_tugas
            $waktuPengumpulan = Carbon::now();
            
            if ($existingPengumpulan) {
                // Update pengumpulan yang sudah ada
                $existingPengumpulan->update([
                    'file_tugas' => $filePath,
                    'waktu_pengumpulan' => $waktuPengumpulan,
                ]);
                
                $message = 'Tugas berhasil diperbarui!';
            } else {
                // Buat pengumpulan baru
                Pengumpulan::create([
                    'tugas_id' => $tugas->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'file_tugas' => $filePath,
                    'waktu_pengumpulan' => $waktuPengumpulan,
                ]);
                
                $message = 'Tugas berhasil dikumpulkan!';
            }

            // ✅ 5. OPTIONAL: Update status di tabel tugas (jika masih digunakan)
            $tugas->update([
                'status' => 'submitted',
                'file_jawaban' => $filePath,
                'waktu_pengumpulan' => $waktuPengumpulan,
                'tepat_waktu' => $waktuPengumpulan->lte($tugas->deadline)
            ]);

            DB::commit();

            return redirect()->route('tugas.index')
                           ->with('success', $message . ($waktuPengumpulan->gt($tugas->deadline) ? ' (Terlambat)' : ''));
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus file jika ada error
            if (isset($filePath) && $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            
            \Log::error('Error submitting tugas: ' . $e->getMessage());
            
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