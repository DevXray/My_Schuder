// resources/js/app.js - OPTIMAL VERSION (WITH SPA ROUTER)
import './bootstrap';

// ✅ Import core PERTAMA
import './core.js';

// ✅ Import router
import { initRouter } from './router.js';

// Import main app
import { initApp } from './script.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Track loaded modules
window.__loadedModules = {
  core: true,
  router: false,
  main: false,
  materi: false,
  materishow: false,
  tugas: false,
  jadwal: false
};

// Initialize Application
document.addEventListener('DOMContentLoaded', () => {
  console.log('🚀 Application Starting (SPA Mode)...');
  console.log('📍 Current Path:', window.location.pathname);
  
  // Initialize main app (sidebar, chatbot, header, dll)
  initApp();
  window.__loadedModules.main = true;
  
  // ✅ Initialize Router untuk SPA functionality
  const router = initRouter();
  window.__loadedModules.router = true;
  
  // ✅ Detect page dan load module yang sesuai UNTUK INITIAL PAGE LOAD
  const currentPath = window.location.pathname;
  
  if (currentPath.includes('materi') || document.querySelector('.materi-card')) {
    import('./materi.js').then(module => {
      module.initMateriApp();
      window.__loadedModules.materi = true;
      console.log('✅ Materi module loaded (initial)');
    }).catch(err => {
      console.error('❌ Failed to load Materi module:', err);
    });
  }

  if (currentPath.includes('materi/show') || document.querySelector('.pdf-viewer')) {
    import('./materi-show.js').then(module => {
      const materiId = document.body.getAttribute('data-materi-id');
      new module.MateriShowApp(materiId);
      window.__loadedModules.materishow = true;
      console.log('✅ Materi-Show module loaded (initial)');
    }).catch(err => {
      console.error('❌ Failed to load Materi-Show module:', err);
    });
  }
  
  if (currentPath.includes('tugas') || document.querySelector('.tugas-item')) {
    import('./tugas.js').then(module => {
      module.initTugasApp();
      window.__loadedModules.tugas = true;
      console.log('✅ Tugas module loaded (initial)');
    }).catch(err => {
      console.error('❌ Failed to load Tugas module:', err);
    });
  }
  
  if (currentPath.includes('jadwal') || document.querySelector('.class-item, .upcoming-card')) {
    import('./jadwal.js').then(module => {
      module.initJadwalApp();
      window.__loadedModules.jadwal = true;
      console.log('✅ Jadwal module loaded (initial)');
    }).catch(err => {
      console.error('❌ Failed to load Jadwal module:', err);
    });
  }
  
  // Log summary
  setTimeout(() => {
    console.log('📊 Loaded Modules:', window.__loadedModules);
    console.log('✅ Application initialized with SPA Router');
  }, 500);
});

// ✅ Expose testing utilities
window.testSPA = () => {
  console.log('🧪 Testing SPA Router...');
  console.log('Currently loaded modules:', window.__loadedModules);
  console.log('Router instance:', window.router);
  console.log('');
  console.log('Try navigating:');
  console.log('- Click sidebar links (should load without full refresh)');
  console.log('- Check Network tab (should only fetch HTML, not all assets)');
  console.log('- Observe loading animation');
};

console.log('💡 Run testSPA() in console to check SPA functionality');