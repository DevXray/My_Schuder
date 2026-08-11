// ============================================
// TUGAS.JS - Tugas Page Module (FIXED)
// ============================================

import { 
  AppConfig,
  SidebarManager,
  SearchManager,
  NotificationManager,
  ChatbotManager
} from './core.js';

// ========== FILTER TAB MANAGER ==========
class FilterTabManager {
  constructor(filterCallback) {
    this.tabs = document.querySelectorAll('.filter-tab');
    this.filterCallback = filterCallback;
    this.boundHandlers = new Map();
    this.initListeners();
  }

  initListeners() {
    this.tabs.forEach(tab => {
      const handler = () => this.handleTabClick(tab);
      this.boundHandlers.set(tab, handler);
      tab.addEventListener("click", handler);
    });
  }

  handleTabClick(clickedTab) {
    this.tabs.forEach(tab => tab.classList.remove("active"));
    clickedTab.classList.add("active");
    this.filterCallback?.();
  }

  getActiveStatus() {
    const activeTab = document.querySelector(".filter-tab.active");
    return activeTab?.getAttribute("data-status") || "all";
  }

  destroy() {
    this.tabs.forEach(tab => {
      const handler = this.boundHandlers.get(tab);
      if (handler) {
        tab.removeEventListener("click", handler);
      }
    });
    this.boundHandlers.clear();
  }
}

// ========== TUGAS FILTER MANAGER ==========
class TugasFilterManager {
  constructor(searchManager, filterTabManager) {
    this.container = document.querySelector('#tugasContainer');
    this.searchManager = searchManager;
    this.filterTabManager = filterTabManager;
  }

  filter() {
    const searchTerm = this.searchManager.getSearchTerm();
    const filterStatus = this.filterTabManager.getActiveStatus();
    const tugasItems = document.querySelectorAll(".tugas-item");
    let visibleCount = 0;
    
    tugasItems.forEach(item => {
      const title = item.querySelector("h3")?.textContent.toLowerCase() || "";
      const description = item.querySelector(".tugas-description")?.textContent.toLowerCase() || "";
      const subject = item.querySelector(".tugas-subject")?.textContent.toLowerCase() || "";
      const itemStatus = item.getAttribute("data-status");
      
      let showItem = true;
      
      if (searchTerm && !title.includes(searchTerm) && 
          !description.includes(searchTerm) && !subject.includes(searchTerm)) {
        showItem = false;
      }
      
      if (filterStatus !== "all" && itemStatus !== filterStatus) {
        showItem = false;
      }
      
      if (showItem) {
        item.style.display = "block";
        item.style.animation = "fadeIn 0.3s ease";
        visibleCount++;
      } else {
        item.style.display = "none";
      }
    });
    
    this.toggleEmptyState(visibleCount === 0);
  }

  toggleEmptyState(show) {
    if (!this.container) return;
    
    let emptyState = this.container.querySelector(".empty-state");
    
    if (show) {
      if (!emptyState) {
        emptyState = document.createElement("div");
        emptyState.className = "empty-state";
        emptyState.innerHTML = `
          <i class="fas fa-inbox"></i>
          <h3>Tidak Ada Tugas</h3>
          <p>Tidak ditemukan tugas yang sesuai dengan filter Anda.</p>
        `;
        this.container.appendChild(emptyState);
      }
    } else {
      emptyState?.remove();
    }
  }

  destroy() {
    // Cleanup jika diperlukan
  }
}

// ========== UPLOAD DIALOG (FIXED VERSION) ==========
class UploadDialog {
  constructor(notificationManager) {
    this.notificationManager = notificationManager;
    this.dialog = null;
    this.currentTugasId = null;
    this.boundHandlers = {
      close: null,
      cancel: null,
      submit: null,
      overlayClick: null,
      fileAreaClick: null,
      fileChange: null,
      removeFile: null
    };
    this.ensureStyles();
  }

