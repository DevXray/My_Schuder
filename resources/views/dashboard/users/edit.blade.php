{{-- resources/views/dashboard/users/edit.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit User - My Schuder</title>
    
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
                    <h1><i class="fas fa-user-edit"></i> Edit User</h1>
                    <p>Update informasi pengguna</p>
                </div>
            </div>
        </section>

        <section class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-user"></i> Nama Lengkap <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="Nama lengkap pengguna">
                        @error('name')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-envelope"></i> Email <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="email@example.com">
                        @error('email')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Role Selection --}}
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-user-tag"></i> Role <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="role_name" required
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role_name', $user->getRoleName()) === 'admin' ? 'selected' : '' }}>
                                Admin (Akses Penuh)
                            </option>
                            <option value="dosen" {{ old('role_name', $user->getRoleName()) === 'dosen' ? 'selected' : '' }}>
                                Dosen (Pengajar)
                            </option>
                            <option value="mahasiswa" {{ old('role_name', $user->getRoleName()) === 'mahasiswa' ? 'selected' : '' }}>
                                Mahasiswa (Peserta Didik)
                            </option>
                        </select>
                        @error('role_name')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password (Optional) --}}
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

                    {{-- Password Confirmation --}}
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <i class="fas fa-lock"></i> Konfirmasi Password Baru
                        </label>
                        <input type="password" name="password_confirmation"
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                               placeholder="Ulangi password">
                    </div>

                    {{-- Action Buttons --}}
                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" class="btn-action primary" style="flex: 1; padding: 0.875rem; border: none; cursor: pointer; border-radius: 8px; font-weight: 600;">
                            <i class="fas fa-save"></i> Update User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn-action secondary" style="flex: 1; text-decoration: none; text-align: center; padding: 0.875rem; border-radius: 8px; font-weight: 600; display: inline-block;">
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