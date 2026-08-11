{{-- resources/views/partials/header.blade.php (UPDATED WITH PROFILE DROPDOWN) --}}
<!-- Header -->
<header class="header">
    <div class="header-content">
        <div class="header-left">
            <button class="menu-btn" id="menuBtn" aria-label="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">
                <img class="logo-icon" src="{{ asset('assets/logo_akademik_hd.png') }}" alt="Logo My Schuder" />
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
            <!-- Dark Mode Toggle -->
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle Dark Mode" title="Toggle Dark Mode (Ctrl+Shift+D)">
                <i class="fas fa-moon"></i>
            </button>
            
            <button class="notification-btn" id="notificationBtn" aria-label="Notifications">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </button>
            
            <!-- User Profile Dropdown -->
            <div class="user-profile-dropdown">
                <button class="user-profile" id="userProfileBtn">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <p class="user-name">{{ Auth::user()->name }}</p>
                        <p class="user-role">{{ Auth::user()->getRoleDisplayName() }}</p>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="profile-dropdown-menu" id="profileDropdownMenu">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="dropdown-info">
                            <p class="dropdown-name">{{ Auth::user()->name }}</p>
                            <p class="dropdown-email">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    
                    <div class="dropdown-divider"></div>
                    
                    <div class="dropdown-items">
                        <a href="/profile" class="dropdown-item">
                            <i class="fas fa-user-circle"></i>
                            <span>Profil Saya</span>
                        </a>
                        <a href="/pengaturan" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </a>
                        <a href="/dashboard" class="dropdown-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                        <button type="button" class="dropdown-item" id="dropdownThemeToggle">
                            <i class="fas fa-moon"></i>
                            <span>Mode Gelap</span>
                        </button>
                    </div>
                    
                    <div class="dropdown-divider"></div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="dropdown-logout-form">
                        @csrf
                        <button type="submit" class="dropdown-item logout-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
/* User Profile Dropdown Styles */
.user-profile-dropdown {
    position: relative;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-profile:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.user-profile .fa-chevron-down {
    transition: transform 0.3s ease;
    color: white;
}

.user-profile.active .fa-chevron-down {
    transform: rotate(180deg);
}

/* Dropdown Menu */
.profile-dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 300px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    overflow: hidden;
}

.profile-dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Dropdown Header */
.dropdown-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #082a98 0%, #0a3ec7 100%);
}

.dropdown-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #F67C1F 0%, #ff9d4d 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(246, 124, 31, 0.4);
}

.dropdown-info {
    flex: 1;
}

.dropdown-name {
    font-weight: 700;
    font-size: 1rem;
    color: white;
    margin: 0 0 0.25rem 0;
}

.dropdown-email {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
}

/* Dropdown Divider */
.dropdown-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 0.5rem 0;
}

/* Dropdown Items */
.dropdown-items {
    padding: 0.5rem;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    color: #475569;
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    font-size: 0.95rem;
}

.dropdown-item:hover {
    background: #f1f5f9;
    color: #082a98;
    transform: translateX(5px);
}

.dropdown-item i {
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

.logout-item {
    color: #dc2626;
}

.logout-item:hover {
    background: #fee2e2;
    color: #dc2626;
}

.dropdown-logout-form {
    padding: 0.5rem;
}

/* Animation for dropdown */
@keyframes dropdownSlide {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .profile-dropdown-menu {
        width: 280px;
    }
    
    .user-info {
        display: none;
    }
    
    .user-profile {
        padding: 0.5rem;
    }
}
</style>

<script>
// Profile Dropdown Toggle
document.addEventListener('DOMContentLoaded', function() {
    const profileBtn = document.getElementById('userProfileBtn');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    
    if (profileBtn && dropdownMenu) {
        // Toggle dropdown
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
            profileBtn.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                profileBtn.classList.remove('active');
            }
        });
        
        // Close dropdown when pressing Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('show');
                profileBtn.classList.remove('active');
            }
        });
        
        // Theme toggle in dropdown
        const dropdownThemeToggle = document.getElementById('dropdownThemeToggle');
        if (dropdownThemeToggle) {
            dropdownThemeToggle.addEventListener('click', function() {
                if (window.darkMode) {
                    window.darkMode.toggle();
                    updateThemeToggleText();
                }
            });
        }
        
        // Update theme toggle text based on current theme
        function updateThemeToggleText() {
            if (dropdownThemeToggle && window.darkMode) {
                const icon = dropdownThemeToggle.querySelector('i');
                const text = dropdownThemeToggle.querySelector('span');
                
                if (window.darkMode.currentTheme === 'dark') {
                    icon.className = 'fas fa-sun';
                    text.textContent = 'Mode Terang';
                } else {
                    icon.className = 'fas fa-moon';
                    text.textContent = 'Mode Gelap';
                }
            }
        }
        
        // Listen for theme changes
        window.addEventListener('themeChanged', function(e) {
            updateThemeToggleText();
        });
        
        // Initial update
        setTimeout(updateThemeToggleText, 100);
    }
});
</script>