  show(tugasId = null) {
    this.close();
    this.currentTugasId = tugasId;
    this.createDialog(tugasId);
    this.attachEventListeners();
  }

  createDialog(preselectedTugas) {
    this.dialog = document.createElement("div");
    this.dialog.className = "upload-dialog";
    
    // ✅ CRITICAL: Buat form yang proper dengan enctype multipart
    this.dialog.innerHTML = `
      <div class="dialog-overlay"></div>
      <div class="dialog-content">
        <div class="dialog-header">
          <h3><i class="fas fa-upload"></i> Upload Tugas</h3>
          <button type="button" class="dialog-close"><i class="fas fa-times"></i></button>
        </div>
        
        <form id="uploadTugasForm" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
          
          
          <div class="dialog-body">
            ${!preselectedTugas ? `
            <div class="form-group">
              <label>Pilih Tugas: <span class="required">*</span></label>
              <select class="form-control" name="tugas_id" id="selectTugas" required>
                <option value="">-- Pilih Tugas --</option>
                ${this.getTugasOptions()}
              </select>
            </div>
            ` : '<input type="hidden" name="tugas_id" value="${preselectedTugas}">'}
            
            <div class="form-group">
              <label>Upload File: <span class="required">*</span></label>
              <div class="file-upload-area" id="fileUploadArea">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Klik atau drag & drop file di sini</p>
                <span class="file-info">PDF, DOC, DOCX, ZIP (Max 20MB)</span>
                <input type="file" name="file_jawaban" id="fileInput" hidden accept=".pdf,.doc,.docx,.zip" required>
              </div>
              <div class="selected-file" id="selectedFile" style="display: none;">
                <i class="fas fa-file"></i>
                <span class="file-name"></span>
                <button type="button" class="remove-file"><i class="fas fa-times"></i></button>
              </div>
            </div>
            
            <div class="form-group">
              <label>Catatan (Opsional):</label>
              <textarea class="form-control" name="catatan" id="noteInput" rows="3" placeholder="Tambahkan catatan untuk dosen..."></textarea>
            </div>
          </div>
          
          <div class="dialog-footer">
            <button type="button" class="btn-cancel">Batal</button>
            <button type="submit" class="btn-submit">
              <i class="fas fa-paper-plane"></i> Kirim Tugas
            </button>
          </div>
        </form>
      </div>
    `;
    
    document.body.appendChild(this.dialog);
  }

  getTugasOptions() {
    // ✅ Ambil semua tugas yang pending dari DOM
    const tugasItems = document.querySelectorAll('.tugas-item[data-status="pending"]');
    let options = '';
    
    tugasItems.forEach(item => {
      const tugasId = item.querySelector('[onclick*="openSubmitModal"]')?.getAttribute('onclick')?.match(/\d+/)?.[0];
      const tugasTitle = item.querySelector('h3')?.textContent.trim();
      if (tugasId && tugasTitle) {
        options += `<option value="${tugasId}">${tugasTitle}</option>`;
      }
    });
    
    return options;
  }

  attachEventListeners() {
    const form = this.dialog.querySelector("#uploadTugasForm");
    const closeBtn = this.dialog.querySelector(".dialog-close");
    const cancelBtn = this.dialog.querySelector(".btn-cancel");
    const dialogOverlay = this.dialog.querySelector(".dialog-overlay");
    const fileUploadArea = this.dialog.querySelector("#fileUploadArea");
    const fileInput = this.dialog.querySelector("#fileInput");
    const selectedFileDiv = this.dialog.querySelector("#selectedFile");
    const removeFileBtn = this.dialog.querySelector(".remove-file");
    
    // ✅ Store handlers
    this.boundHandlers.close = () => this.close();
    this.boundHandlers.cancel = () => this.close();
    this.boundHandlers.overlayClick = () => this.close();
    this.boundHandlers.fileAreaClick = () => fileInput?.click();
    this.boundHandlers.fileChange = (e) => this.handleFileSelect(e.target.files[0], fileUploadArea, selectedFileDiv);
    this.boundHandlers.removeFile = () => this.removeFile(fileInput, fileUploadArea, selectedFileDiv);
    this.boundHandlers.submit = (e) => this.handleSubmit(e, form);
    
    closeBtn?.addEventListener("click", this.boundHandlers.close);
    cancelBtn?.addEventListener("click", this.boundHandlers.cancel);
    dialogOverlay?.addEventListener("click", this.boundHandlers.overlayClick);
    fileUploadArea?.addEventListener("click", this.boundHandlers.fileAreaClick);
    fileInput?.addEventListener("change", this.boundHandlers.fileChange);
    removeFileBtn?.addEventListener("click", this.boundHandlers.removeFile);
    form?.addEventListener("submit", this.boundHandlers.submit);
  }

