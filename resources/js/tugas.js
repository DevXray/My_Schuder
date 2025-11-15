// ============================================
// TUGAS.JS - Tugas Page Module
// ============================================

import { 
  AppConfig,
  SidebarManager,
  SearchManager,
  NotificationManager,
  ChatbotManager  // Import ChatbotManager
} from './core.js';

// ========== FILTER TAB MANAGER ==========
class FilterTabManager {
  constructor(filterCallback) {
    this.tabs = document.querySelectorAll('.filter-tab');
    this.filterCallback = filterCallback;
    this.initListeners();
  }

  initListeners() {
    this.tabs.forEach(tab => {
      tab.addEventListener("click", () => this.handleTabClick(tab));
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
}

// ========== UPLOAD DIALOG ==========
class UploadDialog {
  constructor(notificationManager) {
    this.notificationManager = notificationManager;
    this.dialog = null;
    this.ensureStyles();
  }

  show(preselectedTugas = null) {
    this.createDialog(preselectedTugas);
    this.attachEventListeners();
  }

  createDialog(preselectedTugas) {
    this.dialog = document.createElement("div");
    this.dialog.className = "upload-dialog";
    this.dialog.innerHTML = `
      <div class="dialog-overlay"></div>
      <div class="dialog-content">
        <div class="dialog-header">
          <h3><i class="fas fa-upload"></i> Upload Tugas</h3>
          <button class="dialog-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="dialog-body">
          <div class="form-group">
            <label>Pilih Tugas:</label>
            <select class="form-control" id="selectTugas">
              <option value="">-- Pilih Tugas --</option>
              <option value="1">Project Akhir Semester - Aplikasi Web</option>
              <option value="2">Analisis Kompleksitas Algoritma</option>
            </select>
          </div>
          <div class="form-group">
            <label>Upload File:</label>
            <div class="file-upload-area" id="fileUploadArea">
              <i class="fas fa-cloud-upload-alt"></i>
              <p>Klik atau drag & drop file di sini</p>
              <span class="file-info">PDF, DOC, ZIP (Max 10MB)</span>
              <input type="file" id="fileInput" hidden accept=".pdf,.doc,.docx,.zip">
            </div>
            <div class="selected-file" id="selectedFile" style="display: none;">
              <i class="fas fa-file"></i>
              <span class="file-name"></span>
              <button class="remove-file"><i class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="form-group">
            <label>Catatan (Opsional):</label>
            <textarea class="form-control" id="noteInput" rows="3" placeholder="Tambahkan catatan untuk dosen..."></textarea>
          </div>
        </div>
        <div class="dialog-footer">
          <button class="btn-cancel">Batal</button>
          <button class="btn-submit"><i class="fas fa-paper-plane"></i> Kirim Tugas</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(this.dialog);
    
    if (preselectedTugas) {
      const select = this.dialog.querySelector("#selectTugas");
      if (select) select.value = preselectedTugas;
    }
  }

  attachEventListeners() {
    const closeBtn = this.dialog.querySelector(".dialog-close");
    const cancelBtn = this.dialog.querySelector(".btn-cancel");
    const submitBtn = this.dialog.querySelector(".btn-submit");
    const dialogOverlay = this.dialog.querySelector(".dialog-overlay");
    const fileUploadArea = this.dialog.querySelector("#fileUploadArea");
    const fileInput = this.dialog.querySelector("#fileInput");
    const selectedFileDiv = this.dialog.querySelector("#selectedFile");
    const removeFileBtn = this.dialog.querySelector(".remove-file");
    
    closeBtn?.addEventListener("click", () => this.close());
    cancelBtn?.addEventListener("click", () => this.close());
    dialogOverlay?.addEventListener("click", () => this.close());
    
    fileUploadArea?.addEventListener("click", () => fileInput?.click());
    
    fileInput?.addEventListener("change", (e) => {
      this.handleFileSelect(e.target.files[0], fileUploadArea, selectedFileDiv);
    });
    
    removeFileBtn?.addEventListener("click", () => {
      this.removeFile(fileInput, fileUploadArea, selectedFileDiv);
    });
    
    submitBtn?.addEventListener("click", () => {
      this.handleSubmit(submitBtn, fileInput);
    });
  }

  handleFileSelect(file, uploadArea, selectedDiv) {
    if (file) {
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

  handleSubmit(submitBtn, fileInput) {
    const selectedTugas = this.dialog.querySelector("#selectTugas")?.value;
    const file = fileInput?.files[0];
    
    if (!selectedTugas) {
      this.notificationManager.show("Pilih tugas terlebih dahulu!", "warning");
      return;
    }
    
    if (!file) {
      this.notificationManager.show("Upload file tugas terlebih dahulu!", "warning");
      return;
    }
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
      this.close();
      this.notificationManager.show("Tugas berhasil dikumpulkan! ✅", "success");
    }, 2000);
  }

  close() {
    this.dialog?.remove();
    this.dialog = null;
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
}

// ========== BUTTON ACTION HANDLER ==========
class ButtonActionHandler {
  constructor(notificationManager, uploadDialog) {
    this.notificationManager = notificationManager;
    this.uploadDialog = uploadDialog;
    this.initListeners();
  }

  initListeners() {
    document.addEventListener("click", (e) => {
      if (e.target.closest(".btn-action.primary")) {
        this.handleSubmitAssignment(e);
      }
      
      if (e.target.closest(".btn-action.secondary")) {
        this.handleViewDetail(e);
      }
      
      if (e.target.closest(".btn-action.tertiary")) {
        this.handleTertiaryAction(e);
      }
    });
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
}

// ========== TUGAS APP CLASS ==========
class TugasApp {
  constructor() {
    // Prevent multiple initialization
    if (window.__tugasAppInitialized) {
      console.warn("TugasApp already initialized");
      return;
    }

    this.notificationManager = NotificationManager.getInstance();
    this.sidebar = SidebarManager.getInstance();
    this.chatbot = ChatbotManager.getInstance(); // Akses chatbot singleton
    this.uploadDialog = new UploadDialog(this.notificationManager);
    
    const filterCallback = () => this.tugasFilterManager.filter();
    
    this.searchManager = new SearchManager('searchInput', 'searchClear', filterCallback);
    this.filterTabManager = new FilterTabManager(filterCallback);
    this.tugasFilterManager = new TugasFilterManager(this.searchManager, this.filterTabManager);
    this.buttonActionHandler = new ButtonActionHandler(this.notificationManager, this.uploadDialog);
    
    this.setupUploadButton();
    this.init();
    window.__tugasAppInitialized = true;
  }

  setupUploadButton() {
    const uploadBtn = document.getElementById('uploadTugasBtn');
    uploadBtn?.addEventListener("click", () => {
      this.uploadDialog.show();
    });
  }

  init() {
    console.log("✅ Tugas Page Loaded Successfully");
  }
}

// ========== INITIALIZE TUGAS APP ==========
let tugasAppInstance = null;

function initTugasApp() {
  if (!tugasAppInstance && document.querySelector('.tugas-item')) {
    tugasAppInstance = new TugasApp();
    window.tugasApp = tugasAppInstance;
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