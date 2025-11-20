<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Materi Kelas - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/materi.js'])
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
                    <input type="text" id="searchInput" placeholder="Cari materi..." 
                           value="{{ request('search') }}">
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
              
            </div>
        </section>

        @if(session('success'))
            <div class="alert alert-success" style="margin: 20px; padding: 15px; background: #d4edda; color: #155724; border-radius: 8px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Section -->
        <section class="filter-section">
            <form method="GET" action="{{ route('materi.index') }}" id="filterForm">
                <div class="filter-tabs">
                    <button type="button" class="filter-tab {{ !request('filter') || request('filter') == 'all' ? 'active' : '' }}" 
                            onclick="setFilter('all')">
                        <i class="fas fa-th"></i> Semua
                    </button>
                    <button type="button" class="filter-tab {{ request('filter') == 'recent' ? 'active' : '' }}" 
                            onclick="setFilter('recent')">
                        <i class="fas fa-clock"></i> Terbaru
                    </button>
                    <button type="button" class="filter-tab {{ request('filter') == 'progress' ? 'active' : '' }}" 
                            onclick="setFilter('progress')">
                        <i class="fas fa-chart-line"></i> Sedang Dipelajari
                    </button>
                    <button type="button" class="filter-tab {{ request('filter') == 'completed' ? 'active' : '' }}" 
                            onclick="setFilter('completed')">
                        <i class="fas fa-check-circle"></i> Selesai
                    </button>
                </div>
                
                <input type="hidden" name="filter" id="filterInput" value="{{ request('filter', 'all') }}">
                
                <div class="filter-actions">
                    <select class="filter-select" name="kategori" onchange="this.form.submit()">
                        <option value="all" {{ request('kategori') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="pemrograman" {{ request('kategori') == 'pemrograman' ? 'selected' : '' }}>Pemrograman</option>
                        <option value="matematika" {{ request('kategori') == 'matematika' ? 'selected' : '' }}>Matematika</option>
                        <option value="database" {{ request('kategori') == 'database' ? 'selected' : '' }}>Database</option>
                        <option value="jaringan" {{ request('kategori') == 'jaringan' ? 'selected' : '' }}>Jaringan</option>
                    </select>
                    
                    <select class="filter-select" name="sort" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="progress" {{ request('sort') == 'progress' ? 'selected' : '' }}>Progress</option>
                    </select>
                </div>
            </form>
        </section>

        <!-- Stats Overview -->
        <section class="stats-overview">
            <div class="stat-box blue">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-info">
                    <h3>Total Materi</h3>
                    <p class="stat-value">{{ $stats['total'] }}</p>
                </div>
            </div>
            <div class="stat-box orange">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div class="stat-info">
                    <h3>Sedang Dipelajari</h3>
                    <p class="stat-value">{{ $stats['progress'] }}</p>
                </div>
            </div>
            <div class="stat-box green">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>Selesai</h3>
                    <p class="stat-value">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </section>

        <!-- Materi Grid -->
        <section class="materi-grid" id="materiGrid">
            @forelse($materis as $materi)
                <div class="materi-card" data-category="{{ $materi->kategori }}" data-status="{{ $materi->status }}">
                    <div class="card-ribbon {{ $materi->ribbon_color }}">{{ $materi->ribbon_text }}</div>
                    <div class="card-header">
                        <div class="card-icon {{ $materi->warna }}">
                            <i class="fas {{ $materi->icon }}"></i>
                        </div>
                        <div class="card-badge">{{ ucfirst($materi->kategori) }}</div>
                    </div>
                    <div class="card-body">
                        <h3>{{ $materi->judul }}</h3>
                        <p>{{ $materi->deskripsi }}</p>
                        <div class="card-meta">
                            <span><i class="fas fa-file-alt"></i> {{ $materi->jumlah_modul }} Modul</span>
                            <span><i class="fas fa-clock"></i> {{ $materi->durasi_jam }} Jam</span>
                        </div>
                        <div class="card-progress">
                            <div class="progress-bar">
                                <div class="progress-fill {{ $materi->warna }}" 
                                     style="width: {{ $materi->progress }}%"></div>
                            </div>
                            <span class="progress-text">{{ $materi->progress_text }}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn-secondary">
                            <i class="fas {{ $materi->button_icon }}"></i> {{ $materi->button_text }}
                        </button>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('materi.show', $materi->id) }}" 
                               class="btn-icon" title="Detail">
                                <i class="fas fa-info-circle"></i>
                            </a>
                            <a href="{{ route('materi.edit', $materi->id) }}" 
                               class="btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('materi.destroy', $materi->id) }}" 
                                  method="POST" 
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn-icon" 
                                        title="Hapus"
                                        onclick="return confirm('Hapus materi ini?')"
                                        style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                    <p style="color: #666;">Belum ada materi tersedia</p>
                    <a href="{{ route('materi.create') }}" class="btn-primary" style="margin-top: 16px; display: inline-block;">
                        <i class="fas fa-plus"></i> Tambah Materi Pertama
                    </a>
                </div>
            @endforelse
        </section>
    </main>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Loading Screen -->
    @include('partials.loadingScreen')

    <script>
        function setFilter(filter) {
            document.getElementById('filterInput').value = filter;
            document.getElementById('filterForm').submit();
        }

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const form = document.getElementById('filterForm');
                    const searchParam = new URLSearchParams(new FormData(form));
                    searchParam.set('search', this.value);
                    window.location.href = '{{ route("materi.index") }}?' + searchParam.toString();
                }, 500);
            });
        }
    </script>
</body>
</html>