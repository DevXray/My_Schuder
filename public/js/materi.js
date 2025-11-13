(function() {
    'use strict';

    const DOM = {
        get addMateriBtn() { return document.getElementById("addMateriBtn"); },
        get categoryFilter() { return document.getElementById("categoryFilter"); },
        get sortFilter() { return document.getElementById("sortFilter"); },
        get materiGrid() { return document.getElementById("materiGrid"); }
    };

    // Filter Materi
    function filterMateri() {
        const searchTerm = window.AppCore?.DOM.searchInput?.value.toLowerCase() || "";
        const activeTab = document.querySelector(".filter-tab.active");
        const filterType = activeTab ? activeTab.getAttribute("data-filter") : "all";
        const category = DOM.categoryFilter ? DOM.categoryFilter.value : "all";
        
        const cards = document.querySelectorAll(".materi-card");
        
        cards.forEach(card => {
            const title = card.querySelector("h3")?.textContent.toLowerCase() || "";
            const description = card.querySelector("p")?.textContent.toLowerCase() || "";
            const cardCategory = card.getAttribute("data-category");
            const cardStatus = card.getAttribute("data-status");
            
            let showCard = true;
            
            if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm)) {
                showCard = false;
            }
            
            if (category !== "all" && cardCategory !== category) {
                showCard = false;
            }
            
            if (filterType !== "all") {
                if (filterType === "recent" && cardStatus !== "new") {
                    showCard = false;
                } else if (filterType === "progress" && cardStatus !== "progress") {
                    showCard = false;
                } else if (filterType === "completed" && cardStatus !== "completed") {
                    showCard = false;
                }
            }
            
            if (showCard) {
                card.style.display = "block";
                card.style.animation = "fadeIn 0.3s ease";
            } else {
                card.style.display = "none";
            }
        });
    }

    // Sort Materi
    function sortMateri() {
        const sortValue = DOM.sortFilter ? DOM.sortFilter.value : "newest";
        const cards = Array.from(document.querySelectorAll(".materi-card"));
        
        cards.sort((a, b) => {
            if (sortValue === "name") {
                const nameA = a.querySelector("h3")?.textContent || "";
                const nameB = b.querySelector("h3")?.textContent || "";
                return nameA.localeCompare(nameB);
            } else if (sortValue === "progress") {
                const progressA = parseFloat(a.querySelector(".progress-text")?.textContent || "0");
                const progressB = parseFloat(b.querySelector(".progress-text")?.textContent || "0");
                return progressB - progressA;
            }
            return 0;
        });
        
        cards.forEach(card => {
            DOM.materiGrid?.appendChild(card);
        });
    }

    // Filter Tabs
    function initFilterTabs() {
        const filterTabs = document.querySelectorAll(".filter-tab");
        filterTabs.forEach(tab => {
            tab.addEventListener("click", () => {
                filterTabs.forEach(t => t.classList.remove("active"));
                tab.classList.add("active");
                filterMateri();
            });
        });
    }

    // Initialize Materi
    function initMateri() {
        initFilterTabs();

        if (DOM.categoryFilter) {
            DOM.categoryFilter.addEventListener("change", filterMateri);
        }

        if (DOM.sortFilter) {
            DOM.sortFilter.addEventListener("change", sortMateri);
        }

        if (DOM.addMateriBtn) {
            DOM.addMateriBtn.addEventListener("click", () => {
                if (window.AppCore) {
                    window.AppCore.showNotification("Fitur tambah materi akan segera hadir! 🎉", "info");
                }
            });
        }

        // Connect search from core
        if (window.AppCore?.DOM.searchInput) {
            window.AppCore.DOM.searchInput.addEventListener('input', filterMateri);
        }

        console.log("✅ Materi.js Loaded");
    }

    // Auto-init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMateri);
    } else {
        initMateri();
    }
})();