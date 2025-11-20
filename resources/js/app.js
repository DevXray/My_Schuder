// resources/js/app.js - FIXED VERSION
import './bootstrap';

// ✅ Import core PERTAMA
import './core.js';

// ❌ HAPUS ROUTER - Ini penyebab masalah!
// import { initRouter } from './router.js';

// Import main app
import { initApp } from './script.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Initialize Application
document.addEventListener('DOMContentLoaded', () => {
  // Initialize main app
  initApp();
  
  // ❌ HAPUS ROUTER INIT
  // const router = initRouter();
  
  // ✅ Detect page dan load module yang sesuai
  const currentPath = window.location.pathname;
  
  // Load module berdasarkan path ATAU DOM element
  if (currentPath.includes('materi') || document.querySelector('.materi-card')) {
    import('./materi.js').then(module => {
      module.initMateriApp();
    });
  }
  
  if (currentPath.includes('tugas') || document.querySelector('.tugas-item')) {
    import('./tugas.js').then(module => {
      module.initTugasApp();
    });
  }
  
  if (currentPath.includes('jadwal') || document.querySelector('.class-item, .upcoming-card')) {
    import('./jadwal.js').then(module => {
      module.initJadwalApp();
    });
  }
  
  console.log('✅ Application initialized WITHOUT SPA router');
});