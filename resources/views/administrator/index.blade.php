{{-- resources/views/administrator/index.blade.php --}}
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
                    <p>Kelola data mahasiswa, dosen, dan mata kuliah</p>
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
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                <div class="stat-content">
                    <h3>Total Admin</h3>
                    <p class="stat-number">{{ $stats['total_admin'] }}</p>
                    <span class="stat-change">
                        <i class="fas fa-shield-alt"></i> Super Users
                    </span>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-content">
                    <h3>Total Dosen</h3>
                    <p class="stat-number">{{ $stats['total_dosen'] }}</p>
                    <span class="stat-change">
                        <i class="fas fa-user-tie"></i> Pengajar
                    </span>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-content">
                    <h3>Total Mahasiswa</h3>
                    <p class="stat-number">{{ $stats['total_mahasiswa'] }}</p>
                    <span class="stat-change">
                        <i class="fas fa-users"></i> Peserta Didik
                    </span>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-content">
                    <h3>Mata Kuliah</h3>
                    <p class="stat-number">{{ $stats['total_matakuliah'] }}</p>
                    <span class="stat-change">
                        <i class="fas fa-graduation-cap"></i> Program Studi
                    </span>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section style="margin-bottom: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                
                <!-- Manage Mahasiswa -->
                <div class="info-card" style="border-left: 4px solid #10b981;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 50px; height: 50px; background: #d1fae5; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-graduate" style="font-size: 1.5rem; color: #10b981;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem;">Kelola Mahasiswa</h3>
                            <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">Tambah, edit, hapus data mahasiswa</p>
                        </div>
                    </div>
                    <a href="{{ route('administrator.mahasiswa.index') }}" class="btn-action primary" style="width: 100%; text-decoration: none;">
                        <i class="fas fa-arrow-right"></i> Kelola Mahasiswa
                    </a>
                </div>

                <!-- Manage Dosen -->
                <div class="info-card" style="border-left: 4px solid #f59e0b;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 50px; height: 50px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chalkboard-teacher" style="font-size: 1.5rem; color: #f59e0b;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem;">Kelola Dosen</h3>
                            <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">Tambah, edit, hapus data dosen</p>
                        </div>
                    </div>
                    <a href="{{ route('administrator.dosen.index') }}" class="btn-action primary" style="width: 100%; text-decoration: none; background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-arrow-right"></i> Kelola Dosen
                    </a>
                </div>

                <!-- Manage Mata Kuliah -->
                <div class="info-card" style="border-left: 4px solid #3b82f6;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 50px; height: 50px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-book-open" style="font-size: 1.5rem; color: #3b82f6;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem;">Kelola Mata Kuliah</h3>
                            <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">Tambah, edit, hapus mata kuliah</p>
                        </div>
                    </div>
                    <a href="{{ route('administrator.matakuliah.index') }}" class="btn-action primary" style="width: 100%; text-decoration: none;">
                        <i class="fas fa-arrow-right"></i> Kelola Mata Kuliah
                    </a>
                </div>

            </div>
        </section>

        <!-- Recent Data Tables -->
        <div class="content-grid">
            <!-- Recent Dosen -->
            <section class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chalkboard-teacher"></i> Dosen Terbaru</h3>
                    <a href="{{ route('administrator.dosen.index') }}" class="view-all-btn">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">NIDN</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">Nama</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDosen as $dosen)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 0.75rem; font-size: 0.875rem;">{{ $dosen->nidn }}</td>
                                <td style="padding: 0.75rem; font-weight: 600;">{{ $dosen->nama }}</td>
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">{{ $dosen->email }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="padding: 2rem; text-align: center; color: #9ca3af;">
                                    Belum ada data dosen
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent Mahasiswa -->
            <section class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-graduate"></i> Mahasiswa Terbaru</h3>
                    <a href="{{ route('administrator.mahasiswa.index') }}" class="view-all-btn">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">NIM</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">Nama</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; color: #6b7280;">Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMahasiswa as $mhs)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 0.75rem; font-size: 0.875rem;">{{ $mhs->nim }}</td>
                                <td style="padding: 0.75rem; font-weight: 600;">{{ $mhs->nama }}</td>
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">{{ $mhs->kelas ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="padding: 2rem; text-align: center; color: #9ca3af;">
                                    Belum ada data mahasiswa
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>
    @include('partials.loadingscreen')
</body>
</html>