// ============================================
// SIDEBAR MANAGER CLASS
// ============================================
import mySchuderApp from './script.js';

class SidebarManager {
    constructor(menuBtnId, sidebarId, overlayId) {
        this.menuBtn = document.getElementById(menuBtnId);
        this.sidebar = document.getElementById(sidebarId);
        this.overlay = document.getElementById(overlayId);
        
        this.init();
    }
    
    init() {
        if (!this.menuBtn || !this.sidebar || !this.overlay) return;
        
        this.menuBtn.addEventListener("click", () => this.toggle());
        this.overlay.addEventListener("click", () => this.close());
        window.addEventListener("resize", () => this.handleResize());
    }
    
    toggle() {
        this.sidebar.classList.toggle("active");
        this.overlay.classList.toggle("active");
    }
    
    close() {
        this.sidebar.classList.remove("active");
        this.overlay.classList.remove("active");
    }
    
    handleResize() {
        if (window.innerWidth > 768) {
            this.close();
        }
    }
}

// ============================================
// SEARCH MANAGER CLASS
// ============================================
class SearchManager {
    constructor(searchInputId, searchClearId, filterCallback) {
        this.searchInput = document.getElementById(searchInputId);
        this.searchClear = document.getElementById(searchClearId);
        this.filterCallback = filterCallback;
        
        this.init();
    }
    
    init() {
        if (!this.searchInput) return;
        
        this.searchInput.addEventListener("input", (e) => this.handleInput(e));
        
        if (this.searchClear) {
            this.searchClear.addEventListener("click", () => this.clearSearch());
        }
    }
    
    handleInput(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        if (this.searchClear) {
            this.searchClear.style.display = searchTerm.length > 0 ? "block" : "none";
        }
        
        if (this.filterCallback) {
            this.filterCallback();
        }
    }
    
    clearSearch() {
        this.searchInput.value = "";
        if (this.searchClear) {
            this.searchClear.style.display = "none";
        }
        if (this.filterCallback) {
            this.filterCallback();
        }
    }
    
    getSearchTerm() {
        return this.searchInput ? this.searchInput.value.toLowerCase() : "";
    }
}

// ============================================
// FILTER TAB MANAGER CLASS
// ============================================
class FilterTabManager {
    constructor(tabSelector, filterCallback) {
        this.tabs = document.querySelectorAll(tabSelector);
        this.filterCallback = filterCallback;
        
        this.init();
    }
    
    init() {
        this.tabs.forEach(tab => {
            tab.addEventListener("click", () => this.handleTabClick(tab));
        });
    }
    
    handleTabClick(clickedTab) {
        this.tabs.forEach(tab => tab.classList.remove("active"));
        clickedTab.classList.add("active");
        
        if (this.filterCallback) {
            this.filterCallback();
        }
    }
    
    getActiveStatus() {
        const activeTab = document.querySelector(".filter-tab.active");
        return activeTab ? activeTab.getAttribute("data-status") : "all";
    }
}

// ============================================
// TUGAS FILTER MANAGER CLASS
// ============================================
class TugasFilterManager {
    constructor(containerSelector, searchManager, filterTabManager) {
        this.container = document.querySelector(containerSelector);
        this.searchManager = searchManager;
        this.filterTabManager = filterTabManager;
    }
    
    filter() {
        const searchTerm = this.searchManager.getSearchTerm();
        const filterStatus = this.filterTabManager.getActiveStatus();
        const tugasItems = document.querySelectorAll(".tugas-item");
        let visibleCount = 0;
        
        tugasItems.forEach(item => {
            const title = item.querySelector("h3").textContent.toLowerCase();
            const description = item.querySelector(".tugas-description").textContent.toLowerCase();
            const subject = item.querySelector(".tugas-subject").textContent.toLowerCase();
            const itemStatus = item.getAttribute("data-status");
            
            let showItem = true;
            
            // Search filter
            if (searchTerm && !title.includes(searchTerm) && 
                !description.includes(searchTerm) && !subject.includes(searchTerm)) {
                showItem = false;
            }
            
            // Status filter
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
            if (emptyState) {
                emptyState.remove();
            }
        }
    }
}

// ============================================
// NOTIFICATION MANAGER CLASS
// ============================================
class NotificationManager {
    constructor() {
        this.notifications = [];
    }
    
