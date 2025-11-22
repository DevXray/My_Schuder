// ============================================
// MATERI-SHOW.JS - Module untuk Halaman Detail Materi (UPDATED)
// ============================================

import { NotificationManager } from './core.js';

class MateriShowApp {
  constructor(materiId) {
    this.materiId = materiId;
    this.notificationManager = NotificationManager.getInstance();
    this.initialized = false;
    this.init();
  }

  init() {
    if (this.initialized) {
      console.warn('MateriShowApp already initialized');
      return;
    }

    console.log(`✅ Materi Show Page initialized for ID: ${this.materiId}`);
    
    // Initialize PDF viewer controls
    this.initPDFControls();
    
    // Initialize progress updater
    this.initProgressUpdater();
    
    // Initialize action buttons
    this.initActionButtons();
    
    this.initialized = true;
  }

  initPDFControls() {
    const pdfViewer = document.querySelector('.pdf-viewer');
    if (!pdfViewer) return;
    
    // Add fullscreen support
    const viewerSection = document.querySelector('.pdf-section');
    if (viewerSection) {
      // Remove existing listener jika ada
      const newViewer = viewerSection.cloneNode(true);
      viewerSection.parentNode?.replaceChild(newViewer, viewerSection);
      
      // Add new listener
      newViewer.addEventListener('dblclick', () => {
        if (document.fullscreenElement) {
          document.exitFullscreen();
        } else {
          newViewer.requestFullscreen();
        }
      });
      
      console.log('✅ PDF fullscreen controls initialized');
    }
  }

  initProgressUpdater() {
    // Auto-update progress setelah 5 menit viewing
    if (this.progressTimeout) {
      clearTimeout(this.progressTimeout);
    }
    
    this.progressTimeout = setTimeout(() => {
      this.updateProgress(25); // Increment 25%
    }, 5 * 60 * 1000);
    
    console.log('✅ Progress auto-updater initialized');
  }

  async updateProgress(increment) {
    const progressBar = document.getElementById('progressBar');
    const progressValue = document.getElementById('progressValue');
    
    if (!progressBar || !progressValue) {
      console.warn('Progress elements not found');
      return;
    }
    
    try {
      const currentProgress = parseInt(progressBar.style.width) || 0;
      const newProgress = Math.min(currentProgress + increment, 100);
      
      const response = await fetch(`/materi/${this.materiId}/progress`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ progress: newProgress })
      });
      
      if (response.ok) {
        progressBar.style.width = `${newProgress}%`;
        progressValue.textContent = `${newProgress}%`;
        this.notificationManager.show('Progress berhasil diupdate!', 'success');
        console.log('✅ Progress updated to:', newProgress);
      } else {
        console.error('Failed to update progress:', response.status);
      }
    } catch (error) {
      console.error('Failed to update progress:', error);
    }
  }

  initActionButtons() {
    // Download button
    const downloadBtn = document.querySelector('.action-btn.download');
    if (downloadBtn) {
      // Clone untuk remove existing listeners
      const newBtn = downloadBtn.cloneNode(true);
      downloadBtn.parentNode?.replaceChild(newBtn, downloadBtn);
      
      newBtn.addEventListener('click', (e) => {
        // Let default download work, just show notification
        this.notificationManager.show('Mengunduh file...', 'info');
        console.log('📥 Download initiated');
      });
    }

    // Back button - let SPA router handle it
    const backBtn = document.querySelector('.back-button');
    if (backBtn) {
      console.log('✅ Back button detected (will be handled by router)');
    }
    
    console.log('✅ Action buttons initialized');
  }

  destroy() {
    console.log(`🧹 Cleaning up Materi Show for ID: ${this.materiId}`);
    
    // Clear timeout
    if (this.progressTimeout) {
      clearTimeout(this.progressTimeout);
      this.progressTimeout = null;
    }
    
    // Remove event listeners (already handled by cloning in init)
    this.initialized = false;
    console.log('✅ Materi Show cleanup complete');
  }
}

// ========== SINGLETON INSTANCE MANAGEMENT ==========
let materiShowInstance = null;

export function initMateriShowApp(materiId) {
  // Cleanup old instance
  if (materiShowInstance) {
    console.log('🧹 Cleaning up previous Materi Show instance');
    materiShowInstance.destroy();
    materiShowInstance = null;
  }
  
  // Create new instance
  if (!materiId) {
    console.error('❌ Cannot initialize Materi Show: materiId is required');
    return null;
  }
  
  materiShowInstance = new MateriShowApp(materiId);
  window.materiShowApp = materiShowInstance;
  
  return materiShowInstance;
}

export { MateriShowApp };
export default MateriShowApp;