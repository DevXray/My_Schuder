{{-- resources/views/dashboard/dosen.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dosen Dashboard - My Schuder</title>
    
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
                    <h1><i class="fas fa-chalkboard-teacher"></i> Dashboard Dosen</h1>
                    <p>Selamat datang, {{ Auth::user()->name }}! Kelola pembelajaran Anda</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.location='{{ route('materi.create') }}'">
                        <i class="fas fa-plus"></i> Tambah Materi
                    </button>
                    <button class="btn btn-primary" onclick="window.location='{{ route('tugas.create') }}'">
                        <i class="fas fa-plus"></i> Tambah Tugas
                    </button>
                </div>
            </div>
        </section>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        <!-- Stats Grid -->
        <section class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-content">
                    <h3>Materi Saya</h3>
                    <p class="stat-number">{{ $stats['total_materi'] ?? 0 }}</p>
                    <span class="stat-change">
                        <i class="fas fa-arrow-up"></i> Materi Aktif
                    </span>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-content">
                    <h3>Tugas Saya</h3>
                    <p class="stat-number">{{ $stats['total_tugas'] ?? 0 }}</p>
                    <span class="stat-change">
                        <i class="fas fa-tasks"></i> Tugas Aktif
                    </span>
                </div>
            </div>

            <div class="stat-card red">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-content">
                    <h3>Belum Dinilai</h3>
                    <p class="stat-number">{{ $stats['tugas_belum_dinilai'] ?? 0 }}</p>
                    <span class="stat-change">
                        <i class="fas fa-exclamation-circle"></i> Perlu Tindakan
                    </span>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-content">
                    <h3>Total Mahasiswa</h3>
                    <p class="stat-number">{{ $stats['total_mahasiswa'] ?? 0 }}</p>
                    <span class="stat-change">
                        <i class="fas fa-users"></i> Peserta Aktif
                    </span>
                </div>
            </div>
        </section>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Materi Terbaru -->
            <section class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-book-open"></i> Materi Terbaru Saya</h2>
                    <a href="{{ route('materi.index') }}" class="btn btn-link">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Judul Materi</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMateri ?? [] as $materi)
                            <tr>
                                <td>
                                    <div class="table-cell-content">
                                        <i class="fas fa-file-alt"></i>
                                        <span>{{ $materi->judul }}</span>
                                    </div>
                                </td>
                                <td><span class="badge badge-info">{{ $materi->kategori ?? 'Umum' }}</span></td>
                                <td>{{ $materi->created_at->format('d M Y') }}</td>
                                <td><span class="badge badge-success">Aktif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-sm btn-info" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('materi.edit', $materi->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>Belum ada materi. <a href="{{ route('materi.create') }}">Tambah sekarang</a></p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Tugas Perlu Penilaian -->
            <section class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-clipboard-check"></i> Tugas Perlu Dinilai</h2>
                    <a href="{{ route('tugas.index') }}" class="btn btn-link">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Tugas</th>
                                <th>Dikumpulkan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengumpulanBelumDinilai ?? [] as $pengumpulan)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ substr($pengumpulan->mahasiswa->user->name, 0, 1) }}</div>
                                        <span>{{ $pengumpulan->mahasiswa->user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $pengumpulan->tugas->judul }}</td>
                                <td>{{ $pengumpulan->created_at->diffForHumans() }}</td>
                                <td><span class="badge badge-warning">Menunggu Penilaian</span></td>
                                <td>
                                    <a href="{{ route('pengumpulan.show', $pengumpulan->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-check"></i> Nilai
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-check-circle"></i>
                                        <p>Semua tugas sudah dinilai! 🎉</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Quick Actions -->
        <section class="quick-actions">
            <h2><i class="fas fa-bolt"></i> Aksi Cepat</h2>
            <div class="actions-grid">
                <a href="{{ route('materi.create') }}" class="action-card">
                    <i class="fas fa-plus-circle"></i>
                    <h3>Tambah Materi</h3>
                    <p>Upload materi pembelajaran baru</p>
                </a>
                <a href="{{ route('tugas.create') }}" class="action-card">
                    <i class="fas fa-tasks"></i>
                    <h3>Buat Tugas</h3>
                    <p>Buat tugas atau kuis baru</p>
                </a>
                <a href="{{ route('jadwal.index') }}" class="action-card">
                    <i class="fas fa-calendar"></i>
                    <h3>Lihat Jadwal</h3>
                    <p>Cek jadwal mengajar Anda</p>
                </a>
                <a href="{{ route('users.index') }}" class="action-card">
                    <i class="fas fa-users"></i>
                    <h3>Lihat Mahasiswa</h3>
                    <p>Kelola data mahasiswa</p>
                </a>
            </div>
        </section>
    </main>

    @include('partials.chatbot')
    @include('partials.loadingscreen')
</body>
</html>
