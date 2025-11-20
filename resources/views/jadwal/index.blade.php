<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/jadwal.css', 'resources/css/materi.css', 'resources/js/jadwal.js'])
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
                     <img class="logo-icon" src="{{ asset('assets/logo_akademik_hd.png') }}" alt="Logo My Schuder" />
                    <div class="logo-text">
                        <h1>My Schuder</h1>
                        <p>Portal Pembelajaran</p>
                    </div>
                </div>
            </div>

            <div class="header-center">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari jadwal atau mata kuliah...">
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
                        <p class="user-name"></p>
                        <p class="user-role"></p>
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
                    <h1><i class="fas fa-calendar-alt"></i> Jadwal Kelas</h1>
                    <p>Kelola dan pantau jadwal pembelajaran Anda</p>
                </div>
               
            </div>
        </section>

        <!-- Calendar Navigation -->
        <section class="calendar-nav">
            <div class="calendar-controls">
                <button class="nav-btn" id="prevWeek">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 id="currentWeek">{{ now()->startOfWeek()->format('d') }} - {{ now()->endOfWeek()->format('d M Y') }}</h2>
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
                    <span class="stat-number">{{ $todayClasses }}</span>
                    <span class="stat-label">Kelas Hari Ini</span>
                </div>
            </div>
            <div class="stat-item orange">
                <i class="fas fa-clock"></i>
                <div>
                    <span class="stat-number">{{ $totalWeekHours }}</span>
                    <span class="stat-label">Jam Per Minggu</span>
                </div>
            </div>
            <div class="stat-item green">
                <i class="fas fa-chalkboard-teacher"></i>
                <div>
                    <span class="stat-number">{{ $totalSubjects }}</span>
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
                    @for($hour = 8; $hour <= 16; $hour++)
                        <div class="time-slot">{{ sprintf('%02d:00', $hour) }}</div>
                    @endfor
                </div>

                <!-- Days -->
                @php
                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    $today = ucfirst(now()->locale('id')->dayName);
                @endphp

                @foreach($days as $index => $day)
                    <div class="day-column">
                        <div class="day-header {{ $day == $today ? 'today' : '' }}">
                            <span class="day-name">{{ $day }}</span>
                            <span class="day-date">{{ now()->startOfWeek()->addDays($index)->format('d M') }}</span>
                            @if($day == $today)
                                <span class="today-badge">Hari Ini</span>
                            @endif
                        </div>
                        <div class="schedule-slots">
                            @if(isset($weekSchedule[$day]))
                                @foreach($weekSchedule[$day] as $jadwal)
                                    <div class="class-item {{ $jadwal->warna }}" 
                                         style="grid-row: {{ $jadwal->grid_row }};" 
                                         data-time="{{ $jadwal->formatted_time }}">
                                        <div class="class-time">{{ $jadwal->formatted_time }}</div>
                                        <div class="class-name">{{ $jadwal->nama_matkul }}</div>
                                        <div class="class-room">
                                            <i class="fas fa-map-marker-alt"></i> {{ $jadwal->ruangan }}
                                        </div>
                                        <div class="class-lecturer">{{ $jadwal->dosen }}</div>
                                        <div class="class-actions" style="margin-top: 8px; display: flex; gap: 5px;">
                                            <a href="{{ route('jadwal.edit', $jadwal->id) }}" 
                                               class="btn-sm" 
                                               style="padding: 4px 8px; font-size: 11px; background: rgba(255,255,255,0.2); border-radius: 4px;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('jadwal.destroy', $jadwal->id) }}" 
                                                  method="POST" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn-sm" 
                                                        style="padding: 4px 8px; font-size: 11px; background: rgba(255,255,255,0.2); border: none; border-radius: 4px; color: white; cursor: pointer;"
                                                        onclick="return confirm('Hapus jadwal ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Upcoming Classes -->
        <section class="upcoming-section">
            <div class="section-header">
                <h3><i class="fas fa-clock"></i> Kelas Selanjutnya</h3>
                <button class="view-all-btn">Lihat Semua <i class="fas fa-arrow-right"></i></button>
            </div>
            <div class="upcoming-grid">
                <!-- Kelas yang sedang berlangsung -->
                @if($currentClass)
                    <div class="upcoming-card active">
                        <div class="card-status">Sedang Berlangsung</div>
                        <div class="card-content">
                            <h4>{{ $currentClass->nama_matkul }}</h4>
                            <div class="card-info">
                                <span><i class="fas fa-clock"></i> {{ $currentClass->formatted_time }}</span>
                                <span><i class="fas fa-map-marker-alt"></i> {{ $currentClass->ruangan }}</span>
                            </div>
                            <div class="card-lecturer">
                                <i class="fas fa-user"></i> {{ $currentClass->dosen }}
                            </div>
                            <div class="card-time-left">
                                <i class="fas fa-hourglass-half"></i> Sedang berlangsung
                            </div>
                        </div>
                        <button class="btn-join active"><i class="fas fa-video"></i> Join Now</button>
                    </div>
                @endif

                <!-- Kelas selanjutnya hari ini -->
                @if($nextClass)
                    <div class="upcoming-card">
                        <div class="card-content">
                            <h4>{{ $nextClass->nama_matkul }}</h4>
                            <div class="card-info">
                                <span><i class="fas fa-clock"></i> {{ $nextClass->formatted_time }}</span>
                                <span><i class="fas fa-map-marker-alt"></i> {{ $nextClass->ruangan }}</span>
                            </div>
                            <div class="card-lecturer">
                                <i class="fas fa-user"></i> {{ $nextClass->dosen }}
                            </div>
                            <div class="card-countdown">
                                <i class="fas fa-clock"></i> Kelas Selanjutnya
                            </div>
                        </div>
                        <button class="btn-join"><i class="far fa-calendar-plus"></i> Set Reminder</button>
                    </div>
                @endif

                <!-- Kelas besok -->
                @if($tomorrowSchedule)
                    <div class="upcoming-card">
                        <div class="card-content">
                            <h4>{{ $tomorrowSchedule->nama_matkul }}</h4>
                            <div class="card-info">
                                <span><i class="fas fa-clock"></i> {{ $tomorrowSchedule->formatted_time }} (Besok)</span>
                                <span><i class="fas fa-map-marker-alt"></i> {{ $tomorrowSchedule->ruangan }}</span>
                            </div>
                            <div class="card-lecturer">
                                <i class="fas fa-user"></i> {{ $tomorrowSchedule->dosen }}
                            </div>
                        </div>
                        <button class="btn-join"><i class="fas fa-info-circle"></i> Detail</button>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>
</body>
</html>