  handleFileSelect(file, uploadArea, selectedDiv) {
    if (file) {
      // Validate file size (20MB)
      if (file.size > 20 * 1024 * 1024) {
        this.notificationManager.show("File terlalu besar! Maksimal 20MB", "error");
        return;
      }
      
      uploadArea.style.display = "none";
      selectedDiv.style.display = "flex";
      const fileNameSpan = selectedDiv.querySelector(".file-name");
      if (fileNameSpan) fileNameSpan.textContent = file.name;
    }
  }

  removeFile(fileInput, uploadArea, selectedDiv) {
    if (fileInput) fileInput.value = "";
    uploadArea.style.display = "flex";
    selectedDiv.style.display = "none";
  }

  async handleSubmit(e, form) {
    e.preventDefault();
    
    const submitBtn = form.querySelector(".btn-submit");
    const formData = new FormData(form);
    let tugasId = formData.get('tugas_id') 
    
    if (!tugasId || tugasId === '') {
      tugasId = this.currentTugasId;
    }
    
    // ✅ Debug log
    console.log('Tugas ID:', tugasId);
    console.log('Form Data:', {
      tugas_id: formData.get('tugas_id'),
      file: formData.get('file_jawaban')?.name,
      currentTugasId: this.currentTugasId
    });
    
    if (!tugasId) {
      this.notificationManager.show("Pilih tugas terlebih dahulu!", "warning");
      return;
    }
    
    if (!formData.get('file_jawaban') || formData.get('file_jawaban').size === 0) {
      this.notificationManager.show("Upload file tugas terlebih dahulu!", "warning");
      return;
    }
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';
    submitBtn.disabled = true;
    
    try {
      // ✅ Submit menggunakan fetch API
      const response = await fetch(`/tugas/${tugasId}/submit`, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        }
      });
      
      const result = await response.json();
      
