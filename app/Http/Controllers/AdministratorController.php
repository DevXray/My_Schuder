<?php
// app/Http/Controllers/AdministratorController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Role; // ✅ Import Role model
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
            'total_admin' => User::byRole('admin')->count(),
            'total_dosen' => User::byRole('dosen')->count(),
            'total_mahasiswa' => User::byRole('mahasiswa')->count(),
        ];

        $recentUsers = User::with('role')->latest()->take(10)->get();

        return view('dashboard.admin', compact('stats', 'recentUsers'));
    }

    // ===== USER MANAGEMENT (ALL USERS) =====
    public function userIndex()
    {
        $users = User::with('role')->latest()->paginate(15);
        $roles = Role::all(); // ✅ Pass roles to view
        return view('dashboard.users.index', compact('users', 'roles'));
    }

    public function userCreate()
    {
        $roles = Role::all(); // ✅ Pass roles to view
        return view('dashboard.users.create', compact('roles'));
    }

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_name' => 'required|in:admin,dosen,mahasiswa', // ✅ Changed to role_name
        ]);

        // ✅ Get role_id from role name
        $roleId = Role::getIdByName($validated['role_name']);
        
        if (!$roleId) {
            return back()->withErrors(['role_name' => 'Role tidak valid']);
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $roleId, // ✅ Store role_id
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function userEdit($id)
    {
        $user = User::with('role')->findOrFail($id);
        $roles = Role::all(); // ✅ Pass roles to view
        return view('dashboard.users.edit', compact('user', 'roles'));
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role_name' => 'required|in:admin,dosen,mahasiswa', // ✅ Changed to role_name
            'password' => 'nullable|min:8|confirmed',
        ]);

        // ✅ Get role_id from role name
        $roleId = Role::getIdByName($validated['role_name']);
        
        if (!$roleId) {
            return back()->withErrors(['role_name' => 'Role tidak valid']);
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $roleId, // ✅ Update role_id
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('dashboard.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }

    // Quick role change
    public function changeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'role_name' => 'required|in:admin,dosen,mahasiswa', // ✅ Changed to role_name
        ]);

        // Prevent changing your own role
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengubah role sendiri!'
            ], 403);
        }

        // ✅ Get role_id from role name
        $roleId = Role::getIdByName($validated['role_name']);
        
        if (!$roleId) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak valid'
            ], 400);
        }

        // ✅ Update role_id
        $user->role_id = $roleId;
        $user->save();
        
        // Refresh to get updated role relationship
        $user->refresh();
        
        // ✅ Log untuk debugging
        \Log::info('Role changed', [
            'user_id' => $user->id,
            'new_role_id' => $roleId,
            'new_role_name' => $user->getRoleName()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diubah menjadi ' . $user->getRoleDisplayName(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role_name' => $user->getRoleName(),
                'role_display' => $user->getRoleDisplayName()
            ]
        ]);
    }

    // ===== MAHASISWA CRUD (Legacy - Optional) =====
    public function mahasiswaIndex()
    {
        // ✅ FIXED: Query mahasiswa table, bukan users
        $mahasiswas = Mahasiswa::with('user')->latest()->paginate(15);
        return view('dashboard.mahasiswa.index', compact('mahasiswas'));
    }

    public function mahasiswaCreate()
    {
        return view('dashboard.mahasiswa.create');
    }

    // app/Http/Controllers/AdministratorController.php

public function mahasiswaStore(Request $request)
{
    $validated = $request->validate([
        'nim' => 'required|string|max:20|unique:mahasiswas,nim',
        'nama' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'jurusan' => 'nullable|string|max:100',
        'kelas' => 'nullable|string|max:50',
    ]);

    DB::beginTransaction();
    
    try {
        // ✅ 1. Buat User dulu
        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => Role::getIdByName('mahasiswa'), // Auto set role mahasiswa
        ]);

        // ✅ 2. Buat Mahasiswa linked ke User
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $validated['nim'],
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'jurusan' => $validated['jurusan'] ?? null,
            'kelas' => $validated['kelas'] ?? null,
        ]);

        DB::commit();

        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa dan User berhasil ditambahkan');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Gagal menambahkan mahasiswa: ' . $e->getMessage()])
            ->withInput();
    }
}

public function mahasiswaUpdate(Request $request, $id)
{
     $mahasiswa= Mahasiswa::findOrFail($id);

    $validated = $request->validate([
        'nim' => 'required|string|max:20|unique:mahasiswas,nim,' . $id,
        'nama' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,
        'password' => 'nullable|min:8',
        'jurusan' => 'nullable|string|max:100',
        'kelas' => 'nullable|string|max:50',
    ]);

    DB::beginTransaction();
    
    try {
        // ✅ Update User
        $user = User::findOrFail($mahasiswa->user_id);
        $userData = [
            'name' => $validated['nama'],
            'email' => $validated['email'],
        ];
        
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $user->update($userData);

        // ✅ Update Mahasiswa
        $mahasiswaData = [
            'nim' => $validated['nim'],
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'jurusan' => $validated['jurusan'] ?? null,
            'kelas' => $validated['kelas'] ?? null,
        ];
        
        if ($request->filled('password')) {
            $mahasiswaData['password'] = Hash::make($request->password);
        }
        
        $mahasiswa->update($mahasiswaData);

        DB::commit();

        return redirect()->route('dashboard.mahasiswa.index')
            ->with('success', 'Mahasiswa dan User berhasil diupdate');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Gagal mengupdate mahasiswa: ' . $e->getMessage()])
            ->withInput();
    }
}

public function mahasiswaDestroy($id)
{
    DB::beginTransaction();
    
    try {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = User::findOrFail($mahasiswa->user_id);
        
        // ✅ Hapus mahasiswa dulu (karena foreign key)
        $mahasiswa->delete();
        
        // ✅ Hapus user
        $user->delete();
        
        DB::commit();

        return redirect()->route('dashboard.mahasiswa.index')
            ->with('success', 'Mahasiswa dan User berhasil dihapus');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menghapus mahasiswa: ' . $e->getMessage());
    }
}

    // ===== DOSEN CRUD (Legacy - Optional) =====
    public function dosenIndex()
    {
        $dosens = User::where('role_id', 'dosen')->latest()->paginate(15);
        return view('dashboard.dosen.index', compact('dosens'));
    }

    public function dosenCreate()
    {
        return view('dashboard.dosen.create');
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