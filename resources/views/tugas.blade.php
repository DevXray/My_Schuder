<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tugas - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/tugas.css', 'resources/css/materi.css','resources/js/tugas.js'])
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="header-left">
                <button class="menu-btn" id="menuBtn" aria-label="Toggle Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">
                    <img class="logo-icon" src="{{ Vite::asset('resources/assets/logo_akademik_hd.png') }}" alt="Logo My Schuder" />
                    <div class="logo-text">
                        <h1>My Schuder</h1>
                        <p>Portal Pembelajaran</p>
                    </div>
                </div>
            </div>

            <div class="header-center">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari tugas...">
                    <button class="search-clear" id="searchClear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="header-right">
                <button class="notification-btn" id="notificationBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </button>
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">A</div>
                    <div class="user-info">
                        <p class="user-name">Ahmad Student</p>
                        <p class="user-role">Siswa</p>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Page Header -->
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-clipboard-list"></i> Tugas & Penilaian</h1>
                    <p>Kelola dan kumpulkan tugas Anda tepat waktu</p>
                </div>
                <button class="btn-primary" id="uploadTugasBtn">
                    <i class="fas fa-upload"></i>
                    Upload Tugas
                </button>
            </div>
        </section>

        <!-- Filter Tabs -->
        <section class="filter-section">
            <div class="filter-tabs">
                <button class="filter-tab active" data-status="all">
                    <i class="fas fa-th"></i> Semua <span class="tab-count">12</span>
                </button>
                <button class="filter-tab" data-status="pending">
                    <i class="fas fa-clock"></i> Pending <span class="tab-count warning">5</span>
                </button>
                <button class="filter-tab" data-status="submitted">
                    <i class="fas fa-check"></i> Dikumpulkan <span class="tab-count success">4</span>
                </button>
                <button class="filter-tab" data-status="graded">
                    <i class="fas fa-star"></i> Dinilai <span class="tab-count blue">3</span>
                </button>
            </div>
        </section>

        <!-- Stats Cards -->
        <section class="stats-grid">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-info">
                    <h3>Deadline Dekat</h3>
                    <p class="stat-value">2</p>
                    <span class="stat-desc">Dalam 3 hari</span>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <h3>Belum Dikumpulkan</h3>
                    <p class="stat-value">5</p>
                    <span class="stat-desc">Harus segera diselesaikan</span>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>Sudah Dikumpulkan</h3>
                    <p class="stat-value">4</p>
                    <span class="stat-desc">Menunggu penilaian</span>
                </div>
            </div>
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-info">
                    <h3>Nilai Rata-rata</h3>
                    <p class="stat-value">87</p>
                    <span class="stat-desc">Sangat baik!</span>
                </div>
            </div>
        </section>

        <!-- Tugas List -->
        <section class="tugas-container" id="tugasContainer">
            <!-- Tugas Item 1 - Deadline Dekat -->
            <div class="tugas-item" data-status="pending" data-priority="high">
                <div class="tugas-priority high">
                    <i class="fas fa-exclamation-circle"></i>
                    Deadline Dekat!
                </div>
                <div class="tugas-header">
                    <div class="tugas-info">
                        <h3>Project Akhir Semester - Aplikasi Web</h3>
                        <p class="tugas-subject"><i class="fas fa-book"></i> Pemrograman Web</p>
                    </div>
                    <div class="tugas-status pending">
                        <i class="fas fa-clock"></i> Belum Dikumpulkan
                    </div>
                </div>
                <div class="tugas-body">
                    <p class="tugas-description">
                        Buat aplikasi web fullstack menggunakan framework modern (React/Vue + Laravel/Node.js). 
                        Aplikasi harus memiliki fitur CRUD, authentication, dan responsive design.
                    </p>
                    <div class="tugas-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Diberikan: 1 Des 2025</span>
                        </div>
                        <div class="meta-item deadline">
                            <i class="fas fa-clock"></i>
                            <span>Deadline: 15 Des 2025 (3 hari lagi)</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-weight-hanging"></i>
                            <span>Bobot: 30%</span>
                        </div>
                    </div>
                </div>
                <div class="tugas-footer">
                    <button class="btn-action primary">
                        <i class="fas fa-upload"></i> Kumpulkan Tugas
                    </button>
                    <button class="btn-action secondary">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </button>
                    <button class="btn-action tertiary">
                        <i class="fas fa-download"></i> Unduh Soal
                    </button>
                </div>
            </div>

            <!-- Tugas Item 2 - Pending -->
            <div class="tugas-item" data-status="pending" data-priority="medium">
                <div class="tugas-header">
                    <div class="tugas-info">
                        <h3>Analisis Kompleksitas Algoritma</h3>
                        <p class="tugas-subject"><i class="fas fa-book"></i> Algoritma & Struktur Data</p>
                    </div>
                    <div class="tugas-status pending">
                        <i class="fas fa-clock"></i> Belum Dikumpulkan
                    </div>
                </div>
                <div class="tugas-body">
                    <p class="tugas-description">
                        Analisis kompleksitas waktu dan ruang untuk berbagai algoritma sorting dan searching. 
                        Sertakan grafik perbandingan dan kesimpulan.
                    </p>
                    <div class="tugas-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Diberikan: 5 Des 2025</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Deadline: 18 Des 2025 (6 hari lagi)</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-weight-hanging"></i>
                            <span>Bobot: 20%</span>
                        </div>
                    </div>
                </div>
                <div class="tugas-footer">
                    <button class="btn-action primary">
                        <i class="fas fa-upload"></i> Kumpulkan Tugas
                    </button>
                    <button class="btn-action secondary">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </button>
                    <button class="btn-action tertiary">
                        <i class="fas fa-download"></i> Unduh Soal
                    </button>
                </div>
            </div>

            <!-- Tugas Item 3 - Submitted -->
            <div class="tugas-item" data-status="submitted" data-priority="low">
                <div class="tugas-header">
                    <div class="tugas-info">
                        <h3>Database Design - E-Commerce</h3>
                        <p class="tugas-subject"><i class="fas fa-book"></i> Basis Data</p>
                    </div>
                    <div class="tugas-status submitted">
                        <i class="fas fa-check"></i> Sudah Dikumpulkan
                    </div>
                </div>
                <div class="tugas-body">
                    <p class="tugas-description">
                        Rancang database untuk sistem e-commerce lengkap dengan ERD, normalisasi, dan query optimization.
                    </p>
                    <div class="tugas-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Dikumpulkan: 8 Des 2025, 14:30</span>
                        </div>
                        <div class="meta-item success">
                            <i class="fas fa-check-circle"></i>
                            <span>Tepat Waktu</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-file"></i>
                            <span>database-design.pdf (2.5 MB)</span>
                        </div>
                    </div>
                </div>
                <div class="tugas-footer">
                    <button class="btn-action secondary">
                        <i class="fas fa-eye"></i> Lihat Pengumpulan
                    </button>
                    <button class="btn-action tertiary">
                        <i class="fas fa-edit"></i> Edit Pengumpulan
                    </button>
                </div>
            </div>

            <!-- Tugas Item 4 - Graded -->
            <div class="tugas-item" data-status="graded" data-priority="low">
                <div class="tugas-grade excellent">
                    <i class="fas fa-trophy"></i>
                    <span class="grade-value">92</span>
                    <span class="grade-label">/ 100</span>
                </div>
                <div class="tugas-header">
                    <div class="tugas-info">
                        <h3>Implementasi RESTful API</h3>
                        <p class="tugas-subject"><i class="fas fa-book"></i> Pemrograman Web</p>
                    </div>
                    <div class="tugas-status graded">
                        <i class="fas fa-star"></i> Sudah Dinilai
                    </div>
                </div>
                <div class="tugas-body">
                    <p class="tugas-description">
                        Implementasi RESTful API dengan authentication, validation, dan dokumentasi lengkap.
                    </p>
                    <div class="feedback-box">
                        <h4><i class="fas fa-comment-alt"></i> Feedback Dosen:</h4>
                        <p>"Pekerjaan yang sangat baik! API sudah terstruktur dengan baik, dokumentasi lengkap, dan error handling yang tepat. Pertahankan!"</p>
                    </div>
                    <div class="tugas-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Dinilai: 10 Des 2025</span>
                        </div>
                        <div class="meta-item success">
                            <i class="fas fa-award"></i>
                            <span>Grade: A</span>
                        </div>
                    </div>
                </div>
                <div class="tugas-footer">
                    <button class="btn-action secondary">
                        <i class="fas fa-eye"></i> Lihat Detail Nilai
                    </button>
                    <button class="btn-action tertiary">
                        <i class="fas fa-download"></i> Unduh Feedback
                    </button>
                </div>
            </div>

            <!-- Tugas Item 5 - Graded (Lower Score) -->
            <div class="tugas-item" data-status="graded" data-priority="low">
                <div class="tugas-grade good">
                    <i class="fas fa-star"></i>
                    <span class="grade-value">78</span>
                    <span class="grade-label">/ 100</span>
                </div>
                <div class="tugas-header">
                    <div class="tugas-info">
                        <h3>Laporan Praktikum Jaringan</h3>
                        <p class="tugas-subject"><i class="fas fa-book"></i> Jaringan Komputer</p>
                    </div>
                    <div class="tugas-status graded">
                        <i class="fas fa-star"></i> Sudah Dinilai
                    </div>
                </div>
                <div class="tugas-body">
                    <p class="tugas-description">
                        Laporan lengkap hasil praktikum konfigurasi router dan switching.
                    </p>
                    <div class="feedback-box warning">
                        <h4><i class="fas fa-comment-alt"></i> Feedback Dosen:</h4>
                        <p>"Laporan sudah cukup baik, namun perlu penjelasan lebih detail pada bagian troubleshooting. Tambahkan juga analisis hasil percobaan."</p>
                    </div>
                    <div class="tugas-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Dinilai: 9 Des 2025</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-award"></i>
                            <span>Grade: B+</span>
                        </div>
                    </div>
                </div>
                <div class="tugas-footer">
                    <button class="btn-action secondary">
                        <i class="fas fa-eye"></i> Lihat Detail Nilai
                    </button>
                    <button class="btn-action tertiary">
                        <i class="fas fa-redo"></i> Ajukan Revisi
                    </button>
                </div>
            </div>
        </section>
    </main>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>
</body>
</html>