      if (response.ok && result.success) {
        this.close();
        this.notificationManager.show(result.message || "Tugas berhasil dikumpulkan! ✅", "success");
        
        // Reload page setelah 1 detik
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        throw new Error(result.message || 'Gagal mengupload tugas');
      }
      
    } catch (error) {
      console.error('Upload error:', error);
      this.notificationManager.show(error.message || "Gagal mengupload tugas. Coba lagi.", "error");
      
      // Reset button
      submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Tugas';
      submitBtn.disabled = false;
    }
  }

  close() {
    if (this.dialog) {
      const form = this.dialog.querySelector("#uploadTugasForm");
      const closeBtn = this.dialog.querySelector(".dialog-close");
      const cancelBtn = this.dialog.querySelector(".btn-cancel");
      const dialogOverlay = this.dialog.querySelector(".dialog-overlay");
      const fileUploadArea = this.dialog.querySelector("#fileUploadArea");
      const fileInput = this.dialog.querySelector("#fileInput");
      const removeFileBtn = this.dialog.querySelector(".remove-file");
      
      // Remove all listeners
      closeBtn?.removeEventListener("click", this.boundHandlers.close);
      cancelBtn?.removeEventListener("click", this.boundHandlers.cancel);
      dialogOverlay?.removeEventListener("click", this.boundHandlers.overlayClick);
      fileUploadArea?.removeEventListener("click", this.boundHandlers.fileAreaClick);
      fileInput?.removeEventListener("change", this.boundHandlers.fileChange);
      removeFileBtn?.removeEventListener("click", this.boundHandlers.removeFile);
      form?.removeEventListener("submit", this.boundHandlers.submit);
      
      this.dialog.remove();
      this.dialog = null;
      this.currentTugasId = null;
    }
  }

  ensureStyles() {
    if (document.querySelector("#uploadDialogStyles")) return;
    
    const style = document.createElement("style");
    style.id = "uploadDialogStyles";
    style.textContent = `
      .upload-dialog {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
      }
      
      .dialog-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
      }
      
      .dialog-content {
        position: relative;
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.3s ease;
      }
      
      .dialog-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #f8fafc, #e0f2fe);
      }
      
      .dialog-header h3 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.3rem;
      }
      
      .dialog-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s;
      }
      
      .dialog-close:hover {
        background: rgba(0, 0, 0, 0.1);
      }
      
      .dialog-body {
        padding: 2rem;
      }
      
      .form-group {
        margin-bottom: 1.5rem;
      }
      
      .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
      }
      
      .required {
        color: #ef4444;
      }
      
      .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s;
        font-family: inherit;
      }
      
      .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      }
      
      .file-upload-area {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
      }
      
      .file-upload-area:hover {
        border-color: #3b82f6;
        background: #eff6ff;
      }
      
      .file-upload-area i {
        font-size: 3rem;
        color: #3b82f6;
      }
      
      .file-info {
        font-size: 0.85rem;
        color: #64748b;
      }
      
      .selected-file {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #eff6ff;
        border-radius: 12px;
        border: 2px solid #3b82f6;
      }
      
      .selected-file i {
        font-size: 1.5rem;
        color: #3b82f6;
      }
      
      .file-name {
        flex: 1;
        font-weight: 600;
      }
      
      .remove-file {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s;
      }
      
      .remove-file:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #dc2626;
      }
      
      .dialog-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 1rem;
        background: #f8fafc;
      }
      
      .btn-cancel,
      .btn-submit {
        flex: 1;
        padding: 0.875rem 1.5rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 1rem;
      }
      
      .btn-cancel {
        background: white;
        border: 2px solid #e2e8f0;
      }
      
      .btn-cancel:hover {
        background: #f1f5f9;
      }
      
      .btn-submit {
        background: linear-gradient(135deg, #3b82f6, #f97316);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
      }
      
      .btn-submit:hover:not(:disabled) {
        transform: scale(1.03);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
      }
      
      .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
      }
      
      @keyframes slideUp {
        from {
          transform: translateY(50px);
          opacity: 0;
        }
        to {
          transform: translateY(0);
          opacity: 1;
        }
      }
    `;
    document.head.appendChild(style);
  }

  destroy() {
    this.close();
  }
}

// ✅ Export untuk digunakan di window
window.UploadDialog = UploadDialog;

// ========== BUTTON ACTION HANDLER ==========
class ButtonActionHandler {
  constructor(notificationManager, uploadDialog) {
    this.notificationManager = notificationManager;
    this.uploadDialog = uploadDialog;
    this.boundHandler = (e) => this.handleClick(e);
    this.initListeners();
  }

  initListeners() {
    // ✅ Gunakan SINGLE delegated listener
    document.addEventListener("click", this.boundHandler);
  }

  handleClick(e) {
    if (e.target.closest(".btn-action.primary")) {
      this.handleSubmitAssignment(e);
    } else if (e.target.closest(".btn-action.secondary")) {
      this.handleViewDetail(e);
    } else if (e.target.closest(".btn-action.tertiary")) {
      this.handleTertiaryAction(e);
    }
  }

