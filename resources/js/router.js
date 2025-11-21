// ============================================
// ROUTER.JS - Enhanced with Dynamic Routes
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
        throw new Error(`HTTP ${response.status}`);
      }

      const html = await response.text();
      
      // Parse FULL HTML
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      
      // Extract content sections
      const mainContent = doc.querySelector('.main-content, .pdf-viewer-container')?.innerHTML;
      const sidebarContent = doc.querySelector('.sidebar')?.innerHTML;
      const headerContent = doc.querySelector('.header')?.innerHTML;
      
      if (!mainContent) {
        throw new Error('Main content not found');
      }

      const pageData = {
        mainContent,
        sidebarContent,
        headerContent,
        title: doc.querySelector('title')?.textContent || 'My Schuder'
      };
      
      // Cache for 5 minutes
      this.cache.set(path, pageData);
      setTimeout(() => this.cache.delete(path), 5 * 60 * 1000);

      return pageData;
    } catch (error) {
      console.error('💥 Page load error:', error);
      throw error;
    }
  }

  clearCache() {
    this.cache.clear();
  }

  async loadPageModule(pageName, params = {}) {
    // Cleanup previous controller
    if (this.currentController?.destroy) {
      console.log('🧹 Cleaning up previous module:', this.currentController.constructor.name);
      this.currentController.destroy();
      this.currentController = null;
    }

    // Lazy load page-specific module
    try {
      switch(pageName) {
        case 'materi':
          const { initMateriApp } = await import('./materi.js');
          this.currentController = initMateriApp();
          break;
          
        case 'materi-show':
          // ✅ Lazy load module khusus untuk materi show
          const { initMateriShowApp } = await import('./materi-show.js');
          this.currentController = initMateriShowApp(params.id);
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
    } catch (error) {
      console.error('Failed to load module:', error);
    }
  }
}

// ========== ROUTER CLASS ==========
class Router {
  constructor() {
    this.routes = new Map();
    this.pageLoader = new PageLoader();
    this.mainContent = null;
    this.sidebar = null;
    this.header = null;
    this.isNavigating = false;
    this.notificationManager = NotificationManager.getInstance();
    
    this.initRouter();
  }

  initRouter() {
    this.mainContent = document.getElementById('mainContent');
    this.sidebar = document.getElementById('sidebar');
    this.header = document.querySelector('.header');
    
    // ✅ Handle jika main content menggunakan class berbeda (untuk show pages)
    if (!this.mainContent) {
      this.mainContent = document.querySelector('.pdf-viewer-container, main');
    }
    
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
    
    // Enable prefetch on hover
    this.enablePrefetch();
    
    console.log('✅ Router initialized with dynamic routes');
  }

  registerRoutes() {
    // Static routes
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
    
    // ✅ Dynamic routes (regex patterns)
    this.dynamicRoutes = [
      {
        pattern: /^\/materi\/(\d+)$/,
        handler: (matches) => ({
          title: 'Detail Materi',
          module: 'materi-show',
          params: { id: matches[1] }
        })
      },
      {
        pattern: /^\/tugas\/(\d+)$/,
        handler: (matches) => ({
          title: 'Detail Tugas',
          module: 'tugas-show',
          params: { id: matches[1] }
        })
      }
    ];
  }

  matchRoute(path) {
    // Check static routes first
    if (this.routes.has(path)) {
      return this.routes.get(path);
    }
    
    // ✅ Check dynamic routes
    for (const dynamicRoute of this.dynamicRoutes) {
      const matches = path.match(dynamicRoute.pattern);
      if (matches) {
        return dynamicRoute.handler(matches);
      }
    }
    
    return null;
  }

