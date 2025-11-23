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
use Illuminate\Support\Facades\DB;

class AdministratorController extends Controller
{
    // Dashboard
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admin' => User::where('role', 'admin')->count(),
            'total_dosen' => User::where('role', 'dosen')->count(),
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();

        return view('administrator.index', compact('stats', 'recentUsers'));
    }

    // ===== USER MANAGEMENT (ALL USERS) =====
    public function userIndex()
    {
        $users = User::latest()->paginate(15);
        return view('administrator.users.index', compact('users'));
    }

    public function userCreate()
    {
        return view('administrator.users.create');
    }

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,dosen,mahasiswa',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);

        return redirect()->route('administrator.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        return view('administrator.users.edit', compact('user'));
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:admin,dosen,mahasiswa',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('administrator.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('administrator.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('administrator.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    // Quick role change
    public function changeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'role' => 'required|in:admin,dosen,mahasiswa',
        ]);

        // Prevent changing your own role
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengubah role sendiri!'
            ], 403);
        }

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diubah menjadi ' . $validated['role']
        ]);
    }

    // ===== MAHASISWA CRUD (Legacy - Optional) =====
    public function mahasiswaIndex()
    {
        $mahasiswas = User::where('role', 'mahasiswa')->latest()->paginate(15);
        return view('administrator.mahasiswa.index', compact('mahasiswas'));
    }

    public function mahasiswaCreate()
    {
        return view('administrator.mahasiswa.create');
    }

    public function mahasiswaStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'mahasiswa';
        
        User::create($validated);

        return redirect()->route('administrator.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan');
    }

    public function mahasiswaEdit($id)
    {
        $mahasiswa = User::where('role', 'mahasiswa')->findOrFail($id);
        return view('administrator.mahasiswa.edit', compact('mahasiswa'));
    }

    public function mahasiswaUpdate(Request $request, $id)
    {
        $mahasiswa = User::where('role', 'mahasiswa')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
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
        $mahasiswa = User::where('role', 'mahasiswa')->findOrFail($id);
        $mahasiswa->delete();

        return redirect()->route('administrator.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil dihapus');
    }

    // ===== DOSEN CRUD (Legacy - Optional) =====
    public function dosenIndex()
    {
        $dosens = User::where('role', 'dosen')->latest()->paginate(15);
        return view('administrator.dosen.index', compact('dosens'));
    }

    public function dosenCreate()
    {
        return view('administrator.dosen.create');
    }

    public function dosenStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'dosen';
        
        User::create($validated);

        return redirect()->route('administrator.dosen.index')
            ->with('success', 'Dosen berhasil ditambahkan');
    }

    public function dosenEdit($id)
    {
        $dosen = User::where('role', 'dosen')->findOrFail($id);
        return view('administrator.dosen.edit', compact('dosen'));
    }

    public function dosenUpdate(Request $request, $id)
    {
        $dosen = User::where('role', 'dosen')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
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
        $dosen = User::where('role', 'dosen')->findOrFail($id);
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
        $dosens = User::where('role', 'dosen')->get();
        return view('administrator.matakuliah.create', compact('dosens'));
    }

    public function mataKuliahStore(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliahs,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'dosen_id' => 'required|exists:users,id',
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
        $dosens = User::where('role', 'dosen')->get();
        return view('administrator.matakuliah.edit', compact('mataKuliah', 'dosens'));
    }

    public function mataKuliahUpdate(Request $request, $id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);

        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliahs,kode_mk,'.$id,
            'nama_mk' => 'required|string|max:255',
            'dosen_id' => 'required|exists:users,id',
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

    // ===== BULK ACTIONS =====
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Prevent deleting yourself
        $userIds = array_filter($validated['user_ids'], function($id) {
            return $id != auth()->id();
        });

        User::whereIn('id', $userIds)->delete();

        return redirect()->back()
            ->with('success', count($userIds) . ' user berhasil dihapus');
    }

    public function bulkChangeRole(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required|in:admin,dosen,mahasiswa',
        ]);

        // Prevent changing your own role
        $userIds = array_filter($validated['user_ids'], function($id) {
            return $id != auth()->id();
        });

        User::whereIn('id', $userIds)->update(['role' => $validated['role']]);

        return redirect()->back()
            ->with('success', count($userIds) . ' user role berhasil diubah menjadi ' . $validated['role']);
    }
}