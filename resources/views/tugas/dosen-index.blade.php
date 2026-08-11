<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Tugas - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/dashboard.css', 'resources/css/pages.css','resources/js/app.js'])
</head>
<body>
    
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
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

        <!-- Page Header -->
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-tasks"></i> Kelola Tugas</h1>
                    <p>Kelola tugas yang Anda berikan kepada mahasiswa</p>
                </div>
                <button class="btn-primary" onclick="window.location.href='{{ route('tugas.create') }}'">
                    <i class="fas fa-plus"></i>
                    Tambah Tugas Baru
                </button>
            </div>
        </section>

        <!-- Filter Tabs -->
        <section class="filter-section">
            <div class="filter-tabs">
                <button class="filter-tab {{ request('status', 'all') == 'all' ? 'active' : '' }}" 
                        onclick="filterByStatus('all')">
                    <i class="fas fa-th"></i> Semua 
                    <span class="tab-count">{{ $counts['all'] ?? 0 }}</span>
                </button>
                <button class="filter-tab {{ request('status') == 'active' ? 'active' : '' }}" 
                        onclick="filterByStatus('active')">
                    <i class="fas fa-clock"></i> Aktif 
                    <span class="tab-count success">{{ $counts['active'] ?? 0 }}</span>
                </button>
                <button class="filter-tab {{ request('status') == 'expired' ? 'active' : '' }}" 
                        onclick="filterByStatus('expired')">
                    <i class="fas fa-times-circle"></i> Kadaluarsa 
                    <span class="tab-count warning">{{ $counts['expired'] ?? 0 }}</span>
                </button>
            </div>
        </section>

        <!-- Stats Cards -->
        <section class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-info">
                    <h3>Total Tugas</h3>
                    <p class="stat-value">{{ $stats['total'] ?? 0 }}</p>
                    <span class="stat-desc">Tugas yang dibuat</span>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>Tugas Aktif</h3>
                    <p class="stat-value">{{ $stats['active'] ?? 0 }}</p>
                    <span class="stat-desc">Belum deadline</span>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="fas fa-upload"></i></div>
                <div class="stat-info">
                    <h3>Total Pengumpulan</h3>
                    <p class="stat-value">{{ $stats['total_submissions'] ?? 0 }}</p>
                    <span class="stat-desc">File terkumpul</span>
                </div>
            </div>
        </section>

        <!-- Tugas List -->
        <section class="tugas-container" id="tugasContainer">
            @forelse($tugas as $item)
            <div class="tugas-item" data-status="{{ $item->is_expired ? 'expired' : 'active' }}" data-priority="{{ $item->priority }}">
                
                @if($item->is_expired)
                <div class="tugas-priority low">
                    <i class="fas fa-calendar-times"></i>
                    Deadline Terlewat
                </div>
                @endif

                <!-- Header -->
                <div class="tugas-header">
                    <div class="tugas-info">
                        <h3>{{ $item->judul }}</h3>
                        <p class="tugas-subject">
                            <i class="fas fa-book"></i> 
                            {{ $item->materi->judul ?? 'Materi' }}
                        </p>
                    </div>
                    <div class="tugas-status {{ $item->is_expired ? 'expired' : 'active' }}">
                        <i class="fas fa-{{ $item->is_expired ? 'times-circle' : 'clock' }}"></i>
                        {{ $item->is_expired ? 'Kadaluarsa' : 'Aktif' }}
                    </div>
                </div>

                <!-- Body -->
                <div class="tugas-body">
                    <p class="tugas-description">{{ Str::limit($item->deskripsi, 150) }}</p>

                    <!-- Meta Information -->
                    <div class="tugas-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Diberikan: {{ $item->tanggal_diberikan->format('d M Y') }}</span>
                        </div>
                        <div class="meta-item {{ $item->is_expired ? 'deadline' : '' }}">
                            <i class="fas fa-clock"></i>
                            <span>Deadline: {{ $item->deadline->format('d M Y') }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-weight-hanging"></i>
                            <span>Bobot: {{ $item->bobot }}%</span>
                        </div>
                        <div class="meta-item success">
                            <i class="fas fa-upload"></i>
                            <span>Pengumpulan: {{ $item->total_submissions ?? 0 }} mahasiswa</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="tugas-footer">
                    <button class="btn-action primary" onclick="window.location.href='{{ route('tugas.show', $item->id) }}'">
                        <i class="fas fa-eye"></i> Lihat Pengumpulan ({{ $item->total_submissions ?? 0 }})
                    </button>
                    <button class="btn-action secondary" onclick="window.location.href='{{ route('tugas.edit', $item->id) }}'">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    @if($item->file_soal)
                    <a href="{{ asset('storage/' . $item->file_soal) }}" class="btn-action tertiary" download>
                        <i class="fas fa-download"></i> Unduh Soal
                    </a>
                    @endif
                    <form action="{{ route('tugas.destroy', $item->id) }}" 
                          method="POST" 
                          style="display: inline;"
                          onsubmit="return confirm('Yakin ingin menghapus tugas {{ $item->judul }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action tertiary" style="background: #ef4444;">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            
            @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Belum Ada Tugas</h3>
                <p>Anda belum membuat tugas. Klik tombol "Tambah Tugas Baru" untuk memulai.</p>
                <button class="btn-primary" onclick="window.location.href='{{ route('tugas.create') }}'" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Tambah Tugas Pertama
                </button>
            </div>
            @endforelse
        </section>
    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>
    @include('partials.loadingscreen')

    <script>
        function filterByStatus(status) {
            const url = new URL(window.location.href);
            if (status === 'all') {
                url.searchParams.delete('status');
            } else {
                url.searchParams.set('status', status);
            }
            window.location.href = url.toString();
        }

        // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>