  interceptLinks() {
    // Use event delegation
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a[href^="/"]');
      
      if (!link) return;
      
      const href = link.getAttribute('href');
      
      // Only intercept internal links
      if (href && href.startsWith('/') && !href.startsWith('//')) {
        // Don't intercept logout, download, or external links
        if (link.classList.contains('logout') || 
            link.hasAttribute('download') ||
            link.getAttribute('target') === '_blank') {
          return;
        }
        
        e.preventDefault();
        this.navigateTo(href);
      }
    });
  }

  async navigateTo(path, pushState = true) {
    if (this.isNavigating) {
      console.log('⏳ Navigation in progress, please wait...');
      return;
    }
    
    const route = this.matchRoute(path);
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
      const pageData = await this.pageLoader.loadPage(path);
      
      // Update ALL content sections
      await this.updateAllContent(pageData);
      
      // ✅ Load page-specific module dengan params (untuk dynamic routes)
      await this.pageLoader.loadPageModule(route.module, route.params || {});
      
      // Update browser history
      if (pushState) {
        window.history.pushState({ path }, route.title, path);
      }
      
      // Update document title
      document.title = pageData.title;
      
      // Scroll to top
      this.scrollToTop();
      
      console.log('✅ Navigation complete:', path);
      
    } catch (error) {
      console.error('Navigation error:', error);
      this.notificationManager.show('Gagal memuat halaman', 'error');
      this.hideLoadingState();
    } finally {
      this.isNavigating = false;
    }
  }

  scrollToTop() {
    // Find the scrollable container
    const scrollable = this.mainContent.classList.contains('pdf-viewer-container') 
      ? window 
      : this.mainContent;
      
    if (scrollable === window) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      scrollable.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  showLoadingState() {
    const isMobile = window.innerWidth <= 768;
    const leftOffset = isMobile ? '0' : 'var(--sidebar-width, 260px)';
    
    // Remove existing overlay first
    const existingOverlay = document.getElementById('page-loading-overlay');
    if (existingOverlay) {
      existingOverlay.remove();
    }
    
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
    
    const logoPath = '/assets/logo_akademik_hd.png';
    
    overlay.innerHTML = `
      <div style="text-align: center;">
        <div style="position: relative; margin-bottom: 2rem;">
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
        </div>
        
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
      </div>
    `;
    
    document.body.appendChild(overlay);
    this.ensureLoadingAnimations();
  }

  ensureLoadingAnimations() {
    if (document.getElementById('loading-animations-style')) return;
    
    const style = document.createElement('style');
    style.id = 'loading-animations-style';
    style.textContent = `
      @keyframes logoFloat {
        0%, 100% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(-15px) scale(1.05); }
      }
      @keyframes spin {
        to { transform: translate(-50%, -50%) rotate(360deg); }
      }
      @keyframes glowPulse {
        0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
        50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
      }
      @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
      }
      @keyframes fadeIn {
        from { opacity: 0; backdrop-filter: blur(0px); }
        to { opacity: 1; backdrop-filter: blur(12px); }
      }
      @keyframes fadeOut {
        from { opacity: 1; backdrop-filter: blur(12px); }
        to { opacity: 0; backdrop-filter: blur(0px); }
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

  async updateAllContent(pageData) {
    return new Promise((resolve) => {
      // Fade out
      this.mainContent.style.opacity = '0';
      this.mainContent.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        // Update main content
        this.mainContent.innerHTML = pageData.mainContent;
        
        // ✅ Re-assign mainContent jika struktur berubah (untuk show pages)
        const newMainContent = document.getElementById('mainContent') || 
                               document.querySelector('.pdf-viewer-container, main');
        if (newMainContent) {
          this.mainContent = newMainContent;
        }
        
        // Update sidebar if changed
        if (pageData.sidebarContent && this.sidebar) {
          if (this.sidebar.innerHTML !== pageData.sidebarContent) {
            this.sidebar.innerHTML = pageData.sidebarContent;
            console.log('🔄 Sidebar updated');
          }
        }
        
        // Update header if changed
        if (pageData.headerContent && this.header) {
          if (this.header.innerHTML !== pageData.headerContent) {
            this.header.innerHTML = pageData.headerContent;
            console.log('🔄 Header updated');
          }
        }
        
        // Remove loading overlay
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
    
    // ✅ For detail pages, activate parent route
    const basePath = path.split('/').slice(0, 2).join('/'); // e.g., /materi/123 → /materi
    
    // Try exact match first
    let activeLink = document.querySelector(`.nav-item[href="${path}"]`);
    
    // If not found, try base path
    if (!activeLink && basePath) {
      activeLink = document.querySelector(`.nav-item[href="${basePath}"]`);
    }
    
    if (activeLink) {
      activeLink.classList.add('active');
    }
  }

  enablePrefetch() {
    document.addEventListener('mouseenter', (e) => {
      const link = e.target.closest('a[href^="/"]');
      if (!link) return;
      
      const href = link.getAttribute('href');
      if (href && href.startsWith('/') && !href.startsWith('//')) {
        // Prefetch in background
        this.prefetchRoute(href);
        link.classList.add('prefetching');
        setTimeout(() => link.classList.remove('prefetching'), 500);
      }
    }, true);
  }

  prefetchRoute(path) {
    this.pageLoader.loadPage(path).catch(() => {});
  }

  // Public API
  refresh() {
    this.pageLoader.clearCache();
    const currentPath = window.location.pathname;
    this.navigateTo(currentPath, false);
  }
}

// ========== INITIALIZE ROUTER ==========
let routerInstance = null;

export function initRouter() {
  if (!routerInstance) {
    routerInstance = new Router();
    window.router = routerInstance;
    console.log('✅ Router initialized with dynamic routes support');
  }
  return routerInstance;
}

export default initRouter;