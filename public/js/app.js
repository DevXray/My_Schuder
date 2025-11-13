/**
 * app.js - Main Application Initializer
 * Import dan initialize semua komponen yang diperlukan
 */

// Import semua komponen
import BaseComponent from './components/BaseComponent.js';
import NotificationService from './services/NotificationService.js';
import ApiService from './services/ApiService.js';
import ChatbotUI from './components/ChatbotUI.js';
import SearchComponent from './components/SearchComponent.js';
import MateriPage from './pages/MateriPage.js';
import JadwalPage from './pages/JadwalPage.js';
import TugasPage from './pages/TugasPage.js';

/**
 * App - Main Application Class
 */
class App extends BaseComponent {
  constructor() {
    super();
    
    this.currentPage = this.detectCurrentPage();
    this.components = {};
    
    this.init();
  }

  /**
   * Initialize application
   */
  init() {
    this.showLoadingScreen();
    this.setupGlobalComponents();
    this.setupPageSpecificComponents();
    this.setupGlobalEventListeners();
    
    window.addEventListener('load', () => {
      this.hideLoadingScreen();
    });
  }

  /**
   * Detect current page
   */
  detectCurrentPage() {
    const path = window.location.pathname;
    
    if (path.includes('/materi')) return 'materi';
    if (path.includes('/jadwal')) return 'jadwal';
    if (path.includes('/tugas')) return 'tugas';
    if (path.includes('/nilai')) return 'nilai';
    if (path.includes('/peserta')) return 'peserta';
    
    return 'dashboard';
  }

  /**
   * Setup global components (available on all pages)
   */
  setupGlobalComponents() {
    // Sidebar
    this.setupSidebar();
    
    // Chatbot
    this.components.chatbot = new ChatbotUI();
    
    // Theme toggle
    this.setupThemeToggle();
    
    // Notification button
    this.setupNotificationButton();
    
    // Stats animation (if exists)
    this.setupStatsAnimation();
  }

  /**
   * Setup page-specific components
   */
  setupPageSpecificComponents() {
    switch (this.currentPage) {
      case 'materi':
        this.components.materiPage = new MateriPage();
        break;
      case 'jadwal':
        this.components.jadwalPage = new JadwalPage();
        break;
      case 'tugas':
        this.components.tugasPage = new TugasPage();
        break;
      // Add other pages as needed
    }
  }

