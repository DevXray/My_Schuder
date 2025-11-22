<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peserta - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/dashboard.css', 'resources/css/pages.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-users"></i> Peserta Kelas</h1>
                    <p>Kelola dan lihat daftar peserta</p>
                </div>
            </div>
        </section>

        <div style="padding: 2rem; text-align: center;">
            <i class="fas fa-users" style="font-size: 4rem; color: #e0e0e0;"></i>
            <h3 style="margin-top: 1rem; color: #666;">Halaman Peserta</h3>
            <p style="color: #999;">Fitur ini sedang dalam pengembangan</p>
        </div>
    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>
    @include('partials.loadingscreen')
</body>
</html>