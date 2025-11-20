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
   <!-- header -->
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
                        <p>{{ Str::limit($materi->deskripsi, 100) }}</p>
                        <div class="card-meta">
                            <span><i class="fas fa-file-alt"></i> {{ $materi->jumlah_modul }} Modul</span>
                            <span><i class="fas fa-clock"></i> {{ $materi->durasi_jam }} Jam</span>
                        </div>
                        <div class="card-progress">
                            <div class="progress-bar">
                                <div class="progress-fill {{ $materi->warna }}" 
                                     style="width: {{ $materi->progress }}%"></div>
                            </div>
                            <span class="progress-text">{{ $materi->progress }}% Selesai</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{-- Tombol Lanjutkan - Mengarah ke Show --}}
                        <a href="{{ route('materi.show', $materi->id) }}" 
                           class="btn-secondary">
                            <i class="fas fa-play"></i> Lanjutkan
                        </a>
                        
                        {{-- Action Buttons --}}
                        <div style="display: flex; gap: 5px;">
                            {{-- Info/Detail Button --}}
                            <a href="{{ route('materi.show', $materi->id) }}" 
                               class="btn-icon" 
                               title="Lihat Detail Materi">
                                <i class="fas fa-info-circle"></i>
                            </a>
                            
                            {{-- Edit Button --}}
                            <a href="{{ route('materi.edit', $materi->id) }}" 
                               class="btn-icon" 
                               title="Edit Materi">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            {{-- Delete Button --}}
                            <form action="{{ route('materi.destroy', $materi->id) }}" 
                                  method="POST" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Yakin ingin menghapus materi {{ $materi->judul }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn-icon" 
                                        title="Hapus Materi"
                                        style="background: none; border: none; cursor: pointer; padding: 8px; color: inherit;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 60px 40px;">
                    <i class="fas fa-inbox" style="font-size: 64px; color: #e0e0e0; margin-bottom: 20px;"></i>
                    <h3 style="color: #666; margin-bottom: 8px;">Belum Ada Materi</h3>
                    <p style="color: #999; margin-bottom: 24px;">Mulai tambahkan materi pembelajaran pertama Anda</p>
                    <a href="{{ route('materi.create') }}" class="btn-primary" style="display: inline-block; text-decoration: none;">
                        <i class="fas fa-plus"></i> Tambah Materi Baru
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

        // Search functionality with debounce
        const searchInput = document.getElementById('searchInput');
        const searchClear = document.getElementById('searchClear');
        
        if (searchInput) {
            let searchTimeout;
            
            // Show/hide clear button
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    searchClear.style.display = 'block';
                } else {
                    searchClear.style.display = 'none';
                }
                
                // Debounced search
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch();
                }, 500);
            });
            
            // Clear search
            if (searchClear) {
                searchClear.addEventListener('click', function() {
                    searchInput.value = '';
                    this.style.display = 'none';
                    performSearch();
                });
            }
        }
        
        function performSearch() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const searchValue = searchInput.value;
            
            // Build URL with all parameters
            const params = new URLSearchParams();
            
            // Add form parameters
            for (let [key, value] of formData.entries()) {
                if (value && value !== 'all') {
                    params.set(key, value);
                }
            }
            
            // Add search parameter
            if (searchValue) {
                params.set('search', searchValue);
            }
            
            // Redirect with parameters
            window.location.href = '{{ route("materi.index") }}?' + params.toString();
        }

        // Card hover effects
        document.querySelectorAll('.materi-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Show initial search value clear button
        if (searchInput && searchInput.value.length > 0) {
            searchClear.style.display = 'block';
        }
    </script>
</body>
</html>