<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Dosen - My Schuder</title>
    
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
                    <h1><i class="fas fa-user-edit"></i> Edit Dosen</h1>
                    <p>Update informasi dosen</p>
                </div>
            </div>
        </section>

        @if ($errors->any())
            <div style="margin: 20px; padding: 15px; background: #f8d7da; color: #721c24; border-radius: 8px;">
                <strong>Terjadi kesalahan:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="card-body">
                <form action="{{ route('dosen.update', $dosen->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-id-card"></i> NIDN <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="nidn" value="{{ old('nidn', $dosen->nidn) }}" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                        @error('nidn')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-user"></i> Nama Lengkap <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama', $dosen->nama) }}" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                        @error('nama')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-envelope"></i> Email <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $dosen->email) }}" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                        @error('email')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-lock"></i> Password Baru (Kosongkan jika tidak diubah)
                        </label>
                        <input type="password" name="password"
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" style="flex: 1; padding: 0.875rem; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save"></i> Update Dosen
                        </button>
                        <a href="{{ route('dosen.index') }}" style="flex: 1; text-decoration: none; text-align: center; padding: 0.875rem; background: #6b7280; color: white; border-radius: 8px; font-weight: 600; display: inline-block;">
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