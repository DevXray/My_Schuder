<?php
// ============================================
// app/Http/Controllers/AdministratorController.php
// ============================================
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdministratorController extends Controller
{
    // ========== DASHBOARD ==========
    public function index()
    {
        $stats = [
            'total_mahasiswa' => Mahasiswa::count(),
            'total_dosen' => Dosen::count(),
            'total_admin' => Admin::count(),
            'total_users' => User::count(),
        ];

        $recentMahasiswa = Mahasiswa::with('user')->latest()->take(5)->get();
        $recentDosen = Dosen::with('user')->latest()->take(5)->get();

        return view('administrator.index', compact('stats', 'recentMahasiswa', 'recentDosen'));
    }

    // ========== MAHASISWA MANAGEMENT ==========
    public function mahasiswaIndex()
    {
        $mahasiswas = Mahasiswa::with('user.role')->paginate(20);
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
            'password' => 'required|min:8|confirmed',
            'nim' => 'required|string|unique:mahasiswas,nim',
            'phone' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'kelas' => 'nullable|string',
            'angkatan' => 'nullable|string',
            'program_studi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => Role::mahasiswa()->id,
            ]);

            // 2. Create Mahasiswa Profile
            Mahasiswa::create([
                'user_id' => $user->id,
                'nama' => $validated['name'],
                'nim' => $validated['nim'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'jurusan' => $validated['jurusan'] ?? null,
                'kelas' => $validated['kelas'] ?? null,
                'angkatan' => $validated['angkatan'] ?? null,
                'program_studi' => $validated['program_studi'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();
            return redirect()->route('administrator.mahasiswa.index')
                           ->with('success', 'Mahasiswa berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan mahasiswa: ' . $e->getMessage());
        }
    }

    public function mahasiswaEdit($id)
    {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);
        return view('administrator.mahasiswa.edit', compact('mahasiswa'));
    }

    public function mahasiswaUpdate(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,
            'nim' => 'required|string|unique:mahasiswas,nim,' . $id,
            'phone' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'kelas' => 'nullable|string',
            'angkatan' => 'nullable|string',
            'program_studi' => 'nullable|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // Update User
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $mahasiswa->user->update($userData);

            // Update Mahasiswa
            $mahasiswaData = [
                'nama' => $validated['name'],
                'nim' => $validated['nim'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'jurusan' => $validated['jurusan'] ?? null,
                'kelas' => $validated['kelas'] ?? null,
                'angkatan' => $validated['angkatan'] ?? null,
                'program_studi' => $validated['program_studi'] ?? null,
            ];
            if (!empty($validated['password'])) {
                $mahasiswaData['password'] = Hash::make($validated['password']);
            }
            $mahasiswa->update($mahasiswaData);

            DB::commit();
            return redirect()->route('administrator.mahasiswa.index')
                           ->with('success', 'Mahasiswa berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengupdate mahasiswa: ' . $e->getMessage());
        }
    }

    public function mahasiswaDestroy($id)
    {
        DB::beginTransaction();
        try {
            $mahasiswa = Mahasiswa::findOrFail($id);
            $user = $mahasiswa->user;

            $mahasiswa->delete();
            $user->delete();

            DB::commit();
            return redirect()->route('administrator.mahasiswa.index')
                           ->with('success', 'Mahasiswa berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus mahasiswa: ' . $e->getMessage());
        }
    }

    // ========== DOSEN MANAGEMENT ==========
    public function dosenIndex()
    {
        $dosens = Dosen::with('user.role')->paginate(20);
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
            'password' => 'required|min:8|confirmed',
            'nidn' => 'required|string|unique:dosens,nidn',
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => Role::dosen()->id,
            ]);

            // 2. Create Dosen Profile
            Dosen::create([
                'user_id' => $user->id,
                'nama' => $validated['name'],
                'nidn' => $validated['nidn'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'department' => $validated['department'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();
            return redirect()->route('administrator.dosen.index')
                           ->with('success', 'Dosen berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan dosen: ' . $e->getMessage());
        }
    }

    public function dosenEdit($id)
    {
        $dosen = Dosen::with('user')->findOrFail($id);
        return view('administrator.dosen.edit', compact('dosen'));
    }

    public function dosenUpdate(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->user_id,
            'nidn' => 'required|string|unique:dosens,nidn,' . $id,
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // Update User
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $dosen->user->update($userData);

            // Update Dosen
            $dosenData = [
                'nama' => $validated['name'],
                'nidn' => $validated['nidn'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'department' => $validated['department'] ?? null,
            ];
            if (!empty($validated['password'])) {
                $dosenData['password'] = Hash::make($validated['password']);
            }
            $dosen->update($dosenData);

            DB::commit();
            return redirect()->route('administrator.dosen.index')
                           ->with('success', 'Dosen berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengupdate dosen: ' . $e->getMessage());
        }
    }

    public function dosenDestroy($id)
    {
        DB::beginTransaction();
        try {
            $dosen = Dosen::findOrFail($id);
            $user = $dosen->user;

            $dosen->delete();
            $user->delete();

            DB::commit();
            return redirect()->route('administrator.dosen.index')
                           ->with('success', 'Dosen berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus dosen: ' . $e->getMessage());
        }
    }
}