<?php
// ============================================
// app/Http/Controllers/MatkulController.php (NEW)
// ============================================
namespace App\Http\Controllers;

use App\Models\Matkul;
use App\Models\Dosen;
use Illuminate\Http\Request;

class MatkulController extends Controller
{
    public function index(Request $request)
    {
        $query = Matkul::with('dosen');

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori != 'all') {
            $query->byKategori($request->kategori);
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by dosen (for dosen role)
        if (auth()->user()->isDosen()) {
            $dosenId = auth()->user()->dosen->id;
            $query->where('dosen_id', $dosenId);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_mk', 'LIKE', "%{$request->search}%")
                  ->orWhere('kode_mk', 'LIKE', "%{$request->search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$request->search}%");
            });
        }

        // Sort
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->latest();
                    break;
                case 'name':
                    $query->orderBy('nama_mk', 'asc');
                    break;
                case 'semester':
                    $query->orderBy('semester', 'asc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $matkuls = $query->get();
        $dosens = Dosen::all();
        
        $stats = [
            'total' => Matkul::count(),
            'aktif' => Matkul::where('status', 'aktif')->count(),
            'non_aktif' => Matkul::where('status', 'non-aktif')->count(),
        ];

        return view('materi.index', compact('matkuls', 'dosens', 'stats'));
    }

    public function create()
    {
        $dosens = Dosen::all();
        return view('matkul.create', compact('dosens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|unique:matkuls,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'dosen_id' => 'required|exists:dosens,id',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|in:pemrograman,matematika,database,jaringan,lainnya',
            'status' => 'nullable|in:aktif,non-aktif',
            'icon' => 'nullable|string',
            'warna' => 'nullable|in:blue,orange,green,purple',
        ]);

        $validated['status'] = $validated['status'] ?? 'aktif';
        $validated['icon'] = $validated['icon'] ?? 'fa-book';
        $validated['warna'] = $validated['warna'] ?? 'blue';

        Matkul::create($validated);

        return redirect()->route('matkul.index')
                       ->with('success', 'Mata kuliah berhasil ditambahkan');
    }

    public function show($id)
    {
        $matkul = Matkul::with(['dosen', 'materis', 'tugas'])->findOrFail($id);
        
        // Get stats
        $stats = [
            'total_materi' => $matkul->materis()->count(),
            'total_tugas' => $matkul->tugas()->count(),
            'completed_materi' => $matkul->materis()->where('status', 'completed')->count(),
        ];

        return view('matkul.show', compact('matkul', 'stats'));
    }

    public function edit($id)
    {
        $matkul = Matkul::findOrFail($id);
        $dosens = Dosen::all();
        
        // Check permission (dosen can only edit their own matkul)
        if (auth()->user()->isDosen()) {
            $dosenId = auth()->user()->dosen->id;
            if ($matkul->dosen_id != $dosenId) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengedit mata kuliah ini.');
            }
        }
        
        return view('matkul.edit', compact('matkul', 'dosens'));
    }

    public function update(Request $request, $id)
    {
        $matkul = Matkul::findOrFail($id);
        
        // Check permission
        if (auth()->user()->isDosen()) {
            $dosenId = auth()->user()->dosen->id;
            if ($matkul->dosen_id != $dosenId) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengedit mata kuliah ini.');
            }
        }

        $validated = $request->validate([
            'kode_mk' => 'required|string|unique:matkuls,kode_mk,' . $id,
            'nama_mk' => 'required|string|max:255',
            'dosen_id' => 'required|exists:dosens,id',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|in:pemrograman,matematika,database,jaringan,lainnya',
            'status' => 'nullable|in:aktif,non-aktif',
            'icon' => 'nullable|string',
            'warna' => 'nullable|in:blue,orange,green,purple',
        ]);

        $matkul->update($validated);

        return redirect()->route('matkul.index')
                       ->with('success', 'Mata kuliah berhasil diupdate');
    }

    public function destroy($id)
    {
        $matkul = Matkul::findOrFail($id);
        
        // Check permission
        if (auth()->user()->isDosen()) {
            $dosenId = auth()->user()->dosen->id;
            if ($matkul->dosen_id != $dosenId) {
                return back()->with('error', 'Anda tidak memiliki akses untuk menghapus mata kuliah ini.');
            }
        }

        // Check if matkul has materis or tugas
        if ($matkul->materis()->count() > 0 || $matkul->tugas()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus mata kuliah yang sudah memiliki materi atau tugas.');
        }

        $matkul->delete();

        return redirect()->route('matkul.index')
                       ->with('success', 'Mata kuliah berhasil dihapus');
    }
}