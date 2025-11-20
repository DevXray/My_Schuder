<?php

// app/Http/Controllers/MateriController.php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index(Request $request)
    {
        $query = Materi::with('dosen');

        // Filter berdasarkan kategori
        if ($request->has('kategori') && $request->kategori != 'all') {
            $query->byKategori($request->kategori);
        }

        // Filter berdasarkan status
        if ($request->has('filter') && $request->filter != 'all') {
            if ($request->filter == 'recent') {
                $query->latest();
            } else {
                $query->byStatus($request->filter);
            }
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name':
                    $query->orderBy('judul', 'asc');
                    break;
                case 'progress':
                    $query->orderBy('progress', 'desc');
                    break;
            }
        } else {
            $query->latest();
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('judul', 'LIKE', "%{$request->search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$request->search}%");
        }

        $materis = $query->get();
        $stats = Materi::getStats();
        $dosens = Dosen::all();

        return view('materi.index', compact('materis', 'stats', 'dosens'));
    }

    public function create()
    {
        $dosens = Dosen::all();
        return view('materi.create', compact('dosens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|in:pemrograman,matematika,database,jaringan',
            'jumlah_modul' => 'required|integer|min:1',
            'durasi_jam' => 'required|integer|min:1',
            'icon' => 'nullable|string',
            'warna' => 'nullable|in:blue,orange,green',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240'
        ]);

        // Set default values
        $validated['progress'] = 0;
        $validated['status'] = 'new';
        $validated['icon'] = $validated['icon'] ?? 'fa-book';
        $validated['warna'] = $validated['warna'] ?? 'blue';

        // Handle file upload
        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('materi', 'public');
        }

        Materi::create($validated);

        return redirect()->route('materi.index')
                       ->with('success', 'Materi berhasil ditambahkan');
    }

    public function show($id)
    {
        $materi = Materi::with('dosen')->findOrFail($id);
        return view('materi.show', compact('materi'));
    }

    public function edit($id)
    {
        $materi = Materi::findOrFail($id);
        $dosens = Dosen::all();
        return view('materi.edit', compact('materi', 'dosens'));
    }

    public function update(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);

        $validated = $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|in:pemrograman,matematika,database,jaringan',
            'jumlah_modul' => 'required|integer|min:1',
            'durasi_jam' => 'required|integer|min:1',
            'progress' => 'nullable|integer|min:0|max:100',
            'status' => 'nullable|in:new,progress,completed',
            'icon' => 'nullable|string',
            'warna' => 'nullable|in:blue,orange,green',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($materi->file) {
                Storage::disk('public')->delete($materi->file);
            }
            $validated['file'] = $request->file('file')->store('materi', 'public');
        }

        // Auto update status based on progress
        if (isset($validated['progress'])) {
            if ($validated['progress'] == 0) {
                $validated['status'] = 'new';
            } elseif ($validated['progress'] == 100) {
                $validated['status'] = 'completed';
            } else {
                $validated['status'] = 'progress';
            }
        }

        $materi->update($validated);

        return redirect()->route('materi.index')
                       ->with('success', 'Materi berhasil diupdate');
    }

    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);

        // Delete file if exists
        if ($materi->file) {
            Storage::disk('public')->delete($materi->file);
        }

        $materi->delete();

        return redirect()->route('materi.index')
                       ->with('success', 'Materi berhasil dihapus');
    }

    // Update progress via AJAX
    public function updateProgress(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);
        
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        // Auto update status
        if ($validated['progress'] == 0) {
            $materi->status = 'new';
        } elseif ($validated['progress'] == 100) {
            $materi->status = 'completed';
        } else {
            $materi->status = 'progress';
        }

        $materi->progress = $validated['progress'];
        $materi->save();

        return response()->json([
            'success' => true,
            'materi' => $materi
        ]);
    }
}