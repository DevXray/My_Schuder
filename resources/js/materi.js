import MySchuderApp from './script.js';

document.addEventListener('DOMContentLoaded', () => {
  const app = new MateriApp();       // ← Gunakan langsung
});

// ========== MATERI CONFIG ==========
class MateriConfig {
  static NOTIFICATION_DURATION = 3000;
  static ANIMATION_DURATION = 300;
}

// ========== MATERI FILTER MANAGER ==========
class MateriFilterManager {
  constructor(config = {}) {
    this.searchInput = document.getElementById(config.searchInputId || 'searchInput');
    this.searchClear = document.getElementById(config.searchClearId || 'searchClear');
    this.filterTabs = document.querySelectorAll(config.filterTabsSelector || '.filter-tab');
    this.categoryFilter = document.getElementById(config.categoryFilterId || 'categoryFilter');
    this.sortFilter = document.getElementById(config.sortFilterId || 'sortFilter');
    this.materiGrid = document.getElementById(config.materiGridId || 'materiGrid');
    
    this.initListeners();
  }

  initListeners() {
    if (this.searchInput) {
      this.searchInput.addEventListener("input", (e) => this.handleSearch(e));
    }

    if (this.searchClear) {
      this.searchClear.addEventListener("click", () => this.clearSearch());
    }

    this.filterTabs.forEach(tab => {
      tab.addEventListener("click", () => this.handleFilterTab(tab));
    });

    if (this.categoryFilter) {
      this.categoryFilter.addEventListener("change", () => this.filterMateri());
    }

    if (this.sortFilter) {
      this.sortFilter.addEventListener("change", () => this.sortMateri());
    }
  }

  handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    if (this.searchClear) {
      this.searchClear.style.display = searchTerm.length > 0 ? "block" : "none";
    }

    this.filterMateri();
  }

  clearSearch() {
    if (this.searchInput) {
      this.searchInput.value = "";
      this.searchInput.focus();
    }
    if (this.searchClear) {
      this.searchClear.style.display = "none";
    }
    this.filterMateri();
  }

  handleFilterTab(tab) {
    this.filterTabs.forEach(t => t.classList.remove("active"));
    tab.classList.add("active");
    this.filterMateri();
  }

  filterMateri() {
    const searchTerm = this.searchInput ? this.searchInput.value.toLowerCase() : "";
    const activeTab = document.querySelector(".filter-tab.active");
    const filterType = activeTab ? activeTab.getAttribute("data-filter") : "all";
    const category = this.categoryFilter ? this.categoryFilter.value : "all";
    
    const cards = document.querySelectorAll(".materi-card");
    
    cards.forEach(card => {
      const title = card.querySelector("h3").textContent.toLowerCase();
      const description = card.querySelector("p").textContent.toLowerCase();
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

  sortMateri() {
    const sortValue = this.sortFilter ? this.sortFilter.value : "newest";
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
      this.materiGrid.appendChild(card);
    });
  }
}

// ========== MATERI CARD HANDLER ==========
class MateriCardHandler {
  constructor() {
    this.initListeners();
  }

  initListeners() {
    document.addEventListener("click", (e) => {
      const continueBtn = e.target.closest(".btn-secondary");
      if (continueBtn) {
        this.handleContinue(continueBtn);
      }
      
      const infoBtn = e.target.closest(".btn-icon");
      if (infoBtn) {
        this.handleInfo(infoBtn);
      }
    });
  }

  handleContinue(btn) {
    const card = btn.closest(".materi-card");
    const materiTitle = card.querySelector("h3").textContent;
    MateriNotification.show(`Membuka materi: ${materiTitle}`, "success");
  }

  handleInfo(btn) {
    const card = btn.closest(".materi-card");
    this.showMateriDetail(card);
  }

  showMateriDetail(card) {
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
      MateriNotification.show(`Membuka materi: ${title}`, "success");
    }
  }
}

// ========== MATERI NOTIFICATION ==========
class MateriNotification {
  static show(message, type = "info") {
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
    }, MateriConfig.NOTIFICATION_DURATION);
  }
}

// ========== MATERI ACTION HANDLER ==========
class MateriActionHandler {
  constructor() {
    this.addMateriBtn = document.getElementById('addMateriBtn');
    this.initListeners();
  }

  initListeners() {
    if (this.addMateriBtn) {
      this.addMateriBtn.addEventListener("click", () => {
        MateriNotification.show("Fitur tambah materi akan segera hadir! 🎉", "info");
      });
    }
  }
}

// ========== MATERI ANIMATION STYLES ==========
class MateriAnimationStyles {
  static init() {
    if (document.querySelector("#materiAnimationStyles")) return;
    
    const style = document.createElement('style');
    style.id = "materiAnimationStyles";
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

// ========== MATERI APP CLASS ==========
class MateriApp {
  constructor() {
    this.filter = new MateriFilterManager();
    this.cardHandler = new MateriCardHandler();
    this.actionHandler = new MateriActionHandler();
    
    this.init();
  }

  init() {
    MateriAnimationStyles.init();
    this.initResponsive();
    console.log("✅ Materi Page Loaded Successfully");
  }

  initResponsive() {
    window.addEventListener("resize", () => {
      if (window.innerWidth > 768) {
        this.sidebar.close();
      }
    });
  }
}

// ========== INITIALIZE MATERI APP ==========
window.addEventListener("load", () => {
  window.materiApp = new MateriApp();
});