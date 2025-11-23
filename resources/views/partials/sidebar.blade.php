{{-- resources/views/partials/sidebar.blade.php (FIXED with Role Relationship) --}}
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        {{-- Dashboard - Untuk Semua Role --}}
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
            <div class="nav-indicator"></div>
        </a>
        
        {{-- ✅ ADMIN ONLY: Administrator Menu --}}
        @if(Auth::check() && Auth::user()->getRoleName() === 'admin')
        <a href="/administrator" class="nav-item {{ request()->is('administrator*') ? 'active' : '' }}">
            <i class="fas fa-user-shield"></i>
            <span>Administrator</span>
            <div class="nav-indicator"></div>
        </a>
        @endif
        
        {{-- ✅ MATERI - Untuk Admin & Mahasiswa (bukan Dosen sendirian) --}}
        @if(Auth::check() && in_array(Auth::user()->getRoleName(), ['admin', 'mahasiswa']))
        <a href="/materi" class="nav-item {{ request()->is('materi*') && !request()->is('administrator*') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            <span>Materi Kelas</span>
            <div class="nav-indicator"></div>
        </a>
        @endif
        
        {{-- ✅ DOSEN: Kelola Materi (Upload & Management) --}}
        @if(Auth::check() && Auth::user()->getRoleName() === 'dosen')
        <a href="/materi" class="nav-item {{ request()->is('materi*') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i>
            <span>Kelola Materi</span>
            <div class="nav-indicator"></div>
        </a>
        @endif
        
        {{-- Peserta - Untuk Semua Role --}}
        <a href="/peserta" class="nav-item {{ request()->is('peserta') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Peserta</span>
            <span class="nav-badge">32</span>
            <div class="nav-indicator"></div>
        </a>
        
        {{-- Tugas - Untuk Semua Role --}}
        <a href="/tugas" class="nav-item {{ request()->is('tugas*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            <span>Tugas</span>
        </a>
        
        {{-- Jadwal - Untuk Semua Role --}}
        <a href="/jadwal" class="nav-item {{ request()->is('jadwal*') ? 'active' : '' }}">
            <i class="fas fa-calendar"></i>
            <span>Jadwal</span>
            <div class="nav-indicator"></div>
        </a>
        
        {{-- Pengaturan - Untuk Semua Role --}}
        <a href="/pengaturan" class="nav-item {{ request()->is('pengaturan') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Pengaturan</span>
            <div class="nav-indicator"></div>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="theme-toggle">
            <i class="fas fa-moon"></i>
            <span>Mode Gelap</span>
            <label class="switch">
                <input type="checkbox" id="themeToggle">
                <span class="slider"></span>
            </label>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>