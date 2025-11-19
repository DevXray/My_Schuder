<!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="header-left">
                <button class="menu-btn" id="menuBtn" aria-label="Toggle Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">
                        <img class= "logo-icon" src="{{ asset('assets/logo_akademik_hd.png') }}" alt="Logo My Schuder" />
                    <div class="logo-text">
                        <h1>My Schuder</h1>
                        <p>Portal Pembelajaran</p>
                    </div>
                </div>
            </div>

            <div class="header-center">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari materi, tugas, atau diskusi...">
                    <button class="search-clear" id="searchClear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="header-right">
                <button class="notification-btn" id="notificationBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </button>
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <p class="user-name">{{ Auth::user()->name }}</p>
                        <p class="user-role">{{ Auth::user()->role ?? 'Siswa' }}</p>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>
    </header>