<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $materi->judul }} - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/dashboard.css', 'resources/js/app.js'])
    
    <style>
        /* Custom styles for PDF viewer page */
        .pdf-viewer-container {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .viewer-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            margin-top: 70px; /* Space for main header */
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .viewer-header-content {
            max-width: calc(100% - 250px); /* Adjust for sidebar */
            margin-left: 250px; /* Space for sidebar */
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        
        .viewer-title-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
            min-width: 0;
        }
        
        .back-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #f3f4f6;
            color: #374151;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .back-button:hover {
            background: #e5e7eb;
            transform: translateX(-2px);
        }
        
        .viewer-title h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .viewer-title p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }
        
        .viewer-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .progress-badge {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }
        
        .action-btn.download {
            background: #10b981;
            color: white;
        }
        
        .action-btn.download:hover {
            background: #059669;
        }
        
        .action-btn.edit {
            background: #f59e0b;
            color: white;
        }
        
        .action-btn.edit:hover {
            background: #d97706;
        }
        
        .viewer-main {
            max-width: 1400px;
            margin: 0 auto;
            margin-left: 250px; /* Space for sidebar */
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 1.5rem;
        }
        
        .pdf-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .pdf-viewer {
            width: 100%;
            height: calc(100vh - 200px);
            border: none;
        }
        
        .no-file-state {
            display: flex;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 200px);
            text-align: center;
            padding: 3rem;
        }
        
        .no-file-content i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        
        .no-file-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .no-file-content p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        
        .sidebar-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .info-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .info-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .info-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .info-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .info-item i {
            width: 20px;
        }
        
        .progress-card {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .progress-card h3 {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .progress-display {
            margin-bottom: 1rem;
        }
        
        .progress-display-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .progress-display-header span:first-child {
            font-size: 0.875rem;
            opacity: 0.9;
        }
        
        .progress-display-header span:last-child {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .progress-bar-bg {
            width: 100%;
            height: 12px;
            background: rgba(255,255,255,0.3);
            border-radius: 6px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            background: white;
            border-radius: 6px;
            transition: width 0.3s;
        }
        
        .progress-form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .status-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status-label {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .status-badge {
            padding: 0.5rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .dosen-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .dosen-card h4 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
        }
        
        .dosen-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .dosen-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .dosen-details p:first-child {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.875rem;
            margin-bottom: 0.125rem;
        }
        
        .dosen-details p:last-child {
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        @media (max-width: 1024px) {
            .viewer-main {
                grid-template-columns: 1fr;
                margin-left: 0;
            }
            
            .viewer-header-content {
                margin-left: 0;
                max-width: 100%;
            }
            
            .viewer-actions {
                flex-wrap: wrap;
            }
        }
        
        @media (max-width: 640px) {
            .viewer-header-content {
                flex-direction: column;
                align-items: stretch;
            }
            
            .viewer-title h1 {
                font-size: 1rem;
            }
            
            .viewer-actions {
                justify-content: space-between;
            }
            
            .action-btn span {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    @include('partials.header')

    <!-- Sidebar -->
    @include('partials.sidebar')

    {{-- Main Content Area --}}
    <div class="pdf-viewer-container">
        
        {{-- Sticky Header --}}
        <div class="viewer-header">
            <div class="viewer-header-content">
                <div class="viewer-title-section">
                    <a href="{{ route('materi.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="viewer-title">
                        <h1>{{ $materi->judul }}</h1>
                        <p>{{ $materi->dosen->nama ?? 'Dosen' }}</p>
                    </div>
                </div>
                
                <div class="viewer-actions">
                    <div class="progress-badge">
                        {{ $materi->progress }}% Selesai
                    </div>
                    
                    @if($materi->file)
                    <a href="{{ asset('storage/' . $materi->file) }}" 
                       download
                       class="action-btn download">
                        <i class="fas fa-download"></i>
                        <span>Download</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="viewer-main">
            
            {{-- PDF Viewer Section --}}
            <div class="pdf-section">
                @if($materi->file)
                    @php
                        $fileExtension = pathinfo($materi->file, PATHINFO_EXTENSION);
                    @endphp

                    @if(strtolower($fileExtension) == 'pdf')
                        <iframe src="{{ asset('storage/' . $materi->file) }}" 
                                class="pdf-viewer"
                                type="application/pdf">
                            <div class="no-file-state">
                                <div class="no-file-content">
                                    <i class="fas fa-file-pdf"></i>
                                    <h3>Browser tidak mendukung PDF viewer</h3>
                                    <p>Silakan download file untuk melihat materi</p>
                                    <a href="{{ asset('storage/' . $materi->file) }}" 
                                       download
                                       class="action-btn download">
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                        </iframe>

                    @elseif(in_array(strtolower($fileExtension), ['doc', 'docx', 'ppt', 'pptx']))
                        <iframe src="https://docs.google.com/viewer?url={{ urlencode(asset('storage/' . $materi->file)) }}&embedded=true"
                                class="pdf-viewer">
                        </iframe>

                    @else
                        <div class="no-file-state">
                            <div class="no-file-content">
                                <i class="fas fa-file"></i>
                                <h3>Pratinjau Tidak Tersedia</h3>
                                <p>File tipe .{{ $fileExtension }} tidak dapat ditampilkan</p>
                                <a href="{{ asset('storage/' . $materi->file) }}" 
                                   download
                                   class="action-btn download">
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="no-file-state">
                        <div class="no-file-content">
                            <i class="fas fa-folder-open"></i>
                            <h3>Materi Belum Tersedia</h3>
                            <p>File materi belum diupload oleh dosen</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar Section --}}
            <div class="sidebar-section">
                
                {{-- Materi Info Card --}}
                <div class="info-card">
                    <div class="info-header">
                        <div class="info-icon" style="background: 
                            @if($materi->kategori == 'pemrograman') #dbeafe
                            @elseif($materi->kategori == 'matematika') #d1fae5
                            @elseif($materi->kategori == 'database') #e9d5ff
                            @else #fed7aa
                            @endif;">
                            <i class="fas {{ $materi->icon ?? 'fa-book' }}" style="color:
                                @if($materi->kategori == 'pemrograman') #3b82f6
                                @elseif($materi->kategori == 'matematika') #10b981
                                @elseif($materi->kategori == 'database') #8b5cf6
                                @else #f59e0b
                                @endif;">
                            </i>
                        </div>
                        <div>
                            <span class="info-badge" style="background:
                                @if($materi->kategori == 'pemrograman') #dbeafe; color: #1e40af
                                @elseif($materi->kategori == 'matematika') #d1fae5; color: #065f46
                                @elseif($materi->kategori == 'database') #e9d5ff; color: #6b21a8
                                @else #fed7aa; color: #92400e
                                @endif;">
                                {{ ucfirst($materi->kategori) }}
                            </span>
                        </div>
                    </div>

                    <h3>Informasi Materi</h3>
                    
                    <div class="info-list">
                        <div class="info-item">
                            <i class="fas fa-book-open"></i>
                            <span>{{ $materi->jumlah_modul }} Modul</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $materi->durasi_jam }} Jam</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $materi->created_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    @if($materi->deskripsi)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                        <p style="font-size: 0.875rem; color: #6b7280; line-height: 1.6;">
                            {{ $materi->deskripsi }}
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Progress Card --}}
                <div class="progress-card">
                    <h3>Progress Pembelajaran</h3>
                    
                    <div class="progress-display">
                        <div class="progress-display-header">
                            <span>Selesai</span>
                            <span id="progressValue">{{ $materi->progress }}%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" id="progressBar" style="width: {{ $materi->progress }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Status Badge --}}
                <div class="status-card">
                    <span class="status-label">Status:</span>
                    <span class="status-badge" style="
                        @if($materi->status == 'new') background: #f3f4f6; color: #374151;
                        @elseif($materi->status == 'progress') background: #fef3c7; color: #92400e;
                        @else background: #d1fae5; color: #065f46;
                        @endif">
                        @if($materi->status == 'new') 📝 Baru
                        @elseif($materi->status == 'progress') 🔄 Berjalan
                        @else ✅ Selesai
                        @endif
                    </span>
                </div>

                {{-- Dosen Info --}}
                @if($materi->dosen)
                <div class="dosen-card">
                    <h4>Dosen Pengampu</h4>
                    <div class="dosen-info">
                        <img src="{{ $materi->dosen->foto ?? 'https://ui-avatars.com/api/?name=' . urlencode($materi->dosen->nama) }}" 
                             alt="{{ $materi->dosen->nama }}"
                             class="dosen-avatar">
                        <div class="dosen-details">
                            <p>{{ $materi->dosen->nama }}</p>
                            <p>{{ $materi->dosen->email ?? 'Dosen' }}</p>
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>

    </div>

    <script>
        // Optional: Add any additional JavaScript here if needed
        console.log('Materi viewer loaded');
    </script>

</body>
</html>