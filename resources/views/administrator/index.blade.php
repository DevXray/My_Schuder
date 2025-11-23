{{-- resources/views/administrator/index.blade.php (ENHANCED) --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administrator - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/js/app.js'])
</head>
<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
        <!-- Page Header -->
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-user-shield"></i> Administrator Dashboard</h1>
                    <p>Kelola semua aspek sistem pembelajaran</p>
                </div>
            </div>
        </section>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
        @endif

        <!-- Stats Grid -->
        <section class="stats-grid">
            <div class="stat-card red">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <h3>Total Users</h3>
                    <p class="stat-number">{{ $stats['total_users'] }}</p>
                    <span class="stat-change">
                        <i class="fas fa-users-cog"></i> Semua Pengguna
                    </span>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                <div class="stat-content">
                    <h3>Admin</h3>
                    <p class="stat-number">{{ $stats['total_admin'] }}</p>
                    <span class="stat-change">
                        <i class="fas fa-shield-alt"></i> Super Users
                    </span>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-content">
                    <h3>Dosen</h3>
                    <p class="stat-number">{{ $stats['total_dosen'] }}</p>
                    <span class="stat-change">
                        <i class="fas fa-user-tie"></i> Pengajar
                    </span>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-content">
                    <h3>Mahasiswa</h3>
                    <p class="stat-number">{{ $stats['total_mahasiswa'] }}</p>
                    <span class="stat-change">
                        <i class="fas fa-users"></i> Peserta Didik
                    </span>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: #1f2937;">
                <i class="fas fa-bolt"></i> Quick Actions
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                
                <!-- Kelola Semua User -->
                <div class="info-card" style="border-left: 4px solid #ef4444;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 50px; height: 50px; background: #fee2e2; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users-cog" style="font-size: 1.5rem; color: #ef4444;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem;">Kelola Semua User</h3>
                            <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">CRUD user & ubah role</p>
                        </div>
                    </div>
                    <a href="{{ route('administrator.users.index') }}" class="btn-action primary" style="width: 100%; text-decoration: none;">
                        <i class="fas fa-arrow-right"></i> Kelola User
                    </a>
                </div>

                <!-- Kelola Mahasiswa -->
                <div class="info-card" style="border-left: 4px solid #10b981;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 50px; height: 50px; background: #d1fae5; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-graduate" style="font-size: 1.5rem; color: #10b981;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem;">Kelola Mahasiswa</h3>
                            <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">Khusus data mahasiswa</p>
                        </div>
                    </div>
                    <a href="{{ route('administrator.mahasiswa.index') }}" class="btn-action primary" style="width: 100%; text-decoration: none;">
                        <i class="fas fa-arrow-right"></i> Kelola Mahasiswa
                    </a>
                </div>

                <!-- Kelola Dosen -->
                <div class="info-card" style="border-left: 4px solid #f59e0b;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 50px; height: 50px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chalkboard-teacher" style="font-size: 1.5rem; color: #f59e0b;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem;">Kelola Dosen</h3>
                            <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">Khusus data dosen</p>
                        </div>
                    </div>
                    <a href="{{ route('administrator.dosen.index') }}" class="btn-action primary" style="width: 100%; text-decoration: none; background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-arrow-right"></i> Kelola Dosen
                    </a>
                </div>

            </div>
        </section>

        <!-- Recent Users Table -->
        <section class="card">
            <div class="card-header">
                <h3><i class="fas fa-clock"></i> User Terbaru</h3>
                <a href="{{ route('administrator.users.index') }}" class="view-all-btn">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">Nama</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">Email</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">Role</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 0.75rem; font-weight: 600;">{{ $user->name }}</td>
                            <td style="padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">{{ $user->email }}</td>
                            <td style="padding: 0.75rem;">
                                <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
                                    {{ $user->role === 'admin' ? 'background: #fee2e2; color: #991b1b;' : '' }}
                                    {{ $user->role === 'dosen' ? 'background: #dbeafe; color: #1e40af;' : '' }}
                                    {{ $user->role === 'mahasiswa' ? 'background: #d1fae5; color: #065f46;' : '' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">
                                {{ $user->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 2rem; text-align: center; color: #9ca3af;">
                                Belum ada data user
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>
    @include('partials.loadingscreen')
</body>
</html>