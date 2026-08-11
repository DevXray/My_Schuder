/**
 * Dark Mode Manager
 * My Schuder - Theme Toggle System
 */

class DarkModeManager {
    constructor() {
        this.themeKey = 'my_schuder_theme';
        this.currentTheme = this.getTheme();
        this.init();
    }

    /**
     * Initialize dark mode
     */
    init() {
        // Apply saved theme on page load
        this.applyTheme(this.currentTheme);
        
        // Setup toggle buttons
        this.setupToggleButtons();
        
        // Listen for system theme changes
        this.watchSystemTheme();
    }

    /**
     * Get current theme from localStorage or system preference
     */
    getTheme() {
        // Check localStorage first
        const savedTheme = localStorage.getItem(this.themeKey);
        if (savedTheme) {
            return savedTheme;
        }

        // Check system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }

        return 'light';
    }

    /**
     * Apply theme to document
     */
    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        this.currentTheme = theme;
        localStorage.setItem(this.themeKey, theme);
        
        // Update toggle button icon
        this.updateToggleIcon();
        
        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { theme } 
        }));
        
        console.log(`Theme changed to: ${theme}`);
    }

    /**
     * Toggle between light and dark mode
     */
    toggle() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
        
        // Add animation
        this.addToggleAnimation();
    }

    /**
     * Setup toggle button event listeners
     */
    setupToggleButtons() {
        const toggleButtons = document.querySelectorAll('.theme-toggle, #themeToggle');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', () => {
                this.toggle();
            });
        });
    }

    /**
     * Update toggle button icon
     */
    updateToggleIcon() {
        const icons = document.querySelectorAll('.theme-toggle i, #themeToggle i');
        
        icons.forEach(icon => {
            if (this.currentTheme === 'dark') {
                icon.className = 'fas fa-sun'; // Sun icon for light mode
            } else {
                icon.className = 'fas fa-moon'; // Moon icon for dark mode
            }
        });
    }

    /**
     * Watch for system theme changes
     */
    watchSystemTheme() {
        if (window.matchMedia) {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            darkModeQuery.addEventListener('change', (e) => {
                // Only auto-change if user hasn't manually set a preference
                if (!localStorage.getItem(this.themeKey)) {
                    const newTheme = e.matches ? 'dark' : 'light';
                    this.applyTheme(newTheme);
                }
            });
        }
    }

    /**
     * Add animation when toggling theme
     */
    addToggleAnimation() {
        document.body.style.transition = 'none';
        
        setTimeout(() => {
            document.body.style.transition = '';
        }, 100);
    }

    /**
     * Force light mode
     */
    setLight() {
        this.applyTheme('light');
    }

    /**
     * Force dark mode
     */
    setDark() {
        this.applyTheme('dark');
    }

    /**
     * Reset to system preference
     */
    resetToSystem() {
        localStorage.removeItem(this.themeKey);
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        this.applyTheme(systemTheme);
    }
}

// Initialize Dark Mode Manager
let darkModeManager;

document.addEventListener('DOMContentLoaded', function() {
    darkModeManager = new DarkModeManager();
    
    // Make it globally accessible for debugging
    window.darkMode = darkModeManager;
    
    console.log('✅ Dark Mode Manager initialized');
});

// Keyboard shortcut: Ctrl/Cmd + Shift + D to toggle dark mode
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        if (window.darkMode) {
            window.darkMode.toggle();
            
            // Show toast notification
            showThemeToast();
        }
    }
});

/**
 * Show toast notification when theme changes
 */
function showThemeToast() {
    const theme = darkModeManager.currentTheme;
    const message = theme === 'dark' ? '🌙 Dark Mode Aktif' : '☀️ Light Mode Aktif';
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'theme-toast';
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: ${theme === 'dark' ? '#1e293b' : '#ffffff'};
        color: ${theme === 'dark' ? '#f1f5f9' : '#1e293b'};
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        font-weight: 600;
        z-index: 99999;
        animation: slideIn 0.3s ease;
    `;
    
    // Add to document
    document.body.appendChild(toast);
    
    // Remove after 2 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 2000);
}

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
