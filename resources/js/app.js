// resources/js/app.js
import './bootstrap';

// Import core PERTAMA (penting!)
import './core.js';

// Import main app
import { initApp } from './script.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
// Conditional imports untuk page-specific modules
document.addEventListener('DOMContentLoaded', () => {
  // Auto-detect dan load module yang sesuai
  if (document.querySelector('.materi-card')) {
    import('./materi.js').then(module => {
      module.initMateriApp();
    });
  }
  
  if (document.querySelector('.tugas-item')) {
    import('./tugas.js').then(module => {
      module.initTugasApp();
    });
  }
  
  if (document.querySelector('.class-item, .upcoming-card')) {
    import('./jadwal.js').then(module => {
      module.initJadwalApp();
    });
  }
  
  // Main app selalu diload
  initApp();
});