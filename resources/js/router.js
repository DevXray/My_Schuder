// ============================================
// ROUTER.JS - Client-Side Routing System
// ============================================

import { NotificationManager } from './core.js';

// ========== PAGE LOADER ==========
class PageLoader {
  constructor() {
    this.cache = new Map();
    this.currentController = null;
  }

  async loadPage(path) {
    // Check cache
    if (this.cache.has(path)) {
      console.log('✅ Loading from cache:', path);
      return this.cache.get(path);
    }

    try {
      console.log('🔄 Fetching page:', path);
      
      const response = await fetch(path, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'text/html',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      if (!response.ok) {
        console.error('❌ HTTP Error:', response.status, response.statusText);
        throw new Error(`HTTP ${response.status}`);
      }

      const html = await response.text();
      console.log('✅ Received HTML length:', html.length);
      
      // Extract main content only
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const mainContent = doc.querySelector('.main-content');
      
      if (!mainContent) {
        console.error('❌ Main content not found in response');
        console.log('Available content:', doc.body.innerHTML.substring(0, 200));
        throw new Error('Main content not found');
      }

      const contentHTML = mainContent.innerHTML;
      console.log('✅ Extracted content length:', contentHTML.length);
      
      // Cache for 5 minutes
      this.cache.set(path, contentHTML);
      setTimeout(() => this.cache.delete(path), 5 * 60 * 1000);

      return contentHTML;
    } catch (error) {
      console.error('💥 Page load error:', error);
      throw error;
    }
  }

  clearCache() {
    this.cache.clear();
  }

  async loadPageModule(pageName) {
    // Cleanup previous controller
    if (this.currentController) {
      this.currentController.destroy?.();
      this.currentController = null;
    }

    // Lazy load page-specific module
    switch(pageName) {
      case 'materi':
        const { initMateriApp } = await import('./materi.js');
        this.currentController = initMateriApp();
        break;
      case 'tugas':
        const { initTugasApp } = await import('./tugas.js');
        this.currentController = initTugasApp();
        break;
      case 'jadwal':
        const { initJadwalApp } = await import('./jadwal.js');
        this.currentController = initJadwalApp();
        break;
      default:
        console.log('No specific module for:', pageName);
    }
  }
}

// ========== ROUTER CLASS ==========
class Router {
  constructor() {
    this.routes = new Map();
    this.pageLoader = new PageLoader();
    this.mainContent = null;
    this.isNavigating = false;
    this.notificationManager = NotificationManager.getInstance();
    
    this.initRouter();
  }

  initRouter() {
    this.mainContent = document.getElementById('mainContent');
    
    if (!this.mainContent) {
      console.error('Main content element not found');
      return;
    }

    // Register routes
    this.registerRoutes();
    
    // Handle browser back/forward
    window.addEventListener('popstate', (e) => {
      if (e.state && e.state.path) {
        this.navigateTo(e.state.path, false);
      }
    });

    // Intercept navigation links
    this.interceptLinks();
    
    // ✨ Enable prefetch on hover
    this.enablePrefetch();
  }

  registerRoutes() {
    this.routes.set('/dashboard', { 
      title: 'Dashboard',
      module: 'dashboard'
    });
    this.routes.set('/materi', { 
      title: 'Materi Kelas',
      module: 'materi'
    });
    this.routes.set('/tugas', { 
      title: 'Tugas & Penilaian',
      module: 'tugas'
    });
    this.routes.set('/jadwal', { 
      title: 'Jadwal Kelas',
      module: 'jadwal'
    });
    this.routes.set('/peserta', { 
      title: 'Peserta',
      module: 'peserta'
    });
    this.routes.set('/pengaturan', { 
      title: 'Pengaturan',
      module: 'pengaturan'
    });
  }

