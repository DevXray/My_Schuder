<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Materi Kelas - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/materi.css', 'resources/js/materi.js'])
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
                    <input type="text" id="searchInput" placeholder="Cari materi...">
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
                    <h1><i class="fas fa-book-open"></i> Materi Kelas</h1>
                    <p>Akses semua materi pembelajaran Anda di sini</p>
                </div>
                <button class="btn-primary" id="addMateriBtn">
                    <i class="fas fa-plus"></i>
                    Tambah Materi
                </button>
            </div>
        </section>

        <!-- Filter Section -->
        <section class="filter-section">
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">
                    <i class="fas fa-th"></i> Semua
                </button>
                <button class="filter-tab" data-filter="recent">
                    <i class="fas fa-clock"></i> Terbaru
                </button>
                <button class="filter-tab" data-filter="progress">
                    <i class="fas fa-chart-line"></i> Sedang Dipelajari
                </button>
                <button class="filter-tab" data-filter="completed">
                    <i class="fas fa-check-circle"></i> Selesai
                </button>
            </div>
            
            <div class="filter-actions">
                <select class="filter-select" id="categoryFilter">
                    <option value="all">Semua Kategori</option>
                    <option value="pemrograman">Pemrograman</option>
                    <option value="matematika">Matematika</option>
                    <option value="database">Database</option>
                    <option value="jaringan">Jaringan</option>
                </select>
                
                <select class="filter-select" id="sortFilter">
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="name">Nama A-Z</option>
                    <option value="progress">Progress</option>
                </select>
            </div>
        </section>

        <!-- Stats Overview -->
        <section class="stats-overview">
            <div class="stat-box blue">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-info">
                    <h3>Total Materi</h3>
                    <p class="stat-value">24</p>
                </div>
            </div>
            <div class="stat-box orange">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div class="stat-info">
                    <h3>Sedang Dipelajari</h3>
                    <p class="stat-value">8</p>
                </div>
            </div>
            <div class="stat-box green">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>Selesai</h3>
                    <p class="stat-value">16</p>
                </div>
            </div>
        </section>

        <!-- Materi Grid -->
        <section class="materi-grid" id="materiGrid">
            <!-- Card 1 -->
            <div class="materi-card" data-category="pemrograman" data-status="progress">
                <div class="card-ribbon orange">Sedang Dipelajari</div>
                <div class="card-header">
                    <div class="card-icon blue">
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="card-badge">Pemrograman</div>
                </div>
                <div class="card-body">
                    <h3>Algoritma & Struktur Data</h3>
                    <p>Pelajari konsep dasar algoritma, kompleksitas waktu, dan berbagai struktur data fundamental.</p>
                    <div class="card-meta">
                        <span><i class="fas fa-file-alt"></i> 12 Modul</span>
                        <span><i class="fas fa-clock"></i> 8 Jam</span>
                    </div>
                    <div class="card-progress">
                        <div class="progress-bar">
                            <div class="progress-fill blue" style="width: 75%"></div>
                        </div>
                        <span class="progress-text">75% Selesai</span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn-secondary"><i class="fas fa-play"></i> Lanjutkan</button>
                    <button class="btn-icon" title="Detail"><i class="fas fa-info-circle"></i></button>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="materi-card" data-category="pemrograman" data-status="progress">
                <div class="card-ribbon orange">Sedang Dipelajari</div>
                <div class="card-header">
                    <div class="card-icon orange">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="card-badge">Pemrograman</div>
                </div>
                <div class="card-body">
                    <h3>Pemrograman Web Lanjut</h3>
                    <p>Kuasai JavaScript ES6+, React, Node.js dan framework modern untuk web development.</p>
                    <div class="card-meta">
                        <span><i class="fas fa-file-alt"></i> 15 Modul</span>
                        <span><i class="fas fa-clock"></i> 10 Jam</span>
                    </div>
                    <div class="card-progress">
                        <div class="progress-bar">
                            <div class="progress-fill orange" style="width: 90%"></div>
                        </div>
                        <span class="progress-text">90% Selesai</span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn-secondary"><i class="fas fa-play"></i> Lanjutkan</button>
                    <button class="btn-icon" title="Detail"><i class="fas fa-info-circle"></i></button>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="materi-card" data-category="database" data-status="progress">
                <div class="card-ribbon orange">Sedang Dipelajari</div>
                <div class="card-header">
                    <div class="card-icon blue">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="card-badge">Database</div>
                </div>
                <div class="card-body">
                    <h3>Basis Data & SQL</h3>
                    <p>Pelajari desain database, normalisasi, query optimization, dan manajemen database.</p>
                    <div class="card-meta">
                        <span><i class="fas fa-file-alt"></i> 10 Modul</span>
                        <span><i class="fas fa-clock"></i> 7 Jam</span>
                    </div>
                    <div class="card-progress">
                        <div class="progress-bar">
                            <div class="progress-fill blue" style="width: 60%"></div>
                        </div>
                        <span class="progress-text">60% Selesai</span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn-secondary"><i class="fas fa-play"></i> Lanjutkan</button>
                    <button class="btn-icon" title="Detail"><i class="fas fa-info-circle"></i></button>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="materi-card" data-category="matematika" data-status="completed">
                <div class="card-ribbon green">Selesai</div>
                <div class="card-header">
                    <div class="card-icon orange">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="card-badge">Matematika</div>
                </div>
                <div class="card-body">
                    <h3>Matematika Diskrit</h3>
                    <p>Teori graf, kombinatorik, logika matematika, dan aplikasinya dalam ilmu komputer.</p>
                    <div class="card-meta">
                        <span><i class="fas fa-file-alt"></i> 8 Modul</span>
                        <span><i class="fas fa-clock"></i> 6 Jam</span>
                    </div>
                    <div class="card-progress">
                        <div class="progress-bar">
                            <div class="progress-fill green" style="width: 100%"></div>
                        </div>
                        <span class="progress-text">100% Selesai</span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn-secondary"><i class="fas fa-redo"></i> Ulangi</button>
                    <button class="btn-icon" title="Detail"><i class="fas fa-info-circle"></i></button>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="materi-card" data-category="jaringan" data-status="new">
                <div class="card-ribbon blue">Baru</div>
                <div class="card-header">
                    <div class="card-icon blue">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <div class="card-badge">Jaringan</div>
                </div>
                <div class="card-body">
                    <h3>Jaringan Komputer</h3>
                    <p>Konsep jaringan, protokol TCP/IP, keamanan jaringan, dan administrasi sistem.</p>
                    <div class="card-meta">
                        <span><i class="fas fa-file-alt"></i> 11 Modul</span>
                        <span><i class="fas fa-clock"></i> 9 Jam</span>
                    </div>
                    <div class="card-progress">
                        <div class="progress-bar">
                            <div class="progress-fill blue" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">Belum Dimulai</span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn-secondary"><i class="fas fa-play"></i> Mulai</button>
                    <button class="btn-icon" title="Detail"><i class="fas fa-info-circle"></i></button>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="materi-card" data-category="pemrograman" data-status="completed">
                <div class="card-ribbon green">Selesai</div>
                <div class="card-header">
                    <div class="card-icon orange">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="card-badge">Pemrograman</div>
                </div>
                <div class="card-body">
                    <h3>Mobile App Development</h3>
                    <p>Belajar membuat aplikasi mobile dengan Flutter dan React Native.</p>
                    <div class="card-meta">
                        <span><i class="fas fa-file-alt"></i> 14 Modul</span>
                        <span><i class="fas fa-clock"></i> 12 Jam</span>
                    </div>
                    <div class="card-progress">
                        <div class="progress-bar">
                            <div class="progress-fill green" style="width: 100%"></div>
                        </div>
                        <span class="progress-text">100% Selesai</span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn-secondary"><i class="fas fa-redo"></i> Ulangi</button>
                    <button class="btn-icon" title="Detail"><i class="fas fa-info-circle"></i></button>
                </div>
            </div>
        </section>
    </main>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

     <!-- Loading Screen -->
    @include('partials.loadingScreen')
    
</body>
</html>