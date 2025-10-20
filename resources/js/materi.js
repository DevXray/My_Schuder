// Materi Page JavaScript

// DOM Elements
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const searchInput = document.getElementById("searchInput");
const searchClear = document.getElementById("searchClear");
const filterTabs = document.querySelectorAll(".filter-tab");
const categoryFilter = document.getElementById("categoryFilter");
const sortFilter = document.getElementById("sortFilter");
const materiGrid = document.getElementById("materiGrid");
const addMateriBtn = document.getElementById("addMateriBtn");

// ========== LOADING SCREEN ==========
document.addEventListener("DOMContentLoaded", () => {
  window.addEventListener("load", () => {
    hideLoadingScreen();
  });
  
  setTimeout(() => {
    hideLoadingScreen();
  }, 3000);
});

function hideLoadingScreen() {
  if (loadingScreen && !loadingScreen.classList.contains("hidden")) {
    loadingScreen.classList.add("hidden");
    setTimeout(() => {
      loadingScreen.style.display = "none";
    }, 500);
  }
}

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

        filterMateri();
    });

    if (searchClear) {
        searchClear.addEventListener("click", () => {
            searchInput.value = "";
            searchClear.style.display = "none";
            filterMateri();
        });
    }
}

// Filter Tabs
filterTabs.forEach(tab => {
    tab.addEventListener("click", () => {
        filterTabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
        filterMateri();
    });
});

// Category Filter
if (categoryFilter) {
    categoryFilter.addEventListener("change", () => {
        filterMateri();
    });
}

// Sort Filter
if (sortFilter) {
    sortFilter.addEventListener("change", () => {
        sortMateri();
    });
}

// Filter Materi Function
function filterMateri() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : "";
    const activeTab = document.querySelector(".filter-tab.active");
    const filterType = activeTab ? activeTab.getAttribute("data-filter") : "all";
    const category = categoryFilter ? categoryFilter.value : "all";
    
    const cards = document.querySelectorAll(".materi-card");
    
    cards.forEach(card => {
        const title = card.querySelector("h3").textContent.toLowerCase();
        const description = card.querySelector("p").textContent.toLowerCase();
        const cardCategory = card.getAttribute("data-category");
        const cardStatus = card.getAttribute("data-status");
        
        let showCard = true;
        
        // Search filter
        if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm)) {
            showCard = false;
        }
        
        // Category filter
        if (category !== "all" && cardCategory !== category) {
            showCard = false;
        }
        
        // Status filter
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

// Sort Materi Function
function sortMateri() {
    const sortValue = sortFilter ? sortFilter.value : "newest";
    const cards = Array.from(document.querySelectorAll(".materi-card"));
    
    cards.sort((a, b) => {
        if (sortValue === "name") {
            const nameA = a.querySelector("h3").textContent;
            const nameB = b.querySelector("h3").textContent;
            return nameA.localeCompare(nameB);
        } else if (sortValue === "progress") {
            const progressA = parseFloat(a.querySelector(".progress-text").textContent);
            const progressB = parseFloat(b.querySelector(".progress-text").textContent);
            return progressB - progressA;
        }
        return 0;
    });
    
    cards.forEach(card => {
        materiGrid.appendChild(card);
    });
}

// Add Materi Button
if (addMateriBtn) {
    addMateriBtn.addEventListener("click", () => {
        alert("Fitur tambah materi akan segera hadir! 🎉");
    });
}

// Card Interaction
document.addEventListener("click", (e) => {
    // Continue button
    const continueBtn = e.target.closest(".btn-secondary");
    if (continueBtn) {
        const card = continueBtn.closest(".materi-card");
        const materiTitle = card.querySelector("h3").textContent;
        showNotification(`Membuka materi: ${materiTitle}`, "success");
    }
    
    // Info button
    const infoBtn = e.target.closest(".btn-icon");
    if (infoBtn) {
        const card = infoBtn.closest(".materi-card");
        const materiTitle = card.querySelector("h3").textContent;
        showMateriDetail(card);
    }
});

// Show Materi Detail
function showMateriDetail(card) {
    const title = card.querySelector("h3").textContent;
    const description = card.querySelector("p").textContent;
    const modules = card.querySelector(".card-meta span:first-child").textContent;
    const duration = card.querySelector(".card-meta span:last-child").textContent;
    const progress = card.querySelector(".progress-text").textContent;
    
    const detail = `
📚 ${title}

${description}

${modules}
${duration}
Progress: ${progress}

Klik OK untuk membuka materi ini.
    `;
    
    if (confirm(detail)) {
        showNotification(`Membuka materi: ${title}`, "success");
    }
}

// Notification System
function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 20px;
        background: ${type === 'success' ? 'var(--success)' : 'var(--primary-blue)'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: var(--shadow-xl);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = "slideOut 0.3s ease";
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
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

console.log("✅ Materi Page Loaded Successfully");