{{-- resources/views/dashboard/users/index.blade.php - FIXED DELETE --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Semua User - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/app.js'])
    
    <style>
        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-admin { background: #fee2e2; color: #991b1b; }
        .role-dosen { background: #dbeafe; color: #1e40af; }
        .role-mahasiswa { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-users-cog"></i> Kelola Semua User</h1>
                    <p>Manajemen user, role, dan akses sistem</p>
                </div>
                <a href="{{ route('users.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah User Baru
                </a>
            </div>
        </section>

        @if(session('success'))
        <div class="alert alert-success" style="margin: 20px; padding: 15px; background: #d4edda; color: #155724; border-radius: 8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error" style="margin: 20px; padding: 15px; background: #f8d7da; color: #721c24; border-radius: 8px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        {{-- Table --}}
        <section class="card">
            <div class="card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th style="padding: 1rem; text-align: left;">Nama</th>
                                <th style="padding: 1rem; text-align: left;">Email</th>
                                <th style="padding: 1rem; text-align: left;">Role</th>
                                <th style="padding: 1rem; text-align: left;">Terdaftar</th>
                                <th style="padding: 1rem; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;">
                                                {{ $user->name }}
                                                @if($user->id === auth()->id())
                                                <span style="background: #fef3c7; color: #92400e; padding: 0.125rem 0.5rem; border-radius: 6px; font-size: 0.75rem; margin-left: 0.5rem;">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; color: #6b7280;">{{ $user->email }}</td>
                                <td style="padding: 1rem;">
                                    <span class="role-badge role-{{ $user->getRoleName() }}">
                                        {{ $user->getRoleDisplayName() }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: #6b7280; font-size: 0.875rem;">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                        
                                        {{-- Edit Button --}}
                                        <a href="{{ route('users.edit', $user->id) }}" 
                                           style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border-radius: 8px; text-decoration: none; font-size: 0.875rem;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
                                        {{-- ✅ FIXED: Delete Button - Direct Form Submit with SweetAlert --}}
                                        @if($user->id !== auth()->id())
                                        <form id="delete-form-{{ $user->id }}" 
                                              action="{{ route('users.destroy', $user->id) }}" 
                                              method="POST" 
                                              style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')" 
                                                    style="padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem;">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="padding: 3rem; text-align: center; color: #9ca3af;">
                                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                    Belum ada data user
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </section>
    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>

    <script>
        // ✅ FIXED: Delete confirmation
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus User?',
                html: `Yakin ingin menghapus user <strong>${name}</strong>?<br>Data tidak dapat dikembalikan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form directly
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.3s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>