    show(message, type = "info") {
        const notification = document.createElement("div");
        notification.className = `notification ${type}`;
        
        const icon = this.getIcon(type);
        const bgColor = this.getBackgroundColor(type);
        
        notification.innerHTML = `
            <i class="fas fa-${icon}"></i>
            <span>${message}</span>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: ${90 + (this.notifications.length * 80)}px;
            right: 20px;
            background: ${bgColor};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 10001;
            animation: slideIn 0.3s ease;
            max-width: 400px;
        `;
        
        document.body.appendChild(notification);
        this.notifications.push(notification);
        
        setTimeout(() => {
            notification.style.animation = "slideOut 0.3s ease";
            setTimeout(() => {
                notification.remove();
                this.notifications = this.notifications.filter(n => n !== notification);
            }, 300);
        }, 3000);
    }
    
    getIcon(type) {
        const icons = {
            success: "check-circle",
            warning: "exclamation-triangle",
            error: "exclamation-circle",
            info: "info-circle"
        };
        return icons[type] || icons.info;
    }
    
    getBackgroundColor(type) {
        const colors = {
            success: "var(--success)",
            warning: "var(--warning)",
            error: "#dc2626",
            info: "var(--primary-blue)"
        };
        return colors[type] || colors.info;
    }
}

// ============================================
// UPLOAD DIALOG CLASS
// ============================================
class UploadDialog {
    constructor(notificationManager) {
        this.notificationManager = notificationManager;
        this.dialog = null;
    }
    
    show(preselectedTugas = null) {
        this.createDialog(preselectedTugas);
        this.attachEventListeners();
        this.addStyles();
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
            select.value = preselectedTugas;
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
        
        closeBtn.addEventListener("click", () => this.close());
        cancelBtn.addEventListener("click", () => this.close());
        dialogOverlay.addEventListener("click", () => this.close());
        
        fileUploadArea.addEventListener("click", () => fileInput.click());
        
        fileInput.addEventListener("change", (e) => {
            this.handleFileSelect(e.target.files[0], fileUploadArea, selectedFileDiv);
        });
        
        removeFileBtn.addEventListener("click", () => {
            this.removeFile(fileInput, fileUploadArea, selectedFileDiv);
        });
        
        submitBtn.addEventListener("click", () => {
            this.handleSubmit(submitBtn, fileInput);
        });
    }
    
    handleFileSelect(file, uploadArea, selectedDiv) {
        if (file) {
            uploadArea.style.display = "none";
            selectedDiv.style.display = "flex";
            selectedDiv.querySelector(".file-name").textContent = file.name;
        }
    }
    
    removeFile(fileInput, uploadArea, selectedDiv) {
        fileInput.value = "";
        uploadArea.style.display = "flex";
        selectedDiv.style.display = "none";
    }
    
    handleSubmit(submitBtn, fileInput) {
        const selectedTugas = this.dialog.querySelector("#selectTugas").value;
        const file = fileInput.files[0];
        
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
        if (this.dialog) {
            this.dialog.remove();
            this.dialog = null;
        }
    }
    
    addStyles() {
        if (document.querySelector("#dialogStyles")) return;
        
        const style = document.createElement("style");
        style.id = "dialogStyles";
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
                border-bottom: 1px solid var(--border-color);
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: linear-gradient(135deg, #f8fafc, #e0f2fe);
            }
            
            .dialog-header h3 {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: var(--text-primary);
                font-size: 1.3rem;
            }
            
            .dialog-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                color: var(--text-secondary);
                cursor: pointer;
                padding: 0.5rem;
                border-radius: 8px;
                transition: var(--transition);
            }
            
            .dialog-close:hover {
                background: rgba(0, 0, 0, 0.1);
                color: var(--text-primary);
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
                color: var(--text-primary);
            }
            
            .form-control {
                width: 100%;
                padding: 0.875rem 1rem;
                border: 2px solid var(--border-color);
                border-radius: 12px;
                font-size: 1rem;
                transition: var(--transition);
                font-family: inherit;
            }
            
            .form-control:focus {
                outline: none;
                border-color: var(--primary-blue);
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }
            
            .file-upload-area {
                border: 2px dashed var(--border-color);
                border-radius: 12px;
                padding: 2rem;
                text-align: center;
                cursor: pointer;
                transition: var(--transition);
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }
            
            .file-upload-area:hover {
                border-color: var(--primary-blue);
                background: var(--light-blue);
            }
            
            .file-upload-area i {
                font-size: 3rem;
                color: var(--primary-blue);
            }
            
            .file-upload-area p {
                font-weight: 600;
                color: var(--text-primary);
            }
            
            .file-info {
                font-size: 0.85rem;
                color: var(--text-secondary);
            }
            
            .selected-file {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1rem;
                background: var(--light-blue);
                border-radius: 12px;
                border: 2px solid var(--primary-blue);
            }
            
            .selected-file i {
                font-size: 1.5rem;
                color: var(--primary-blue);
            }
            
