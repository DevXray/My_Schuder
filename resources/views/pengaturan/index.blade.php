<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/js/app.js'])
</head>
<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
        <!-- Page Header -->
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-cog"></i> Pengaturan</h1>
                    <p>Kelola preferensi dan konfigurasi aplikasi Anda</p>
                </div>
            </div>
        </section>

        <!-- Settings Content -->
        <section class="settings-container" style="max-width: 800px; margin: 2rem auto;">
            
            <!-- Profile Settings -->
            <div class="settings-section" style="background: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem;">
                    <i class="fas fa-user" style="color: #3b82f6;"></i>
                    Profil Saya
                </h2>
                <p style="color: #666; margin-bottom: 1rem;">
                    Kelola informasi profil dan akun Anda
                </p>
                <a href="{{ route('profile.edit') }}" style="display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; cursor: pointer;">
                    <i class="fas fa-edit"></i> Edit Profil
                </a>
            </div>

            <!-- Notification Settings -->
            <div class="settings-section" style="background: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem;">
                    <i class="fas fa-bell" style="color: #f59e0b;"></i>
                    Notifikasi
                </h2>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <span>Email Notifikasi</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <span>Notifikasi Tugas</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span>Notifikasi Materi Baru</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="settings-section" style="background: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem;">
                    <i class="fas fa-palette" style="color: #10b981;"></i>
                    Tampilan
                </h2>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span>Mode Gelap</span>
                    <label class="switch">
                        <input type="checkbox" id="themeToggle">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="settings-section" style="background: #fee2e2; padding: 2rem; border-radius: 8px; border-left: 4px solid #ef4444;">
                <h2 style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem; color: #dc2626;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Zona Berbahaya
                </h2>
                <button onclick="if(confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')) { document.getElementById('delete-form').submit(); }" style="padding: 10px 20px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    <i class="fas fa-trash"></i> Hapus Akun Saya
                </button>
                <form id="delete-form" action="{{ route('profile.destroy') }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

        </section>
    </main>

    <style>
        .settings-section {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #3b82f6;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</body>
</html>
