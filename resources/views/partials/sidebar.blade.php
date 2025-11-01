<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
            <div class="nav-indicator"></div>
        </a>
        
        <a href="/materi" class="nav-item {{ request()->is('materi') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            <span>Materi Kelas</span>
            <div class="nav-indicator"></div>
        </a>
        
        <a href="/peserta" class="nav-item {{ request()->is('peserta') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Peserta</span>
            <span class="nav-badge">32</span>
            <div class="nav-indicator"></div>
        </a>
        
        <a href="/tugas" class="nav-item {{ request()->is('tugas') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            <span>Tugas</span>
            <span class="nav-badge orange">5</span>
            <div class="nav-indicator"></div>
        </a>
        
        <a href="/jadwal" class="nav-item {{ request()->is('jadwal') ? 'active' : '' }}">
            <i class="fas fa-calendar"></i>
            <span>Jadwal</span>
            <div class="nav-indicator"></div>
        </a>
        
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