            .file-name {
                flex: 1;
                font-weight: 600;
                color: var(--text-primary);
            }
            
            .remove-file {
                background: none;
                border: none;
                color: var(--text-secondary);
                cursor: pointer;
                padding: 0.5rem;
                border-radius: 8px;
                transition: var(--transition);
            }
            
            .remove-file:hover {
                background: rgba(0, 0, 0, 0.1);
                color: #dc2626;
            }
            
            .dialog-footer {
                padding: 1.5rem 2rem;
                border-top: 1px solid var(--border-color);
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
                transition: var(--transition);
                font-size: 1rem;
            }
            
            .btn-cancel {
                background: white;
                color: var(--text-secondary);
                border: 2px solid var(--border-color);
            }
            
            .btn-cancel:hover {
                background: #f1f5f9;
            }
            
            .btn-submit {
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-orange));
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

// ============================================
// BUTTON ACTION HANDLER CLASS
// ============================================
class ButtonActionHandler {
    constructor(notificationManager, uploadDialog) {
        this.notificationManager = notificationManager;
        this.uploadDialog = uploadDialog;
        
        this.init();
    }
    
    init() {
        document.addEventListener("click", (e) => {
            this.handleClick(e);
        });
    }
    
    handleClick(e) {
        // Kumpulkan Tugas
        if (e.target.closest(".btn-action.primary")) {
            this.handleSubmitAssignment(e);
        }
        
        // Lihat Detail
        if (e.target.closest(".btn-action.secondary")) {
            this.handleViewDetail(e);
        }
        
        // Unduh/Actions lainnya
        if (e.target.closest(".btn-action.tertiary")) {
            this.handleTertiaryAction(e);
        }
    }
    
    handleSubmitAssignment(e) {
        const btn = e.target.closest(".btn-action.primary");
        const tugasItem = btn.closest(".tugas-item");
        const tugasId = tugasItem.getAttribute("data-id");
        
        this.uploadDialog.show(tugasId);
    }
    
    handleViewDetail(e) {
        const btn = e.target.closest(".btn-action.secondary");
        const tugasItem = btn.closest(".tugas-item");
        const tugasTitle = tugasItem.querySelector("h3").textContent;
        
        this.notificationManager.show(`Membuka detail: ${tugasTitle}`, "info");
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

// ============================================
// STYLE MANAGER CLASS
// ============================================
class StyleManager {
    static addAnimations() {
        if (document.querySelector("#animationStyles")) return;
        
        const style = document.createElement("style");
        style.id = "animationStyles";
        style.textContent = `
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// ============================================
// MAIN APP CLASS - ORCHESTRATOR
// ============================================
class TugasApp {
    constructor(config = {}) {
        this.config = {
            menuBtnId: "menuBtn",
            sidebarId: "sidebar",
            overlayId: "overlay",
            searchInputId: "searchInput",
            searchClearId: "searchClear",
            filterTabSelector: ".filter-tab",
            tugasContainerId: "#tugasContainer",
            uploadBtnId: "uploadTugasBtn",
            ...config
        };
        
        this.init();
    }
    
    init() {
        // Initialize all managers
        this.notificationManager = new NotificationManager();
        this.uploadDialog = new UploadDialog(this.notificationManager);
        
        // Setup filter callback
        const filterCallback = () => this.tugasFilterManager.filter();
        
        this.sidebarManager = new SidebarManager(
            this.config.menuBtnId,
            this.config.sidebarId,
            this.config.overlayId
        );
        
        this.searchManager = new SearchManager(
            this.config.searchInputId,
            this.config.searchClearId,
            filterCallback
        );
        
        this.filterTabManager = new FilterTabManager(
            this.config.filterTabSelector,
            filterCallback
        );
        
        this.tugasFilterManager = new TugasFilterManager(
            this.config.tugasContainerId,
            this.searchManager,
            this.filterTabManager
        );
        
        this.buttonActionHandler = new ButtonActionHandler(
            this.notificationManager,
            this.uploadDialog
        );
        
        // Add styles
        StyleManager.addAnimations();
        
        // Setup upload button
        this.setupUploadButton();
        
        console.log("✅ Tugas App Initialized (OOP Version)");
    }
    
    setupUploadButton() {
        const uploadBtn = document.getElementById(this.config.uploadBtnId);
        if (uploadBtn) {
            uploadBtn.addEventListener("click", () => {
                this.uploadDialog.show();
            });
        }
    }
}

// ============================================
// INITIALIZE APP
// ============================================
document.addEventListener("DOMContentLoaded", () => {
    window.tugasApp = new TugasApp();
});