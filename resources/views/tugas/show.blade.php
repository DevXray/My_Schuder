<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tugas->judul }} - Detail Tugas</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/dashboard.css', 'resources/css/pages.css', 'resources/js/app.js'])
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
                        <p>Detail tugas dan pengumpulan</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Deadline Warning jika sudah lewat -->
        @if($isExpired)
        <div class="alert alert-error" style="margin-bottom: 2rem;">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Deadline Terlewat!</strong> Tugas ini tidak dapat lagi dikumpulkan karena deadline sudah berakhir pada {{ $tugas->deadline->format('d M Y, H:i') }} WIB.
        </div>
        @endif

        <!-- Tugas Detail Card -->
        <section class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Materi</h3>
                        <p style="font-weight: 600;">{{ $tugas->materi->judul ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Dosen</h3>
                        <p style="font-weight: 600;">{{ $tugas->dosen->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Diberikan</h3>
                        <p style="font-weight: 600;">{{ $tugas->tanggal_diberikan->format('d M Y') }}</p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Deadline</h3>
                        <p style="font-weight: 600; color: {{ $isExpired ? '#ef4444' : '#10b981' }};">
                            {{ $tugas->deadline->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Bobot</h3>
                        <p style="font-weight: 600;">{{ $tugas->bobot }}%</p>
                    </div>
                </div>
                
                @if($tugas->deskripsi)
                <div style="padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <h3 style="margin-bottom: 0.75rem; font-weight: 600;">Deskripsi Tugas</h3>
                    <p style="color: #374151; line-height: 1.6;">{{ $tugas->deskripsi }}</p>
                </div>
                @endif
                
                @if($tugas->file_soal)
                <div style="margin-top: 1.5rem;">
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

        <!-- Pengumpulan Section -->
        @if($pengumpulan)
        <!-- Sudah Mengumpulkan -->
        <section class="card">
            <div class="card-header">
                <h3 style="color: #10b981;">
                    <i class="fas fa-check-circle"></i> Status: Sudah Dikumpulkan
                </h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Waktu Pengumpulan</h3>
                        <p style="font-weight: 600;">{{ $pengumpulan->waktu_pengumpulan->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Status</h3>
                        @php
                            $isOnTime = $pengumpulan->waktu_pengumpulan->lte($tugas->deadline);
                        @endphp
                        <p style="font-weight: 600; color: {{ $isOnTime ? '#10b981' : '#ef4444' }};">
                            <i class="fas fa-{{ $isOnTime ? 'check' : 'clock' }}"></i>
                            {{ $isOnTime ? 'Tepat Waktu' : 'Terlambat' }}
                        </p>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">File Dikumpulkan</h3>
                        <p style="font-weight: 600;">
                            <i class="fas fa-file-pdf" style="color: #3b82f6;"></i>
                            {{ basename($pengumpulan->file_tugas) }}
                        </p>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <a href="{{ asset('storage/' . $pengumpulan->file_tugas) }}" 
                       class="btn-action primary" 
                       download
                       style="text-decoration: none;">
                        <i class="fas fa-download"></i> Download File
                    </a>
                    
                    @if(!$isExpired)
                    <button class="btn-action secondary" onclick="openResubmitModal()">
                        <i class="fas fa-edit"></i> Edit Pengumpulan
                    </button>
                    @endif
                </div>
            </div>
        </section>
        
        @elseif(!$isExpired)
        <!-- Belum Mengumpulkan & Deadline Belum Lewat -->
        <section class="card">
            <div class="card-header">
                <h3 style="color: #f59e0b;">
                    <i class="fas fa-clock"></i> Status: Belum Dikumpulkan
                </h3>
            </div>
            <div class="card-body">
                <p style="margin-bottom: 1.5rem; color: #6b7280;">
                    Silakan upload file jawaban tugas Anda sebelum deadline.
                </p>
                
                <!-- Form Upload -->
                <form action="{{ route('tugas.submit', $tugas->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      id="submitForm">
                    @csrf
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-file-upload"></i> Upload File Jawaban <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="file" 
                               name="file_jawaban" 
                               accept=".pdf,.doc,.docx,.zip" 
                               required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                        <small style="display: block; margin-top: 0.5rem; color: #6b7280;">
                            Format: PDF, DOC, DOCX, ZIP (Maksimal 20MB)
                        </small>
                        @error('file_jawaban')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-comment"></i> Catatan (Opsional)
                        </label>
                        <textarea name="catatan" 
                                  rows="3" 
                                  maxlength="1000"
                                  placeholder="Tambahkan catatan jika diperlukan..."
                                  style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; resize: vertical;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-action primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Kumpulkan Tugas
                    </button>
                </form>
            </div>
        </section>
        
        @else
        <!-- Belum Mengumpulkan & Deadline Sudah Lewat -->
        <section class="card">
            <div class="card-header">
                <h3 style="color: #ef4444;">
                    <i class="fas fa-times-circle"></i> Status: Tidak Dikumpulkan
                </h3>
            </div>
            <div class="card-body">
                <p style="color: #6b7280;">
                    Anda tidak dapat lagi mengumpulkan tugas ini karena deadline telah berakhir.
                </p>
            </div>
        </section>
        @endif

    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>
    @include('partials.loadingscreen')
    
    @if($pengumpulan && !$isExpired)
    <script>
        function openResubmitModal() {
            if (confirm('Apakah Anda yakin ingin mengedit pengumpulan? File lama akan diganti dengan file baru.')) {
                // Create modal or redirect to resubmit page
                window.location.href = '#submitForm';
            }
        }
    </script>
    @endif
</body>
</html>