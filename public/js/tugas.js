(function() {
    'use strict';

    const DOM = {
        get uploadTugasBtn() { return document.getElementById("uploadTugasBtn"); },
        get tugasContainer() { return document.getElementById("tugasContainer"); }
    };

    // Filter Tugas
    function filterTugas() {
        const searchTerm = window.AppCore?.DOM.searchInput?.value.toLowerCase() || "";
        const activeTab = document.querySelector(".filter-tab.active");
        const filterStatus = activeTab ? activeTab.getAttribute("data-status") : "all";
        
        const tugasItems = document.querySelectorAll(".tugas-item");
        let visibleCount = 0;
        
        tugasItems.forEach(item => {
            const title = item.querySelector("h3")?.textContent.toLowerCase() || "";
            const description = item.querySelector(".tugas-description")?.textContent.toLowerCase() || "";
            const subject = item.querySelector(".tugas-subject")?.textContent.toLowerCase() || "";
            const itemStatus = item.getAttribute("data-status");
            
            let showItem = true;
            
            if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm) && !subject.includes(searchTerm)) {
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
                DOM.tugasContainer?.appendChild(emptyState);
            }
        } else {
            if (emptyState) {
                emptyState.remove();
            }
        }
    }

    // Filter Tabs
    function initFilterTabs() {
        const filterTabs = document.querySelectorAll(".filter-tab");
        filterTabs.forEach(tab => {
            tab.addEventListener("click", () => {
                filterTabs.forEach(t => t.classList.remove("active"));
                tab.classList.add("active");
                filterTugas();
            });
        });
    }

    // Button Actions
    function initButtonActions() {
        document.addEventListener("click", (e) => {
            // Kumpulkan Tugas
            if (e.target.closest(".btn-action.primary")) {
                const btn = e.target.closest(".btn-action.primary");
                const tugasItem = btn.closest(".tugas-item");
                const tugasTitle = tugasItem.querySelector("h3")?.textContent || "Tugas";
                
                if (window.AppCore) {
                    window.AppCore.showNotification("Fitur upload tugas akan segera hadir! 🎉", "info");
                }
            }
            
            // Lihat Detail
            if (e.target.closest(".btn-action.secondary")) {
                const btn = e.target.closest(".btn-action.secondary");
                const tugasItem = btn.closest(".tugas-item");
                const tugasTitle = tugasItem.querySelector("h3")?.textContent || "Tugas";
                
                if (window.AppCore) {
                    window.AppCore.showNotification(`Membuka detail: ${tugasTitle}`, "info");
                }
            }
            
            // Unduh/Actions lainnya
            if (e.target.closest(".btn-action.tertiary")) {
                const btn = e.target.closest(".btn-action.tertiary");
                const btnText = btn.textContent.trim();
                
                if (btnText.includes("Unduh")) {
                    if (window.AppCore) {
                        window.AppCore.showNotification("Mengunduh file...", "info");
                    }
                } else if (btnText.includes("Edit") || btnText.includes("Revisi")) {
                    if (window.AppCore) {
                        window.AppCore.showNotification("Fitur sedang dikembangkan", "info");
                    }
                }
            }
        });
    }

    // Initialize Tugas
    function initTugas() {
        initFilterTabs();
        initButtonActions();

        if (DOM.uploadTugasBtn) {
            DOM.uploadTugasBtn.addEventListener("click", () => {
                if (window.AppCore) {
                    window.AppCore.showNotification("Fitur upload tugas akan segera hadir! 🎉", "info");
                }
            });
        }

        // Connect search from core
        if (window.AppCore?.DOM.searchInput) {
            window.AppCore.DOM.searchInput.addEventListener('input', filterTugas);
        }

        console.log("✅ Tugas.js Loaded");
    }

    // Auto-init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTugas);
    } else {
        initTugas();
    }
})();