// ============================================
// MATERI-SHOW.JS - Module untuk Halaman Detail Materi
// ============================================

import { NotificationManager } from './core.js';

class MateriShowApp {
  constructor(materiId) {
    this.materiId = materiId;
    this.notificationManager = NotificationManager.getInstance();
    this.init();
  }

  init() {
    console.log(`✅ Materi Show Page initialized for ID: ${this.materiId}`);
    
    // Initialize PDF viewer controls
    this.initPDFControls();
    
    // Initialize progress updater
    this.initProgressUpdater();
    
    // Initialize action buttons
    this.initActionButtons();
  }

  initPDFControls() {
    const pdfViewer = document.querySelector('.pdf-viewer');
    if (!pdfViewer) return;
    
    // Add fullscreen support
    const viewerSection = document.querySelector('.pdf-section');
    if (viewerSection) {
      viewerSection.addEventListener('dblclick', () => {
        if (document.fullscreenElement) {
          document.exitFullscreen();
        } else {
          viewerSection.requestFullscreen();
        }
      });
    }
  }

  initProgressUpdater() {
    // Auto-update progress setelah 5 menit viewing
    setTimeout(() => {
      this.updateProgress(25); // Increment 25%
    }, 5 * 60 * 1000);
  }

  async updateProgress(increment) {
    const progressBar = document.getElementById('progressBar');
    const progressValue = document.getElementById('progressValue');
    
    if (!progressBar || !progressValue) return;
    
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
      }
    } catch (error) {
      console.error('Failed to update progress:', error);
    }
  }

  initActionButtons() {
    // Download button
    const downloadBtn = document.querySelector('.action-btn.download');
    if (downloadBtn) {
      downloadBtn.addEventListener('click', () => {
        this.notificationManager.show('Mengunduh file...', 'info');
      });
    }

    // Back button
    const backBtn = document.querySelector('.back-button');
    if (backBtn) {
      backBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.history.back();
      });
    }
  }

  destroy() {
    console.log(`🧹 Cleaning up Materi Show for ID: ${this.materiId}`);
    // Cleanup event listeners if needed
  }
}

let materiShowInstance = null;

export function initMateriShowApp(materiId) {
  if (materiShowInstance) {
    materiShowInstance.destroy();
  }
  
  materiShowInstance = new MateriShowApp(materiId);
  return materiShowInstance;
}

export default MateriShowApp;