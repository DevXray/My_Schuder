<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/dashboard.css', 'resources/css/pages.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Header -->
    @include('partials.header')
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Page Header -->
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-calendar-alt"></i> Jadwal Kelas</h1>
                    <p>Kelola dan pantau jadwal pembelajaran Anda</p>
                </div>
                <div class="header-actions">
                    <button class="btn-secondary" id="exportBtn">
                        <i class="fas fa-download"></i>
                        Export Jadwal
                    </button>
                    <button class="btn-primary" id="addScheduleBtn">
                        <i class="fas fa-plus"></i>
                        Tambah Jadwal
                    </button>
                </div>
            </div>
        </section>

        <!-- Calendar Navigation -->
        <section class="calendar-nav">
            <div class="calendar-controls">
                <button class="nav-btn" id="prevWeek">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 id="currentWeek">12 - 18 Desember 2025</h2>
                <button class="nav-btn" id="nextWeek">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="view-switcher">
                <button class="view-btn active" data-view="week">
                    <i class="fas fa-calendar-week"></i> Minggu
                </button>
                <button class="view-btn" data-view="day">
                    <i class="fas fa-calendar-day"></i> Hari
                </button>
                <button class="view-btn" data-view="month">
                    <i class="fas fa-calendar"></i> Bulan
                </button>
            </div>
        </section>

        <!-- Quick Stats -->
        <section class="quick-stats">
            <div class="stat-item blue">
                <i class="fas fa-calendar-check"></i>
                <div>
                    <span class="stat-number">3</span>
                    <span class="stat-label">Kelas Hari Ini</span>
                </div>
            </div>
            <div class="stat-item orange">
                <i class="fas fa-clock"></i>
                <div>
                    <span class="stat-number">8</span>
                    <span class="stat-label">Jam Per Minggu</span>
                </div>
            </div>
            <div class="stat-item green">
                <i class="fas fa-chalkboard-teacher"></i>
                <div>
                    <span class="stat-number">12</span>
                    <span class="stat-label">Total Mata Kuliah</span>
                </div>
            </div>
        </section>

        <!-- Weekly Schedule -->
        <section class="schedule-view" id="scheduleView">
            <div class="schedule-table">
                <!-- Time Column -->
                <div class="time-column">
                    <div class="time-header">Waktu</div>
                    <div class="time-slot">08:00</div>
                    <div class="time-slot">09:00</div>
                    <div class="time-slot">10:00</div>
                    <div class="time-slot">11:00</div>
                    <div class="time-slot">12:00</div>
                    <div class="time-slot">13:00</div>
                    <div class="time-slot">14:00</div>
                    <div class="time-slot">15:00</div>
                    <div class="time-slot">16:00</div>
                </div>

                <!-- Monday -->
                <div class="day-column">
                    <div class="day-header">
                        <span class="day-name">Senin</span>
                        <span class="day-date">15 Des</span>
                    </div>
                    <div class="schedule-slots">
                        <div class="class-item blue" style="grid-row: 2 / 4;" data-time="09:00-11:00">
                            <div class="class-time">09:00 - 11:00</div>
                            <div class="class-name">Matematika Lanjut</div>
                            <div class="class-room"><i class="fas fa-map-marker-alt"></i> Ruang A101</div>
                            <div class="class-lecturer">Prof. Dr. Ahmad</div>
                        </div>
                        <div class="class-item orange" style="grid-row: 6 / 8;" data-time="13:00-15:00">
                            <div class="class-time">13:00 - 15:00</div>
                            <div class="class-name">Pemrograman Web</div>
                            <div class="class-room"><i class="fas fa-map-marker-alt"></i> Lab Komputer 2</div>
                            <div class="class-lecturer">Dr. Budi Santoso</div>
                        </div>
                    </div>
                </div>

                <!-- Tuesday -->
                <div class="day-column">
                    <div class="day-header">
                        <span class="day-name">Selasa</span>
                        <span class="day-date">16 Des</span>
                    </div>
                    <div class="schedule-slots">
                        <div class="class-item blue" style="grid-row: 3 / 5;" data-time="10:00-12:00">
                            <div class="class-time">10:00 - 12:00</div>
                            <div class="class-name">Basis Data</div>
                            <div class="class-room"><i class="fas fa-map-marker-alt"></i> Ruang B202</div>
                            <div class="class-lecturer">Dr. Citra Dewi</div>
                        </div>
                    </div>
                </div>

                <!-- Wednesday -->
                <div class="day-column">
                    <div class="day-header today">
                        <span class="day-name">Rabu</span>
                        <span class="day-date">17 Des</span>
                        <span class="today-badge">Hari Ini</span>
                    </div>
                    <div class="schedule-slots">
                        <div class="class-item orange" style="grid-row: 2 / 4;" data-time="09:00-11:00">
                            <div class="class-time">09:00 - 11:00</div>
                            <div class="class-name">Algoritma & Struktur Data</div>
                            <div class="class-room"><i class="fas fa-map-marker-alt"></i> Ruang C301</div>
                            <div class="class-lecturer">Dr. Dedi Firmansyah</div>
                        </div>
                        <div class="class-item blue" style="grid-row: 7 / 9;" data-time="14:00-16:00">
                            <div class="class-time">14:00 - 16:00</div>
                            <div class="class-name">Jaringan Komputer</div>
                            <div class="class-room"><i class="fas fa-map-marker-alt"></i> Lab Jaringan</div>
                            <div class="class-lecturer">Dr. Eko Prasetyo</div>
                        </div>
                    </div>
                </div>

                <!-- Thursday -->
                <div class="day-column">
                    <div class="day-header">
                        <span class="day-name">Kamis</span>
                        <span class="day-date">18 Des</span>
                    </div>
                    <div class="schedule-slots">
                        <div class="class-item orange" style="grid-row: 4 / 6;" data-time="11:00-13:00">
                            <div class="class-time">11:00 - 13:00</div>
                            <div class="class-name">Sistem Operasi</div>
                            <div class="class-room"><i class="fas fa-map-marker-alt"></i> Lab Komputer 1</div>
                            <div class="class-lecturer">Dr. Fahmi Rahman</div>
                        </div>
                    </div>
                </div>

                <!-- Friday -->
                <div class="day-column">
                    <div class="day-header">
                        <span class="day-name">Jumat</span>
                        <span class="day-date">19 Des</span>
                    </div>
                    <div class="schedule-slots">
                        <div class="class-item blue" style="grid-row: 2 / 4;" data-time="09:00-11:00">
                            <div class="class-time">09:00 - 11:00</div>
                            <div class="class-name">Keamanan Sistem</div>
                            <div class="class-room"><i class="fas fa-map-marker-alt"></i> Ruang D401</div>
                            <div class="class-lecturer">Dr. Gita Sari</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Upcoming Classes -->
        <section class="upcoming-section">
            <div class="section-header">
                <h3><i class="fas fa-clock"></i> Kelas Selanjutnya</h3>
                <button class="view-all-btn">Lihat Semua <i class="fas fa-arrow-right"></i></button>
            </div>
            <div class="upcoming-grid">
                <div class="upcoming-card active">
                    <div class="card-status">Sedang Berlangsung</div>
                    <div class="card-content">
                        <h4>Algoritma & Struktur Data</h4>
                        <div class="card-info">
                            <span><i class="fas fa-clock"></i> 09:00 - 11:00</span>
                            <span><i class="fas fa-map-marker-alt"></i> Ruang C301</span>
                        </div>
                        <div class="card-lecturer">
                            <i class="fas fa-user"></i> Dr. Dedi Firmansyah
                        </div>
                        <div class="card-time-left">
                            <i class="fas fa-hourglass-half"></i> Tersisa 45 menit
                        </div>
                    </div>
                    <button class="btn-join active"><i class="fas fa-video"></i> Join Now</button>
                </div>

                <div class="upcoming-card">
                    <div class="card-content">
                        <h4>Jaringan Komputer</h4>
                        <div class="card-info">
                            <span><i class="fas fa-clock"></i> 14:00 - 16:00</span>
                            <span><i class="fas fa-map-marker-alt"></i> Lab Jaringan</span>
                        </div>
                        <div class="card-lecturer">
                            <i class="fas fa-user"></i> Dr. Eko Prasetyo
                        </div>
                        <div class="card-countdown">
                            <i class="fas fa-clock"></i> Dimulai dalam 3 jam
                        </div>
                    </div>
                    <button class="btn-join"><i class="far fa-calendar-plus"></i> Set Reminder</button>
                </div>

                <div class="upcoming-card">
                    <div class="card-content">
                        <h4>Sistem Operasi</h4>
                        <div class="card-info">
                            <span><i class="fas fa-clock"></i> 11:00 - 13:00 (Besok)</span>
                            <span><i class="fas fa-map-marker-alt"></i> Lab Komputer 1</span>
                        </div>
                        <div class="card-lecturer">
                            <i class="fas fa-user"></i> Dr. Fahmi Rahman
                        </div>
                    </div>
                    <button class="btn-join"><i class="fas fa-info-circle"></i> Detail</button>
                </div>
            </div>
        </section>
    </main>

    <!--chatbot-->
    @include('partials.chatbot')

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>
    <!-- Loading Screen -->
    @include('partials.loadingscreen')
</body>
</html>