  /**
   * Setup sidebar
   */
  setupSidebar() {
    const menuBtn = this.getElement('#menuBtn');
    const sidebar = this.getElement('#sidebar');
    const overlay = this.getElement('#overlay');

    if (menuBtn && sidebar && overlay) {
      this.addEventListener(menuBtn, 'click', () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
      });

      this.addEventListener(overlay, 'click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
      });
    }

    // Navigation items
    const navItems = this.getElements('.nav-item:not(.logout)');
    navItems.forEach(item => {
      this.addEventListener(item, 'click', (e) => {
        const href = item.getAttribute('href');
        const isAbsolute = href && href.startsWith('/');

        // Update active state
        navItems.forEach(nav => nav.classList.remove('active'));
        item.classList.add('active');

        // Close sidebar on mobile
        if (window.innerWidth <= 768 && sidebar && overlay) {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
        }

        // Allow default navigation for absolute paths
        if (isAbsolute) {
          return;
        }

        e.preventDefault();
        const page = item.getAttribute('data-page');
        if (page) {
          console.log(`Navigate to: ${page}`);
        }
      });
    });

    // Auto-close on resize
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        if (window.innerWidth > 768 && sidebar && overlay) {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
        }
      }, 250);
    });
  }

  /**
   * Setup theme toggle
   */
  setupThemeToggle() {
    const themeToggle = this.getElement('#themeToggle');
    
    if (themeToggle) {
      this.addEventListener(themeToggle, 'change', () => {
        if (themeToggle.checked) {
          NotificationService.info("Fitur dark mode sedang dalam pengembangan. Coming soon! 🌙");
          
          // Open chatbot if not active
          const chatbot = this.getElement('#chatbot');
          if (chatbot && !chatbot.classList.contains('active')) {
            chatbot.classList.add('active');
          }
          
          setTimeout(() => {
            themeToggle.checked = false;
          }, 500);
        }
      });
    }
  }

  /**
   * Setup notification button
   */
  setupNotificationButton() {
    const notificationBtn = this.getElement('#notificationBtn');
    
    if (notificationBtn) {
      this.addEventListener(notificationBtn, 'click', () => {
        const message = "📬 Anda memiliki 3 notifikasi baru:\n\n1. ⚠ Deadline tugas: 3 hari lagi\n2. 📚 Materi baru telah ditambahkan\n3. 🎉 Selamat! Anda mencapai progress 75%";
        
        if (this.components.chatbot) {
          this.components.chatbot.addMessage(message, false);
          this.components.chatbot.open();
        }
      });
    }
  }

  /**
   * Setup stats animation
   */
  setupStatsAnimation() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          if (entry.target.classList.contains('stat-number')) {
            const target = parseInt(entry.target.getAttribute('data-target'));
            this.animateValue(entry.target, 0, target, 1000);
            observer.unobserve(entry.target);
          }

          if (entry.target.classList.contains('progress-fill')) {
            const progress = entry.target.getAttribute('data-progress');
            setTimeout(() => {
              entry.target.style.width = progress + '%';
            }, 200);
            observer.unobserve(entry.target);
          }
        }
      });
    }, observerOptions);

    this.getElements('.stat-number').forEach(stat => {
      observer.observe(stat);
    });

    this.getElements('.progress-fill').forEach(progress => {
      observer.observe(progress);
    });
  }

  /**
   * Animate number value
   */
  animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
      current += increment;
      if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
        element.textContent = end;
        clearInterval(timer);
      } else {
        element.textContent = Math.floor(current);
      }
    }, 16);
  }

  /**
   * Setup global event listeners
   */
  setupGlobalEventListeners() {
    // Schedule join buttons
    this.addEventListener(document, 'click', (e) => {
      const joinBtn = e.target.closest('.schedule-join');
      if (joinBtn) {
        e.stopPropagation();
        this.handleScheduleJoin(joinBtn);
      }
    });

    // Easter egg - Konami code
    this.setupKonamiCode();

    // Console messages
    this.showConsoleBanner();
  }

  /**
   * Handle schedule join
   */
  handleScheduleJoin(btn) {
    const scheduleItem = btn.closest('.schedule-item');
    const className = scheduleItem.querySelector('h4').textContent;

    btn.textContent = 'Joining...';
    btn.disabled = true;

    setTimeout(() => {
      btn.textContent = 'Joined ✓';
      btn.style.opacity = '0.7';

      setTimeout(() => {
        NotificationService.success(`✅ Anda berhasil join kelas "${className}". Selamat belajar! 📚`);
        
        if (this.components.chatbot) {
          this.components.chatbot.open();
        }
      }, 500);
    }, 1500);
  }

  /**
   * Setup Konami code easter egg
   */
  setupKonamiCode() {
    let konamiCode = [];
    const konamiPattern = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 
                          'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];

    this.addEventListener(document, 'keydown', (e) => {
      konamiCode.push(e.key);
      konamiCode = konamiCode.slice(-10);

      if (konamiCode.join('') === konamiPattern.join('')) {
        if (this.components.chatbot) {
          this.components.chatbot.addMessage("🎮 Easter Egg Found! Anda menemukan kode rahasia! Selamat! 🎉✨", false);
          this.components.chatbot.open();
        }
        konamiCode = [];
      }
    });
  }

  /**
   * Show console banner
   */
  showConsoleBanner() {
    console.log("%c🎓 Selamat datang di My Schuder!", "color: #3b82f6; font-size: 24px; font-weight: bold;");
    console.log("%c✨ Portal Pembelajaran Modern dengan Gemini AI", "color: #f97316; font-size: 16px; font-weight: bold;");
    console.log("%c💡 Tip: Coba chat dengan AI assistant untuk bantuan cepat!", "color: #10b981; font-size: 14px;");
  }

  /**
   * Show loading screen
   */
  showLoadingScreen() {
    const loadingScreen = this.getElement('#loadingScreen');
    if (loadingScreen) {
      loadingScreen.style.display = 'flex';
      loadingScreen.classList.remove('hidden');
    }
  }

  /**
   * Hide loading screen
   */
  hideLoadingScreen() {
    const loadingScreen = this.getElement('#loadingScreen');
    if (loadingScreen && !loadingScreen.classList.contains('hidden')) {
      loadingScreen.classList.add('hidden');
      setTimeout(() => {
        loadingScreen.style.display = 'none';
      }, 500);
    }
  }

  /**
   * Cleanup on page unload
   */
  destroy() {
    this.cleanup();
    Object.values(this.components).forEach(component => {
      if (component.cleanup) {
        component.cleanup();
      }
    });
  }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  window.mySchuderApp = new App();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
  if (window.mySchuderApp) {
    window.mySchuderApp.destroy();
  }
});

export default App;