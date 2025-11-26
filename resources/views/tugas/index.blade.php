<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tugas - My Schuder</title>
    
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
                    <h1><i class="fas fa-clipboard-list"></i> Tugas & Pengumpulan</h1>
                    <p>Kelola dan kumpulkan tugas Anda tepat waktu</p>
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
                <button class="filter-tab {{ request('status') == 'pending' ? 'active' : '' }}" 
                        onclick="filterByStatus('pending')">
                    <i class="fas fa-clock"></i> Belum Dikumpulkan 
                    <span class="tab-count warning">{{ $counts['pending'] ?? 0 }}</span>
                </button>
                <button class="filter-tab {{ request('status') == 'submitted' ? 'active' : '' }}" 
                        onclick="filterByStatus('submitted')">
                    <i class="fas fa-check"></i> Sudah Dikumpulkan 
                    <span class="tab-count success">{{ $counts['submitted'] ?? 0 }}</span>
                </button>
            </div>
        </section>

        <!-- Stats Cards -->
        <section class="stats-grid">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-info">
                    <h3>Deadline Dekat</h3>
                    <p class="stat-value">{{ $stats['deadline_dekat'] ?? 0 }}</p>
                    <span class="stat-desc">Dalam 3 hari</span>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <h3>Belum Dikumpulkan</h3>
                    <p class="stat-value">{{ $stats['belum_dikumpulkan'] ?? 0 }}</p>
                    <span class="stat-desc">Harus segera diselesaikan</span>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>Sudah Dikumpulkan</h3>
                    <p class="stat-value">{{ $stats['sudah_dikumpulkan'] ?? 0 }}</p>
                    <span class="stat-desc">Tugas selesai</span>
                </div>
            </div>
        </section>

        <!-- Tugas List -->
        <section class="tugas-container" id="tugasContainer">
            @forelse($tugas as $item)
            <div class="tugas-item" data-status="{{ $item->status }}" data-priority="{{ $item->priority }}">
                
                @if($item->is_deadline_dekat && $item->status == 'pending')
                <div class="tugas-priority high">
                    <i class="fas fa-exclamation-circle"></i>
                    Deadline Dekat!
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
                    <div class="tugas-status {{ $item->status }}">
                        <i class="fas fa-{{ $item->status == 'submitted' ? 'check' : 'clock' }}"></i>
                        {{ $item->status == 'submitted' ? 'Sudah Dikumpulkan' : 'Belum Dikumpulkan' }}
                    </div>
                </div>

                <!-- Body -->
                <div class="tugas-body">
                    <p class="tugas-description">{{ $item->deskripsi }}</p>

                    <!-- Meta Information -->
                    <div class="tugas-meta">
                        @if($item->status == 'pending')
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Diberikan: {{ $item->tanggal_diberikan->format('d M Y') }}</span>
                            </div>
                            <div class="meta-item {{ $item->is_deadline_dekat ? 'deadline' : '' }}">
                                <i class="fas fa-clock"></i>
                                <span>Deadline: {{ $item->deadline->format('d M Y') }} ({{ $item->sisa_hari }})</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-weight-hanging"></i>
                                <span>Bobot: {{ $item->bobot }}%</span>
                            </div>
                        @else
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Dikumpulkan: {{ $item->pengumpulan_data->waktu_pengumpulan->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="meta-item {{ $item->pengumpulan_data->tepat_waktu ? 'success' : 'deadline' }}">
                                <i class="fas fa-{{ $item->pengumpulan_data->tepat_waktu ? 'check-circle' : 'exclamation-circle' }}"></i>
                                <span>{{ $item->pengumpulan_data->tepat_waktu ? 'Tepat Waktu' : 'Terlambat' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-file"></i>
                                <span>{{ basename($item->pengumpulan_data->file_tugas) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="tugas-footer">
                    @if($item->status == 'pending')
                        <button class="btn-action primary" onclick="openSubmitModal({{ $item->id }})">
                            <i class="fas fa-upload"></i> Kumpulkan Tugas
                        </button>
                        <button class="btn-action secondary" onclick="window.location.href='{{ route('tugas.show', $item->id) }}'">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </button>
                        @if($item->file_soal)
                        <a href="{{ asset('storage/' . $item->file_soal) }}" class="btn-action tertiary" download>
                            <i class="fas fa-download"></i> Unduh Soal
                        </a>
                        @endif
                    @else
                        <button class="btn-action secondary" onclick="window.location.href='{{ route('tugas.show', $item->id) }}'">
                            <i class="fas fa-eye"></i> Lihat Pengumpulan
                        </button>
                        <a href="{{ asset('storage/' . $item->pengumpulan_data->file_tugas) }}" class="btn-action tertiary" download>
                            <i class="fas fa-download"></i> Unduh File
                        </a>
                        <button class="btn-action tertiary" onclick="openSubmitModal({{ $item->id }})">
                            <i class="fas fa-edit"></i> Edit Pengumpulan
                        </button>
                    @endif
                </div>
            </div>
            
            @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Tidak Ada Tugas</h3>
                <p>Belum ada tugas yang tersedia saat ini.</p>
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

        function openSubmitModal(tugasId) {
            // Create modal using existing upload dialog from tugas.js
            const uploadDialog = new window.UploadDialog(window.NotificationManager.getInstance());
            uploadDialog.show(tugasId);
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