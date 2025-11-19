<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Portal Pembelajaran Modern</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/dashboard.css', 'resources/js/app.js'])

</head>
<body>
    <!-- Header -->
    @include('partials.header')

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="welcome-content">
                <h2>Selamat Datang, {{ Auth::user()->name ?? 'Mahasiswa' }}</h2>
                <p>
                    @if($deadlineDekat > 0)
                        <i class="fas fa-exclamation-circle"></i>
                        Kamu punya {{ $deadlineDekat }} tugas dengan deadline dekat!
                    @else
                        Semua tugas dalam kendali. Tetap semangat belajar! 🎓
                    @endif
                </p>
            </div>
        </section>

        <!-- Stats Cards -->
        <section class="stats-grid">
            <div class="stat-card blue" data-aos="fade-up" data-aos-delay="0">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Materi</h3>
                    <p class="stat-number" data-target="{{ $totalMateri }}">0</p>
                    <span class="stat-change positive">
                        <i class="fas fa-check-circle"></i> 
                        {{ $materiSelesai }} selesai
                    </span>
                </div>
            </div>

            <div class="stat-card orange" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Tugas Aktif</h3>
                    <p class="stat-number" data-target="{{ $tugasAktif }}">0</p>
                    <span class="stat-change {{ $deadlineDekat > 0 ? 'warning' : '' }}">
                        <i class="fas fa-clock"></i> 
                        {{ $deadlineDekat }} deadline dekat
                    </span>
                </div>
            </div>

            <div class="stat-card blue" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Peserta Kelas</h3>
                    <p class="stat-number" data-target="{{ $pesertaKelas }}">0</p>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 2 peserta baru
                    </span>
                </div>
            </div>

            <div class="stat-card orange" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-content">
                    <h3>Jadwal Hari Ini</h3>
                    <p class="stat-number" data-target="{{ $jadwalHariIni }}">0</p>
                    <span class="stat-change">
                        <i class="fas fa-clock"></i> 
                        @if($jadwalList->isNotEmpty())
                            Mulai {{ \Carbon\Carbon::parse($jadwalList->first()->jam_mulai)->format('H:i') }} WIB
                        @else
                            Tidak ada kelas
                        @endif
                    </span>
                </div>
            </div>
        </section>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Updates -->
            <section class="card recent-updates" data-aos="fade-right">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-bell"></i>
                        Informasi Terbaru
                    </h3>
                    <a href="{{ route('tugas.index') }}" class="view-all-btn">
                        Lihat Semua
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    @forelse($informasiTerbaru as $info)
                        <div class="update-item {{ $info['type'] }}">
                            <div class="update-indicator"></div>
                            <div class="update-icon">
                                <i class="fas {{ $info['icon'] }}"></i>
                            </div>
                            <div class="update-content">
                                <h4>{{ $info['title'] }}</h4>
                                <p>{{ $info['description'] }}</p>
                                <span class="update-time">
                                    <i class="far fa-clock"></i> {{ $info['time'] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Belum ada informasi terbaru</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Upcoming Schedule -->
            <section class="card schedule-card" data-aos="fade-left">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-calendar-day"></i>
                        Jadwal Hari Ini ({{ $hariIni }})
                    </h3>
                    <a href="{{ route('jadwal.index') }}" class="view-all-btn">
                        <i class="fas fa-calendar-week"></i>
                    </a>
                </div>
                <div class="card-body">
                    @forelse($jadwalList as $index => $jadwal)
                        <div class="schedule-item">
                            <div class="schedule-time {{ $index % 2 == 0 ? 'blue' : 'orange' }}">
                                <i class="far fa-clock"></i>
                                <span>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                            </div>
                            <div class="schedule-content">
                                <h4>{{ $jadwal->nama_matkul }}</h4>
                                <p>{{ $jadwal->dosen }}</p>
                                <span class="schedule-room">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $jadwal->ruangan }}
                                </span>
                            </div>
                            <button class="schedule-join {{ $index % 2 == 0 ? 'blue' : 'orange' }}">Join</button>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>Tidak ada jadwal hari ini</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- Progress Section -->
        <section class="card progress-section" data-aos="fade-up">
            <div class="card-header">
                <h3>
                    <i class="fas fa-chart-line"></i>
                    Progress Pembelajaran
                </h3>
                <div class="progress-filter">
                    <a href="{{ route('materi.index') }}" class="filter-btn active">Semua Materi</a>
                </div>
            </div>
            <div class="card-body">
                <div class="progress-grid">
                    @forelse($progressMateri as $index => $materi)
                        <div class="progress-item">
                            <div class="progress-header">
                                <div>
                                    <span class="progress-title">{{ $materi['title'] }}</span>
                                    <span class="progress-subtitle">
                                        {{ $materi['completed'] }} dari {{ $materi['total'] }} materi selesai
                                    </span>
                                </div>
                                <span class="progress-percentage">{{ $materi['percentage'] }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill {{ $index % 2 == 0 ? 'blue' : 'orange' }}" 
                                     data-progress="{{ $materi['percentage'] }}"></div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-chart-bar"></i>
                            <p>Belum ada data progress</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <!--chatbot-->
    @include('partials.chatbot')

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Loading Screen -->
    @include('partials.loadingScreen')

</body>
</html>