<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tugas - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/tugas.css', 'resources/js/tugas.js'])
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
                    <input type="text" id="searchInput" placeholder="Cari tugas..." value="{{ request('search') }}">
                    <button class="search-clear" id="searchClear" style="{{ request('search') ? '' : 'display: none;' }}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="header-right">
                <button class="notification-btn" id="notificationBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="badge">{{ $counts['pending'] ?? 0 }}</span>
                </button>
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                    <div class="user-info">
                        <p class="user-name">{{ auth()->user()->name ?? 'Ahmad Student' }}</p>
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
        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        <!-- Page Header -->
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-clipboard-list"></i> Tugas & Penilaian</h1>
                    <p>Kelola dan kumpulkan tugas Anda tepat waktu</p>
                </div>
                <button class="btn-primary" id="uploadTugasBtn" onclick="window.location.href='{{ route('tugas.create') }}'">
                    <i class="fas fa-upload"></i>
                    Upload Tugas
                </button>
            </div>
        </section>

        <!-- Filter Tabs -->
        <section class="filter-section">
            <div class="filter-tabs">
                <button class="filter-tab {{ request('status', 'all') == 'all' ? 'active' : '' }}" 
                        onclick="filterByStatus('all')">
                    <i class="fas fa-th"></i> Semua 
                    <span class="tab-count">{{ $tugas->count() }}</span>
                </button>
                <button class="filter-tab {{ request('status') == 'pending' ? 'active' : '' }}" 
                        onclick="filterByStatus('pending')">
                    <i class="fas fa-clock"></i> Pending 
                    <span class="tab-count warning">{{ $counts['pending'] ?? 0 }}</span>
                </button>
                <button class="filter-tab {{ request('status') == 'submitted' ? 'active' : '' }}" 
                        onclick="filterByStatus('submitted')">
                    <i class="fas fa-check"></i> Dikumpulkan 
                    <span class="tab-count success">{{ $counts['submitted'] ?? 0 }}</span>
                </button>
                <button class="filter-tab {{ request('status') == 'graded' ? 'active' : '' }}" 
                        onclick="filterByStatus('graded')">
                    <i class="fas fa-star"></i> Dinilai 
                    <span class="tab-count blue">{{ $counts['graded'] ?? 0 }}</span>
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
                    <span class="stat-desc">Menunggu penilaian</span>
                </div>
            </div>
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-info">
                    <h3>Nilai Rata-rata</h3>
                    <p class="stat-value">{{ $stats['rata_rata_nilai'] ?? 0 }}</p>
                    <span class="stat-desc">
                        @if(($stats['rata_rata_nilai'] ?? 0) >= 85)
                            Sangat baik!
                        @elseif(($stats['rata_rata_nilai'] ?? 0) >= 75)
                            Baik!
                        @else
                            Perlu ditingkatkan
                        @endif
                    </span>
                </div>
            </div>
        </section>

        <!-- Tugas List -->
        <section class="tugas-container" id="tugasContainer">
            @forelse($tugas as $item)
            <div class="tugas-item" data-status="{{ $item->status }}" data-priority="{{ $item->priority }}">
                
                <!-- Priority Badge for High Priority Pending Tasks -->
                @if($item->priority == 'high' && $item->status == 'pending')
                <div class="tugas-priority high">
                    <i class="fas fa-exclamation-circle"></i>
                    Deadline Dekat!
                </div>
                @endif

                <!-- Grade Badge for Graded Tasks -->
                @if($item->status == 'graded' && $item->nilai)
                <div class="tugas-grade {{ $item->nilai >= 85 ? 'excellent' : 'good' }}">
                    <i class="fas fa-{{ $item->nilai >= 85 ? 'trophy' : 'star' }}"></i>
                    <span class="grade-value">{{ $item->nilai }}</span>
                    <span class="grade-label">/ 100</span>
                </div>
                @endif

                <!-- Header -->
                <div class="tugas-header">
                    <div class="tugas-info">
                        <h3>{{ $item->judul }}</h3>
                        <p class="tugas-subject">
                            <i class="fas fa-book"></i> 
                            {{ $item->materi->nama_materi ?? 'Materi' }}
                        </p>
                    </div>
                    <div class="tugas-status {{ $item->status }}">
                        <i class="fas fa-{{ 
                            $item->status == 'pending' ? 'clock' : 
                            ($item->status == 'submitted' ? 'check' : 'star') 
                        }}"></i>
                        {{ 
                            $item->status == 'pending' ? 'Belum Dikumpulkan' : 
                            ($item->status == 'submitted' ? 'Sudah Dikumpulkan' : 'Sudah Dinilai') 
                        }}
                    </div>
                </div>

                <!-- Body -->
                <div class="tugas-body">
                    <p class="tugas-description">{{ $item->deskripsi }}</p>

                    <!-- Feedback Box for Graded Tasks -->
                    @if($item->status == 'graded' && $item->feedback)
                    <div class="feedback-box {{ $item->nilai >= 85 ? '' : 'warning' }}">
                        <h4><i class="fas fa-comment-alt"></i> Feedback Dosen:</h4>
                        <p>"{{ $item->feedback }}"</p>
                    </div>
                    @endif

                    <!-- Meta Information -->
                    <div class="tugas-meta">
                        @if($item->status == 'pending')
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Diberikan: {{ $item->tanggal_diberikan->format('d M Y') }}</span>
                            </div>
                            <div class="meta-item {{ $item->deadline->diffInDays(now()) <= 3 ? 'deadline' : '' }}">
                                <i class="fas fa-clock"></i>
                                <span>Deadline: {{ $item->deadline->format('d M Y') }} 
                                    ({{ $item->deadline->diffForHumans() }})
                                </span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-weight-hanging"></i>
                                <span>Bobot: {{ $item->bobot }}%</span>
                            </div>
                        @elseif($item->status == 'submitted')
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Dikumpulkan: {{ $item->waktu_pengumpulan->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="meta-item {{ $item->tepat_waktu ? 'success' : 'deadline' }}">
                                <i class="fas fa-{{ $item->tepat_waktu ? 'check-circle' : 'exclamation-circle' }}"></i>
                                <span>{{ $item->tepat_waktu ? 'Tepat Waktu' : 'Terlambat' }}</span>
                            </div>
                            @if($item->file_jawaban)
                            <div class="meta-item">
                                <i class="fas fa-file"></i>
                                <span>{{ basename($item->file_jawaban) }}</span>
                            </div>
                            @endif
                        @elseif($item->status == 'graded')
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Dinilai: {{ $item->updated_at->format('d M Y') }}</span>
                            </div>
                            <div class="meta-item success">
                                <i class="fas fa-award"></i>
                                <span>Grade: {{ $item->grade }}</span>
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
                    @elseif($item->status == 'submitted')
                        <button class="btn-action secondary" onclick="window.location.href='{{ route('tugas.show', $item->id) }}'">
                            <i class="fas fa-eye"></i> Lihat Pengumpulan
                        </button>
                        <button class="btn-action tertiary" onclick="openSubmitModal({{ $item->id }})">
                            <i class="fas fa-edit"></i> Edit Pengumpulan
                        </button>
                    @elseif($item->status == 'graded')
                        <button class="btn-action secondary" onclick="window.location.href='{{ route('tugas.show', $item->id) }}'">
                            <i class="fas fa-eye"></i> Lihat Detail Nilai
                        </button>
                        @if($item->feedback)
                        <button class="btn-action tertiary" onclick="downloadFeedback({{ $item->id }})">
                            <i class="fas fa-download"></i> Unduh Feedback
                        </button>
                        @endif
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

    <!-- Submit Modal -->
    <div id="submitModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Kumpulkan Tugas</h3>
                <button class="modal-close" onclick="closeSubmitModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="submitForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Upload File Jawaban</label>
                        <input type="file" name="file_jawaban" required 
                               accept=".pdf,.doc,.docx,.zip">
                        <small>Format: PDF, DOC, DOCX, ZIP (Max: 20MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action secondary" onclick="closeSubmitModal()">
                        Batal
                    </button>
                    <button type="submit" class="btn-action primary">
                        <i class="fas fa-upload"></i> Kumpulkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

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
            const modal = document.getElementById('submitModal');
            const form = document.getElementById('submitForm');
            form.action = `/tugas/${tugasId}/submit`;
            modal.style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeSubmitModal() {
            document.getElementById('submitModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        // Search functionality
        document.getElementById('searchInput')?.addEventListener('input', function(e) {
            const searchBtn = document.getElementById('searchClear');
            if (e.target.value) {
                searchBtn.style.display = 'block';
            } else {
                searchBtn.style.display = 'none';
            }
        });

        document.getElementById('searchClear')?.addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            this.style.display = 'none';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            window.location.href = url.toString();
        });

        // Submit search on Enter
        document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const url = new URL(window.location.href);
                if (this.value) {
                    url.searchParams.set('search', this.value);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }
        });

        // Close modal on overlay click
        document.getElementById('overlay')?.addEventListener('click', closeSubmitModal);
    </script>

    <style>
        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #e9ecef;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #adb5bd;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: #212529;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: #f8f9fa;
            color: #212529;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #212529;
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            cursor: pointer;
        }

        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #6c757d;
            font-size: 0.875rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
    </style>
</body>
</html>