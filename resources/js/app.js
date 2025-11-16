// resources/js/app.js
import './bootstrap';

// Import core PERTAMA (penting!)
import './core.js';

// ✨ TAMBAHAN: Import Router
import { initRouter } from './router.js';

// Import main app
import { initApp } from './script.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Initialize Application
document.addEventListener('DOMContentLoaded', () => {
  // Initialize main app
  initApp();
  
  // ✨ TAMBAHAN: Initialize router untuk SPA navigation
  const router = initRouter();
  
  // Initial page load - detect dan load module yang sesuai
  const currentPath = window.location.pathname; // ✨ TAMBAHAN
  
  // ✨ PERUBAHAN: Cek berdasarkan URL juga, bukan hanya DOM element
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
  
  console.log('✅ Application initialized with client-side routing'); // ✨ TAMBAHAN
});