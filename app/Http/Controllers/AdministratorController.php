<?php
// app/Http/Controllers/AdministratorController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdministratorController extends Controller
{
    // Dashboard
    public function index()
    {
        $stats = [
            'total_admin' => User::where('role', 'admin')->count(),
            'total_dosen' => Dosen::count(),
            'total_mahasiswa' => Mahasiswa::count(),
            'total_matakuliah' => MataKuliah::count(),
        ];

        $recentDosen = Dosen::latest()->take(5)->get();
        $recentMahasiswa = Mahasiswa::latest()->take(5)->get();

        return view('administrator.index', compact('stats', 'recentDosen', 'recentMahasiswa'));
    }

    // ===== MAHASISWA CRUD =====
    public function mahasiswaIndex()
    {
        $mahasiswas = Mahasiswa::latest()->paginate(15);
        return view('administrator.mahasiswa.index', compact('mahasiswas'));
    }

    public function mahasiswaCreate()
    {
        return view('administrator.mahasiswa.create');
    }

    public function mahasiswaStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'required|string|max:20|unique:mahasiswas,nim',
            'email' => 'required|email|unique:mahasiswas,email',
            'password' => 'required|min:8',
            'jurusan' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        Mahasiswa::create($validated);

        return redirect()->route('administrator.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan');
    }

    public function mahasiswaEdit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        return view('administrator.mahasiswa.edit', compact('mahasiswa'));
    }

    public function mahasiswaUpdate(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'required|string|max:20|unique:mahasiswas,nim,'.$id,
            'email' => 'required|email|unique:mahasiswas,email,'.$id,
            'jurusan' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $mahasiswa->update($validated);

        return redirect()->route('administrator.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil diupdate');
    }

    public function mahasiswaDestroy($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->delete();

        return redirect()->route('administrator.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil dihapus');
    }

    // ===== DOSEN CRUD =====
    public function dosenIndex()
    {
        $dosens = Dosen::latest()->paginate(15);
        return view('administrator.dosen.index', compact('dosens'));
    }

    public function dosenCreate()
    {
        return view('administrator.dosen.create');
    }

    public function dosenStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nidn' => 'required|string|max:20|unique:dosens,nidn',
            'email' => 'required|email|unique:dosens,email',
            'password' => 'required|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        Dosen::create($validated);

        return redirect()->route('administrator.dosen.index')
            ->with('success', 'Dosen berhasil ditambahkan');
    }

    public function dosenEdit($id)
    {
        $dosen = Dosen::findOrFail($id);
        return view('administrator.dosen.edit', compact('dosen'));
    }

    public function dosenUpdate(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nidn' => 'required|string|max:20|unique:dosens,nidn,'.$id,
            'email' => 'required|email|unique:dosens,email,'.$id,
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $dosen->update($validated);

        return redirect()->route('administrator.dosen.index')
            ->with('success', 'Dosen berhasil diupdate');
    }

    public function dosenDestroy($id)
    {
        $dosen = Dosen::findOrFail($id);
        $dosen->delete();

        return redirect()->route('administrator.dosen.index')
            ->with('success', 'Dosen berhasil dihapus');
    }

    // ===== MATA KULIAH CRUD =====
    public function mataKuliahIndex()
    {
        $mataKuliahs = MataKuliah::with('dosen')->latest()->paginate(15);
        return view('administrator.matakuliah.index', compact('mataKuliahs'));
    }

    public function mataKuliahCreate()
    {
        $dosens = Dosen::all();
        return view('administrator.matakuliah.create', compact('dosens'));
    }

    public function mataKuliahStore(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliahs,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'dosen_id' => 'required|exists:dosens,id',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'kategori' => 'required|in:wajib,pilihan',
            'deskripsi' => 'nullable|string',
        ]);

        MataKuliah::create($validated);

        return redirect()->route('administrator.matakuliah.index')
            ->with('success', 'Mata Kuliah berhasil ditambahkan');
    }

    public function mataKuliahEdit($id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        $dosens = Dosen::all();
        return view('administrator.matakuliah.edit', compact('mataKuliah', 'dosens'));
    }

    public function mataKuliahUpdate(Request $request, $id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);

        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliahs,kode_mk,'.$id,
            'nama_mk' => 'required|string|max:255',
            'dosen_id' => 'required|exists:dosens,id',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'kategori' => 'required|in:wajib,pilihan',
            'deskripsi' => 'nullable|string',
        ]);

        $mataKuliah->update($validated);

        return redirect()->route('administrator.matakuliah.index')
            ->with('success', 'Mata Kuliah berhasil diupdate');
    }

    public function mataKuliahDestroy($id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        $mataKuliah->delete();

        return redirect()->route('administrator.matakuliah.index')
            ->with('success', 'Mata Kuliah berhasil dihapus');
    }
}