  handleSubmitAssignment(e) {
    const btn = e.target.closest(".btn-action.primary");
    const tugasItem = btn.closest(".tugas-item");
    const tugasId = tugasItem?.getAttribute("data-id");
    
    this.uploadDialog.show(tugasId);
  }

  handleViewDetail(e) {
    const btn = e.target.closest(".btn-action.secondary");
    const tugasItem = btn.closest(".tugas-item");
    const tugasTitle = tugasItem?.querySelector("h3")?.textContent;
    
    if (tugasTitle) {
      this.notificationManager.show(`Membuka detail: ${tugasTitle}`, "info");
    }
  }

  handleTertiaryAction(e) {
    const btn = e.target.closest(".btn-action.tertiary");
    const btnText = btn.textContent.trim();
    
    if (btnText.includes("Unduh")) {
      this.notificationManager.show("Mengunduh file...", "info");
    } else if (btnText.includes("Edit")) {
      this.notificationManager.show("Fitur edit sedang dikembangkan", "info");
    } else if (btnText.includes("Revisi")) {
      this.notificationManager.show("Fitur revisi sedang dikembangkan", "info");
    }
  }

  destroy() {
    // ✅ Remove listener saat destroy
    document.removeEventListener("click", this.boundHandler);
  }
}

// ========== TUGAS APP CLASS ==========
class TugasApp {
  constructor() {
    this.notificationManager = NotificationManager.getInstance();
    this.sidebar = SidebarManager.getInstance();
    this.chatbot = ChatbotManager.getInstance();
    this.uploadDialog = new UploadDialog(this.notificationManager);
    
    const filterCallback = () => this.tugasFilterManager.filter();
    
    this.searchManager = new SearchManager('searchInput', 'searchClear', filterCallback);
    this.filterTabManager = new FilterTabManager(filterCallback);
    this.tugasFilterManager = new TugasFilterManager(this.searchManager, this.filterTabManager);
    this.buttonActionHandler = new ButtonActionHandler(this.notificationManager, this.uploadDialog);
    
    this.uploadBtnHandler = () => this.uploadDialog.show();
    this.setupUploadButton();
    this.init();
  }

  setupUploadButton() {
    const uploadBtn = document.getElementById('uploadTugasBtn');
    if (uploadBtn) {
      // ✅ Remove old listener dulu (jika ada)
      uploadBtn.removeEventListener("click", this.uploadBtnHandler);
      uploadBtn.addEventListener("click", this.uploadBtnHandler);
    }
  }

  init() {
    console.log("✅ Tugas Page Loaded Successfully");
  }

  // ✅ PENTING: Tambahkan destroy method
  destroy() {
    console.log("🧹 Cleaning up Tugas Page...");
    
    // Cleanup upload button
    const uploadBtn = document.getElementById('uploadTugasBtn');
    if (uploadBtn) {
      uploadBtn.removeEventListener("click", this.uploadBtnHandler);
    }
    
    // Cleanup all managers
    this.filterTabManager?.destroy();
    this.tugasFilterManager?.destroy();
    this.buttonActionHandler?.destroy();
    this.uploadDialog?.destroy();
    
    // Clear reference
    this.filterTabManager = null;
    this.tugasFilterManager = null;
    this.buttonActionHandler = null;
    this.uploadDialog = null;
  }
}

// ========== INITIALIZE TUGAS APP ==========
let tugasAppInstance = null;

function initTugasApp() {
  // ✅ Cleanup instance lama
  if (tugasAppInstance) {
    tugasAppInstance.destroy();
    tugasAppInstance = null;
  }
  
  // ✅ Hanya init jika element ada
  if (document.querySelector('.tugas-item')) {
    tugasAppInstance = new TugasApp();
    window.tugasApp = tugasAppInstance;
    console.log("✅ TugasApp initialized");
  }
  
  return tugasAppInstance;
}

// Auto-initialize
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initTugasApp);
} else {
  initTugasApp();
}

export default TugasApp;
export { initTugasApp };