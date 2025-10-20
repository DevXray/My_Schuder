// Tugas Page JavaScript

// DOM Elements
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const searchInput = document.getElementById("searchInput");
const searchClear = document.getElementById("searchClear");
const filterTabs = document.querySelectorAll(".filter-tab");
const tugasContainer = document.getElementById("tugasContainer");
const uploadTugasBtn = document.getElementById("uploadTugasBtn");

// Sidebar Toggle
if (menuBtn && sidebar && overlay) {
    menuBtn.addEventListener("click", () => {
        sidebar.classList.toggle("active");
        overlay.classList.toggle("active");
    });

    overlay.addEventListener("click", () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
    });
}

// Search Functionality
if (searchInput) {
    searchInput.addEventListener("input", (e) => {
        const searchTerm = e.target.value.toLowerCase();
        
        if (searchTerm.length > 0) {
            if (searchClear) searchClear.style.display = "block";
        } else {
            if (searchClear) searchClear.style.display = "none";
        }

        filterTugas();
    });

    if (searchClear) {
        searchClear.addEventListener("click", () => {
            searchInput.value = "";
            searchClear.style.display = "none";
            filterTugas();
        });
    }
}

// Filter Tabs
filterTabs.forEach(tab => {
    tab.addEventListener("click", () => {
        filterTabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
        filterTugas();
    });
});

// Filter Tugas Function
function filterTugas() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : "";
    const activeTab = document.querySelector(".filter-tab.active");
    const filterStatus = activeTab ? activeTab.getAttribute("data-status") : "all";
    
    const tugasItems = document.querySelectorAll(".tugas-item");
    let visibleCount = 0;
    
    tugasItems.forEach(item => {
        const title = item.querySelector("h3").textContent.toLowerCase();
        const description = item.querySelector(".tugas-description").textContent.toLowerCase();
        const subject = item.querySelector(".tugas-subject").textContent.toLowerCase();
        const itemStatus = item.getAttribute("data-status");
        
        let showItem = true;
        
        // Search filter
        if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm) && !subject.includes(searchTerm)) {
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

    // Show empty state if no results
    showEmptyState(visibleCount === 0);
}

// Show Empty State
function showEmptyState(show) {
    let emptyState = document.querySelector(".empty-state");
    
    if (show) {
        if (!emptyState) {
            emptyState = document.createElement("div");
            emptyState.className = "empty-state";
            emptyState.innerHTML = `
                <i class="fas fa-inbox"></i>
                <h3>Tidak Ada Tugas</h3>
                <p>Tidak ditemukan tugas yang sesuai dengan filter Anda.</p>
            `;
            tugasContainer.appendChild(emptyState);
        }
    } else {
        if (emptyState) {
            emptyState.remove();
        }
    }
}

// Upload Tugas Button
if (uploadTugasBtn) {
    uploadTugasBtn.addEventListener("click", () => {
        showUploadDialog();
    });
}

// Show Upload Dialog
function showUploadDialog() {
    const dialog = document.createElement("div");
    dialog.className = "upload-dialog";
    dialog.innerHTML = `
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
                    <textarea class="form-control" rows="3" placeholder="Tambahkan catatan untuk dosen..."></textarea>
                </div>
            </div>
            <div class="dialog-footer">
                <button class="btn-cancel">Batal</button>
                <button class="btn-submit"><i class="fas fa-paper-plane"></i> Kirim Tugas</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(dialog);
    
    // Dialog interactions
    const closeBtn = dialog.querySelector(".dialog-close");
    const cancelBtn = dialog.querySelector(".btn-cancel");
    const submitBtn = dialog.querySelector(".btn-submit");
    const dialogOverlay = dialog.querySelector(".dialog-overlay");
    const fileUploadArea = dialog.querySelector("#fileUploadArea");
    const fileInput = dialog.querySelector("#fileInput");
    const selectedFileDiv = dialog.querySelector("#selectedFile");
    const removeFileBtn = dialog.querySelector(".remove-file");
    
    const closeDialog = () => dialog.remove();
    
    closeBtn.addEventListener("click", closeDialog);
    cancelBtn.addEventListener("click", closeDialog);
    dialogOverlay.addEventListener("click", closeDialog);
    
    // File upload
    fileUploadArea.addEventListener("click", () => fileInput.click());
    
    fileInput.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (file) {
            fileUploadArea.style.display = "none";
            selectedFileDiv.style.display = "flex";
            selectedFileDiv.querySelector(".file-name").textContent = file.name;
        }
    });
    
    removeFileBtn.addEventListener("click", () => {
        fileInput.value = "";
        fileUploadArea.style.display = "flex";
        selectedFileDiv.style.display = "none";
    });
    
    submitBtn.addEventListener("click", () => {
        const selectedTugas = dialog.querySelector("#selectTugas").value;
        const file = fileInput.files[0];
        
        if (!selectedTugas) {
            showNotification("Pilih tugas terlebih dahulu!", "warning");
            return;
        }
        
        if (!file) {
            showNotification("Upload file tugas terlebih dahulu!", "warning");
            return;
        }
        
        // Simulate upload
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            closeDialog();
            showNotification("Tugas berhasil dikumpulkan! ✅", "success");
        }, 2000);
    });
    
    // Add dialog styles
    addDialogStyles();
}

// Add Dialog Styles
function addDialogStyles() {
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

// Notification System
function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    
    const icon = type === "success" ? "check-circle" : type === "warning" ? "exclamation-triangle" : "info-circle";
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    const bgColor = type === "success" ? "var(--success)" : type === "warning" ? "var(--warning)" : "var(--primary-blue)";
    
    notification.style.cssText = `
        position: fixed;
        top: 90px;
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
    
    setTimeout(() => {
        notification.style.animation = "slideOut 0.3s ease";
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Button Actions
document.addEventListener("click", (e) => {
    // Kumpulkan Tugas
    if (e.target.closest(".btn-action.primary")) {
        const btn = e.target.closest(".btn-action.primary");
        const tugasItem = btn.closest(".tugas-item");
        const tugasTitle = tugasItem.querySelector("h3").textContent;
        
        showUploadDialog();
    }
    
    // Lihat Detail
    if (e.target.closest(".btn-action.secondary")) {
        const btn = e.target.closest(".btn-action.secondary");
        const tugasItem = btn.closest(".tugas-item");
        const tugasTitle = tugasItem.querySelector("h3").textContent;
        
        showNotification(`Membuka detail: ${tugasTitle}`, "info");
    }
    
    // Unduh/Actions lainnya
    if (e.target.closest(".btn-action.tertiary")) {
        const btn = e.target.closest(".btn-action.tertiary");
        const btnText = btn.textContent.trim();
        
        if (btnText.includes("Unduh")) {
            showNotification("Mengunduh file...", "info");
        } else if (btnText.includes("Edit")) {
            showNotification("Fitur edit sedang dikembangkan", "info");
        } else if (btnText.includes("Revisi")) {
            showNotification("Fitur revisi sedang dikembangkan", "info");
        }
    }
});

// Add animations
const style = document.createElement("style");
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

// Responsive Sidebar Close
window.addEventListener("resize", () => {
    if (window.innerWidth > 768 && sidebar && overlay) {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
    }
});

console.log("✅ Tugas Page Loaded Successfully");