  interceptLinks() {
    // Intercept all navigation links
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a.nav-item');
      
      if (!link) return;
      
      const href = link.getAttribute('href');
      
      // Only intercept internal links
      if (href && href.startsWith('/') && !href.startsWith('//')) {
        e.preventDefault();
        this.navigateTo(href);
      }
    });
  }

  async navigateTo(path, pushState = true) {
    if (this.isNavigating) return;
    
    const route = this.routes.get(path);
    if (!route) {
      console.warn('Route not found:', path);
      return;
    }

    this.isNavigating = true;

    try {
      // Show loading state
      this.showLoadingState();
      
      // Update active nav
      this.updateActiveNav(path);
      
      // Load page content
      const content = await this.pageLoader.loadPage(path);
      
      // Update content with fade animation
      await this.updateContent(content);
      
      // Load page-specific module
      await this.pageLoader.loadPageModule(route.module);
      
      // Update browser history
      if (pushState) {
        window.history.pushState({ path }, route.title, path);
      }
      
      // Update document title
      document.title = `${route.title} - My Schuder`;
      
      // Scroll to top
      this.mainContent.scrollTo({ top: 0, behavior: 'smooth' });
      
    } catch (error) {
      console.error('Navigation error:', error);
      this.notificationManager.show('Gagal memuat halaman', 'error');
      this.hideLoadingState();
    } finally {
      this.isNavigating = false;
    }
  }

  showLoadingState() {
    if (!this.mainContent) return;
    
    // Detect mobile
    const isMobile = window.innerWidth <= 768;
    const leftOffset = isMobile ? '0' : 'var(--sidebar-width, 260px)';
    
    // Add loading overlay dengan FIXED position (selalu terlihat)
    const overlay = document.createElement('div');
    overlay.id = 'page-loading-overlay';
    overlay.style.cssText = `
      position: fixed;
      top: var(--header-height, 70px);
      left: ${leftOffset};
      right: 0;
      bottom: 0;
      background: rgba(248, 250, 252, 0.95);
      backdrop-filter: blur(12px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      animation: fadeIn 0.3s ease;
    `;
    
    // ✅ Logo dari public folder
    const logoPath = '/assets/logo_akademik_hd.png';
    
    overlay.innerHTML = `
      <div style="text-align: center;">
        <!-- Logo dengan animasi -->
        <div style="position: relative; margin-bottom: 2rem;">
          <!-- Glow effect behind logo -->
          <div style="
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, rgba(8, 42, 152, 0.15) 0%, rgba(246, 124, 31, 0.1) 50%, transparent 70%);
            border-radius: 50%;
            animation: glowPulse 2s ease-in-out infinite;
            z-index: -2;
          "></div>
          
          <img 
            src="${logoPath}" 
            alt="Loading" 
            onerror="this.style.display='none'"
            style="
              width: 120px; 
              height: 120px; 
              object-fit: contain;
              animation: logoFloat 2s ease-in-out infinite;
              filter: drop-shadow(0 10px 30px rgba(8, 42, 152, 0.25));
            "
          />
          
          <!-- Rotating circle around logo -->
          <div style="
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 145px;
            height: 145px;
            border: 4px solid transparent;
            border-top-color: #082a98;
            border-right-color: #F67C1F;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
            z-index: -1;
          "></div>
          
          <!-- Second rotating circle (slower) -->
          <div style="
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 165px;
            height: 165px;
            border: 3px solid transparent;
            border-bottom-color: #F67C1F;
            border-left-color: #082a98;
            border-radius: 50%;
            animation: spinReverse 2s linear infinite;
            z-index: -1;
            opacity: 0.5;
          "></div>
        </div>
        
        <!-- Loading text -->
        <p style="
          color: #082a98; 
          font-weight: 700; 
          font-size: 1.2rem;
          margin: 0 0 0.5rem 0;
          animation: pulse 1.5s ease-in-out infinite;
        ">Memuat halaman...</p>
        
        <p style="
          color: #94a3b8; 
          font-size: 0.9rem;
          margin: 0;
        ">Mohon tunggu sebentar</p>
        
        <!-- Loading dots -->
        <div style="
          display: flex;
          gap: 0.5rem;
          justify-content: center;
          margin-top: 1.5rem;
        ">
          <div style="
            width: 10px;
            height: 10px;
            background: #082a98;
            border-radius: 50%;
            animation: dotBounce 1.4s ease-in-out infinite;
          "></div>
          <div style="
            width: 10px;
            height: 10px;
            background: #F67C1F;
            border-radius: 50%;
            animation: dotBounce 1.4s ease-in-out infinite;
            animation-delay: 0.2s;
          "></div>
          <div style="
            width: 10px;
            height: 10px;
            background: #082a98;
            border-radius: 50%;
            animation: dotBounce 1.4s ease-in-out infinite;
            animation-delay: 0.4s;
          "></div>
        </div>
      </div>
    `;
    
    // Tambahkan ke BODY (bukan main-content)
    document.body.appendChild(overlay);
    
    // Tambahkan CSS animations jika belum ada
    this.ensureLoadingAnimations();
  }

  ensureLoadingAnimations() {
    if (document.getElementById('loading-animations-style')) return;
    
    const style = document.createElement('style');
    style.id = 'loading-animations-style';
    style.textContent = `
      @keyframes logoFloat {
        0%, 100% { 
          transform: translateY(0px) scale(1);
        }
        50% { 
          transform: translateY(-15px) scale(1.05);
        }
      }
      
      @keyframes spin {
        to { 
          transform: translate(-50%, -50%) rotate(360deg); 
        }
      }
      
      @keyframes spinReverse {
        to { 
          transform: translate(-50%, -50%) rotate(-360deg); 
        }
      }
      
      @keyframes glowPulse {
        0%, 100% { 
          transform: translate(-50%, -50%) scale(1);
          opacity: 0.5;
        }
        50% { 
          transform: translate(-50%, -50%) scale(1.2);
          opacity: 0.8;
        }
      }
      
      @keyframes pulse {
        0%, 100% { 
          opacity: 1; 
        }
        50% { 
          opacity: 0.6; 
        }
      }
      
      @keyframes dotBounce {
        0%, 80%, 100% { 
          transform: translateY(0); 
        }
        40% { 
          transform: translateY(-12px); 
        }
      }
      
      @keyframes fadeIn {
        from { 
          opacity: 0;
          backdrop-filter: blur(0px);
        }
        to { 
          opacity: 1;
          backdrop-filter: blur(12px);
        }
      }
      
      @keyframes fadeOut {
        from { 
          opacity: 1;
          backdrop-filter: blur(12px);
        }
        to { 
          opacity: 0;
          backdrop-filter: blur(0px);
        }
      }
    `;
    document.head.appendChild(style);
  }

  hideLoadingState() {
    const overlay = document.getElementById('page-loading-overlay');
    if (overlay) {
      overlay.style.animation = 'fadeOut 0.2s ease';
      setTimeout(() => overlay.remove(), 200);
    }
  }

  async updateContent(html) {
    return new Promise((resolve) => {
      // Fade out
      this.mainContent.style.opacity = '0';
      this.mainContent.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        // Update content
        this.mainContent.innerHTML = html;
        
        // Remove loading overlay if exists
        this.hideLoadingState();
        
        // Fade in
        requestAnimationFrame(() => {
          this.mainContent.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          this.mainContent.style.opacity = '1';
          this.mainContent.style.transform = 'translateY(0)';
          
          setTimeout(resolve, 300);
        });
      }, 150);
    });
  }

  updateActiveNav(path) {
    // Remove all active states
    document.querySelectorAll('.nav-item').forEach(item => {
      item.classList.remove('active');
    });
    
    // Add active to current
    const activeLink = document.querySelector(`.nav-item[href="${path}"]`);
    if (activeLink) {
      activeLink.classList.add('active');
    }
  }

  // ✨ Prefetch on hover untuk faster navigation
  enablePrefetch() {
    document.querySelectorAll('.nav-item').forEach(link => {
      link.addEventListener('mouseenter', () => {
        const href = link.getAttribute('href');
        if (href && href.startsWith('/') && !href.startsWith('//')) {
          // Prefetch in background
          this.prefetchRoute(href);
          link.classList.add('prefetching');
          setTimeout(() => link.classList.remove('prefetching'), 500);
        }
      });
    });
  }

  // Public API
  refresh() {
    this.pageLoader.clearCache();
    const currentPath = window.location.pathname;
    this.navigateTo(currentPath, false);
  }

  prefetchRoute(path) {
    // Prefetch untuk faster navigation
    this.pageLoader.loadPage(path).catch(() => {});
  }
}

// ========== INITIALIZE ROUTER ==========
let routerInstance = null;

export function initRouter() {
  if (!routerInstance) {
    routerInstance = new Router();
    window.router = routerInstance;
  }
  return routerInstance;
}

export default initRouter;
