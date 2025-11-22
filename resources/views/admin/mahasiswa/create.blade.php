{{-- resources/views/administrator/mahasiswa/create.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Mahasiswa - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/js/app.js'])
</head>
<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-user-plus"></i> Tambah Mahasiswa</h1>
                    <p>Tambahkan mahasiswa baru ke sistem</p>
                </div>
            </div>
        </section>

        <section class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="card-body">
                <form action="{{ route('administrator.mahasiswa.store') }}" method="POST">
                    @csrf

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-id-card"></i> NIM <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="nim" value="{{ old('nim') }}" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="Contoh: 2021010001">
                        @error('nim')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-user"></i> Nama Lengkap <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="Nama lengkap mahasiswa">
                        @error('nama')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-envelope"></i> Email <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="email@example.com">
                        @error('email')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-lock"></i> Password <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="password" name="password" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-graduation-cap"></i> Jurusan
                        </label>
                        <input type="text" name="jurusan" value="{{ old('jurusan') }}"
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="Contoh: Teknik Informatika">
                        @error('jurusan')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-users"></i> Kelas
                        </label>
                        <input type="text" name="kelas" value="{{ old('kelas') }}"
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="Contoh: TI-3A">
                        @error('kelas')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" class="btn-action primary" style="flex: 1;">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('administrator.mahasiswa.index') }}" class="btn-action secondary" style="flex: 1; text-decoration: none; text-align: center;">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>
</body>
</html>