<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tugas->judul }} - Pengumpulan</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/dashboard.css', 'resources/css/pages.css', 'resources/js/app.js'])
    
    <style>
        .submission-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .submission-table thead {
            background: #f9fafb;
        }
        
        .submission-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .submission-table td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .submission-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-badge.ontime {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-badge.late {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
        <!-- Page Header -->
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <a href="{{ route('tugas.index') }}" class="back-button" style="margin-right: 1rem;">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1><i class="fas fa-clipboard-list"></i> {{ $tugas->judul }}</h1>
                        <p>Daftar pengumpulan tugas dari mahasiswa</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tugas Info Card -->
        <section class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Materi</h3>
                        <p style="font-weight: 600;">{{ $tugas->materi->judul ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Diberikan</h3>
                        <p style="font-weight: 600;">{{ $tugas->tanggal_diberikan->format('d M Y') }}</p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Deadline</h3>
                        <p style="font-weight: 600; color: {{ \Carbon\Carbon::now()->gt($tugas->deadline) ? '#ef4444' : '#10b981' }};">
                            {{ $tugas->deadline->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Bobot</h3>
                        <p style="font-weight: 600;">{{ $tugas->bobot }}%</p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Total Pengumpulan</h3>
                        <p style="font-weight: 600; color: #3b82f6;">{{ $pengumpulanList->count() }} mahasiswa</p>
                    </div>
                </div>
                
                @if($tugas->deskripsi)
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Deskripsi Tugas</h3>
                    <p style="color: #374151; line-height: 1.6;">{{ $tugas->deskripsi }}</p>
                </div>
                @endif
                
                @if($tugas->file_soal)
                <div style="margin-top: 1rem;">
                    <a href="{{ asset('storage/' . $tugas->file_soal) }}" 
                       class="btn-action secondary" 
                       download
                       style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                        <i class="fas fa-download"></i> Download File Soal
                    </a>
                </div>
                @endif
            </div>
        </section>

        <!-- Pengumpulan List -->
        <section class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Daftar Pengumpulan ({{ $pengumpulanList->count() }})</h3>
            </div>
            <div class="card-body" style="padding: 0; overflow-x: auto;">
                @if($pengumpulanList->count() > 0)
                <table class="submission-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Waktu Pengumpulan</th>
                            <th>Status</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengumpulanList as $index => $pengumpulan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 600;">{{ $pengumpulan->mahasiswa->nim ?? '-' }}</td>
                            <td>{{ $pengumpulan->mahasiswa->nama ?? '-' }}</td>
                            <td>
                                <div>{{ $pengumpulan->waktu_pengumpulan->format('d M Y') }}</div>
                                <small style="color: #6b7280;">{{ $pengumpulan->waktu_pengumpulan->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                @php
                                    $isOnTime = $pengumpulan->waktu_pengumpulan->lte($tugas->deadline);
                                @endphp
                                <span class="status-badge {{ $isOnTime ? 'ontime' : 'late' }}">
                                    <i class="fas fa-{{ $isOnTime ? 'check' : 'clock' }}"></i>
                                    {{ $isOnTime ? 'Tepat Waktu' : 'Terlambat' }}
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-file-pdf" style="color: #3b82f6;"></i>
                                {{ basename($pengumpulan->file_tugas) }}
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $pengumpulan->file_tugas) }}" 
                                   class="btn-action primary" 
                                   download
                                   style="padding: 0.5rem 1rem; font-size: 0.875rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div style="padding: 4rem 2rem; text-align: center; color: #9ca3af;">
                    <i class="fas fa-inbox" style="font-size: 4rem; margin-bottom: 1rem; display: block;"></i>
                    <h3 style="color: #6b7280; margin-bottom: 0.5rem;">Belum Ada Pengumpulan</h3>
                    <p>Belum ada mahasiswa yang mengumpulkan tugas ini.</p>
                </div>
                @endif
            </div>
        </section>
    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>
    @include('partials.loadingscreen')
</body>
</html>