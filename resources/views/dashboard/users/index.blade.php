{{-- resources/views/administrator/users/index.blade.php --}}
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
        
        .quick-role-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .quick-role-btn:hover {
            background: #f3f4f6;
            border-color: #3b82f6;
        }
        
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .filter-tab {
            padding: 0.75rem 1.5rem;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.2s;
        }
        .filter-tab:hover {
            color: #3b82f6;
        }
        .filter-tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }
        
        .bulk-actions {
            display: none;
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            gap: 0.5rem;
            align-items: center;
        }
        .bulk-actions.show {
            display: flex;
        }
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
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        {{-- Filter Tabs --}}
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterRole('all')">
                <i class="fas fa-users"></i> Semua ({{ $users->total() }})
            </button>
            <button class="filter-tab" onclick="filterRole('admin')">
                <i class="fas fa-user-shield"></i> Admin
            </button>
            <button class="filter-tab" onclick="filterRole('dosen')">
                <i class="fas fa-chalkboard-teacher"></i> Dosen
            </button>
            <button class="filter-tab" onclick="filterRole('mahasiswa')">
                <i class="fas fa-user-graduate"></i> Mahasiswa
            </button>
        </div>

        {{-- Bulk Actions Bar --}}
        <div class="bulk-actions" id="bulkActions">
            <span id="selectedCount">0</span> user dipilih
            <button class="btn-sm btn-primary" onclick="bulkChangeRole()">
                <i class="fas fa-exchange-alt"></i> Ubah Role
            </button>
            <button class="btn-sm btn-danger" onclick="bulkDelete()">
                <i class="fas fa-trash"></i> Hapus
            </button>
            <button class="btn-sm" onclick="deselectAll()">
                <i class="fas fa-times"></i> Batal
            </button>
        </div>

        {{-- Table --}}
        <section class="card">
            <div class="card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th style="padding: 1rem; width: 40px;">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                                </th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Nama</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Email</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Role</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Terdaftar</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="user-row" data-role="{{ $user->getRoleName() }}" style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem;">
                                    <input type="checkbox" class="user-checkbox" value="{{ $user->id }}" 
                                           {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                           onchange="updateBulkActions()">
                                </td>
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
                                        {{-- Quick Role Change --}}
                                        @if($user->id !== auth()->id())
                                        <div style="display: flex; gap: 0.25rem;">
                                            @if($user->getRoleName() !== 'admin')
                                            <button class="quick-role-btn" onclick="quickChangeRole({{ $user->id }}, 'admin')" title="Jadikan Admin">
                                                <i class="fas fa-shield-alt"></i>
                                            </button>
                                            @endif
                                            @if($user->getRoleName() !== 'dosen')
                                            <button class="quick-role-btn" onclick="quickChangeRole({{ $user->id }}, 'dosen')" title="Jadikan Dosen">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                            </button>
                                            @endif
                                            @if($user->getRoleName() !== 'mahasiswa')
                                            <button class="quick-role-btn" onclick="quickChangeRole({{ $user->id }}, 'mahasiswa')" title="Jadikan Mahasiswa">
                                                <i class="fas fa-user-graduate"></i>
                                            </button>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        {{-- Edit Button --}}
                                        <a href="{{ route('users.edit', $user->id) }}" 
                                           style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border-radius: 8px; text-decoration: none; font-size: 0.875rem;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        {{-- Delete Button --}}
                                        @if($user->id !== auth()->id())
                                        <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" 
                                                style="padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                    
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="padding: 3rem; text-align: center; color: #9ca3af;">
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
        // Filter by role
        function filterRole(role) {
            const rows = document.querySelectorAll('.user-row');
            const tabs = document.querySelectorAll('.filter-tab');
            
            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
            
            rows.forEach(row => {
                const rowRole = row.dataset.role;
                if (role === 'all' || rowRole === role) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Quick role change
        function quickChangeRole(userId, newRole) {
            Swal.fire({
                title: 'Ubah Role User?',
                text: `Ubah role menjadi ${newRole.toUpperCase()}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Changing role for user:', userId, 'to:', newRole);
                    
                    fetch(`/users/${userId}/change-role`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ role_name: newRole })
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success')
                                .then(() => {
                                    console.log('Reloading page...');
                                    location.reload();
                                });
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan saat mengubah role', 'error');
                    });
                }
            });
        }

        // Delete confirmation
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
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        // Bulk actions
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBulkActions();
        }

        function updateBulkActions() {
            const checked = document.querySelectorAll('.user-checkbox:checked').length;
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = checked;
            bulkActions.classList.toggle('show', checked > 0);
        }

        function deselectAll() {
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBulkActions();
        }

        function bulkChangeRole() {
            const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            
            Swal.fire({
                title: 'Ubah Role',
                input: 'select',
                inputOptions: {
                    'admin': 'Admin',
                    'dosen': 'Dosen',
                    'mahasiswa': 'Mahasiswa'
                },
                inputPlaceholder: 'Pilih role baru',
                showCancelButton: true,
                confirmButtonText: 'Ubah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("users.bulk-change-role") }}';
                    form.innerHTML = `
                        @csrf
                        <input type="hidden" name="role" value="${result.value}">
                        ${selected.map(id => `<input type="hidden" name="user_ids[]" value="${id}">`).join('')}
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function bulkDelete() {
            const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            
            Swal.fire({
                title: 'Hapus User?',
                text: `Hapus ${selected.length} user yang dipilih?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("users.bulk-delete") }}';
                    form.innerHTML = `
                        @csrf
                        ${selected.map(id => `<input type="hidden" name="user_ids[]" value="${id}">`).join('')}
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>