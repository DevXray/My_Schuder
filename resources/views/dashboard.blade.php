<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelas Online - Portal Pembelajaran Modern</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/dashboard.css', 'resources/js/script.js'])

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
                <h2>Selamat Datang </h2>
                <p>Reminder tugas</p>
            </div>
            <div class="quick-actions">
                <button class="action-btn primary">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Materi</span>
                </button>
                <button class="action-btn secondary">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Buat Jadwal</span>
                </button>
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
                    <p class="stat-number" data-target="24">0</p>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 3 materi baru minggu ini
                    </span>
                </div>
            </div>

            <div class="stat-card orange" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Tugas Aktif</h3>
                    <p class="stat-number" data-target="5">0</p>
                    <span class="stat-change warning">
                        <i class="fas fa-clock"></i> 2 deadline minggu ini
                    </span>
                </div>
            </div>

            <div class="stat-card blue" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Peserta Kelas</h3>
                    <p class="stat-number" data-target="32">0</p>
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
                    <p class="stat-number" data-target="3">0</p>
                    <span class="stat-change">
                        <i class="fas fa-clock"></i> Mulai 09:00 WIB
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
                    <button class="view-all-btn">
                        Lihat Semua
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="update-item announcement">
                        <div class="update-indicator"></div>
                        <div class="update-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="update-content">
                            <h4>Pengumuman: Ujian Tengah Semester</h4>
                            <p>Ujian akan dilaksanakan minggu depan. Pastikan untuk mempersiapkan diri dengan baik.</p>
                            <span class="update-time">
                                <i class="far fa-clock"></i> 2 jam yang lalu
                            </span>
                        </div>
                    </div>

                    <div class="update-item material">
                        <div class="update-indicator"></div>
                        <div class="update-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="update-content">
                            <h4>Materi Baru: Algoritma & Struktur Data</h4>
                            <p>Materi pembelajaran baru telah ditambahkan ke kelas.</p>
                            <span class="update-time">
                                <i class="far fa-clock"></i> 5 jam yang lalu
                            </span>
                        </div>
                    </div>

                    <div class="update-item assignment">
                        <div class="update-indicator"></div>
                        <div class="update-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="update-content">
                            <h4>Tugas: Project Akhir Semester</h4>
                            <p>Deadline pengumpulan project adalah 15 Desember 2025.</p>
                            <span class="update-time">
                                <i class="far fa-clock"></i> 1 hari yang lalu
                            </span>
                        </div>
                    </div>

                    <div class="update-item discussion">
                        <div class="update-indicator"></div>
                        <div class="update-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="update-content">
                            <h4>Diskusi: Persiapan Presentasi Kelompok</h4>
                            <p>Forum diskusi untuk koordinasi presentasi kelompok.</p>
                            <span class="update-time">
                                <i class="far fa-clock"></i> 2 hari yang lalu
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Upcoming Schedule -->
            <section class="card schedule-card" data-aos="fade-left">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-calendar-day"></i>
                        Jadwal Hari Ini
                    </h3>
                    <button class="view-all-btn">
                        <i class="fas fa-calendar-week"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="schedule-item">
                        <div class="schedule-time blue">
                            <i class="far fa-clock"></i>
                            <span>09:00</span>
                        </div>
                        <div class="schedule-content">
                            <h4>Matematika Lanjut</h4>
                            <p>Kalkulus Integral</p>
                            <span class="schedule-room">
                                <i class="fas fa-map-marker-alt"></i>
                                Ruang A101
                            </span>
                        </div>
                        <button class="schedule-join blue">Join</button>
                    </div>

                    <div class="schedule-item">
                        <div class="schedule-time orange">
                            <i class="far fa-clock"></i>
                            <span>11:00</span>
                        </div>
                        <div class="schedule-content">
                            <h4>Pemrograman Web</h4>
                            <p>JavaScript ES6+</p>
                            <span class="schedule-room">
                                <i class="fas fa-map-marker-alt"></i>
                                Lab Komputer 2
                            </span>
                        </div>
                        <button class="schedule-join orange">Join</button>
                    </div>

                    <div class="schedule-item">
                        <div class="schedule-time blue">
                            <i class="far fa-clock"></i>
                            <span>14:00</span>
                        </div>
                        <div class="schedule-content">
                            <h4>Basis Data</h4>
                            <p>Database Design</p>
                            <span class="schedule-room">
                                <i class="fas fa-map-marker-alt"></i>
                                Ruang B202
                            </span>
                        </div>
                        <button class="schedule-join blue">Join</button>
                    </div>
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
                    <button class="filter-btn active">Semua</button>
                    <button class="filter-btn">Semester Ini</button>
                    <button class="filter-btn">Bulan Ini</button>
                </div>
            </div>
            <div class="card-body">
                <div class="progress-grid">
                    <div class="progress-item">
                        <div class="progress-header">
                            <div>
                                <span class="progress-title">Algoritma & Struktur Data</span>
                                <span class="progress-subtitle">24 dari 32 materi selesai</span>
                            </div>
                            <span class="progress-percentage">75%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill blue" data-progress="75"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <div>
                                <span class="progress-title">Pemrograman Web</span>
                                <span class="progress-subtitle">27 dari 30 materi selesai</span>
                            </div>
                            <span class="progress-percentage">90%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill orange" data-progress="90"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <div>
                                <span class="progress-title">Basis Data</span>
                                <span class="progress-subtitle">18 dari 30 materi selesai</span>
                            </div>
                            <span class="progress-percentage">60%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill blue" data-progress="60"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <div>
                                <span class="progress-title">Matematika Lanjut</span>
                                <span class="progress-subtitle">17 dari 20 materi selesai</span>
                            </div>
                            <span class="progress-percentage">85%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill orange" data-progress="85"></div>
                        </div>
                    </div>
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