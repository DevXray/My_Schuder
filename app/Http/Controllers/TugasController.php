<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Dosen;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Tugas::with(['dosen', 'materi']);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->byStatus($request->status);
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

        $tugas = $query->get();
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

        $validated['status'] = 'pending';

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
        return view('tugas.show', compact('pengumpulan'));
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
        if ($tugas->file_jawaban) {
            Storage::disk('public')->delete($tugas->file_jawaban);
        }

        $tugas->delete();

        return redirect()->route('tugas.index')
                       ->with('success', 'Tugas berhasil dihapus');
    }

    // Submit tugas (mahasiswa mengumpulkan)
    public function submit(Request $request, $id)
    {
        $tugas = Tugas::findOrFail($id);

        $validated = $request->validate([
            'file_jawaban' => 'required|file|mimes:pdf,doc,docx,zip|max:20480'
        ]);

        if ($request->hasFile('file_jawaban')) {
            if ($tugas->file_jawaban) {
                Storage::disk('public')->delete($tugas->file_jawaban);
            }
            $validated['file_jawaban'] = $request->file('file_jawaban')->store('jawaban', 'public');
        }

        $tugas->update([
            'status' => 'submitted',
            'file_jawaban' => $validated['file_jawaban'],
            'waktu_pengumpulan' => Carbon::now(),
            'tepat_waktu' => Carbon::now()->lte($tugas->deadline)
        ]);

        return redirect()->route('tugas.index')
                       ->with('success', 'Tugas berhasil dikumpulkan');
    }

    // Grade tugas (dosen memberi nilai)
    public function grade(Request $request, $id)
    {
        $tugas = Tugas::findOrFail($id);

        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'grade' => 'required|string|max:5',
            'feedback' => 'nullable|string'
        ]);

        $tugas->update([
            'status' => 'graded',
            'nilai' => $validated['nilai'],
            'grade' => $validated['grade'],
            'feedback' => $validated['feedback']
        ]);

        return redirect()->route('tugas.show', $id)
                       ->with('success', 'Tugas berhasil dinilai');
    }
}