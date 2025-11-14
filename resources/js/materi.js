// ============================================
// MATERI.JS - Materi Page Module
// ============================================

import { 
  AppConfig, 
  SidebarManager,
  SearchManager,
  NotificationManager,
  ChatbotManager  // Import ChatbotManager
} from './core.js';

// ========== MATERI FILTER MANAGER ==========
class MateriFilterManager {
  constructor() {
    this.filterTabs = document.querySelectorAll('.filter-tab');
    this.categoryFilter = document.getElementById('categoryFilter');
    this.sortFilter = document.getElementById('sortFilter');
    this.materiGrid = document.getElementById('materiGrid');
    
    this.initListeners();
  }

  initListeners() {
    this.filterTabs.forEach(tab => {
      tab.addEventListener("click", () => this.handleFilterTab(tab));
    });

    this.categoryFilter?.addEventListener("change", () => this.filterMateri());
    this.sortFilter?.addEventListener("change", () => this.sortMateri());
  }

  handleFilterTab(tab) {
    this.filterTabs.forEach(t => t.classList.remove("active"));
    tab.classList.add("active");
    this.filterMateri();
  }

  filterMateri(searchTerm = "") {
    const activeTab = document.querySelector(".filter-tab.active");
    const filterType = activeTab?.getAttribute("data-filter") || "all";
    const category = this.categoryFilter?.value || "all";
    
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
      
      card.style.display = showCard ? "block" : "none";
      if (showCard) {
        card.style.animation = "fadeIn 0.3s ease";
      }
    });
  }

  sortMateri() {
    if (!this.materiGrid) return;
    
    const sortValue = this.sortFilter?.value || "newest";
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
      this.materiGrid.appendChild(card);
    });
  }
}

// ========== MATERI CARD HANDLER ==========
class MateriCardHandler {
  constructor(notificationManager) {
    this.notificationManager = notificationManager;
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
    const materiTitle = card?.querySelector("h3")?.textContent;
    if (materiTitle) {
      this.notificationManager.show(`Membuka materi: ${materiTitle}`, "success");
    }
  }

  handleInfo(btn) {
    const card = btn.closest(".materi-card");
    if (card) {
      this.showMateriDetail(card);
    }
  }

  showMateriDetail(card) {
    const title = card.querySelector("h3")?.textContent || "";
    const description = card.querySelector("p")?.textContent || "";
    const modules = card.querySelector(".card-meta span:first-child")?.textContent || "";
    const duration = card.querySelector(".card-meta span:last-child")?.textContent || "";
    const progress = card.querySelector(".progress-text")?.textContent || "";
    
    const detail = `
📚 ${title}

${description}

${modules}
${duration}
Progress: ${progress}

Klik OK untuk membuka materi ini.
    `;
    
    if (confirm(detail)) {
      this.notificationManager.show(`Membuka materi: ${title}`, "success");
    }
  }
}

// ========== MATERI ACTION HANDLER ==========
class MateriActionHandler {
  constructor(notificationManager) {
    this.notificationManager = notificationManager;
    this.addMateriBtn = document.getElementById('addMateriBtn');
    this.initListeners();
  }

  initListeners() {
    this.addMateriBtn?.addEventListener("click", () => {
      this.notificationManager.show("Fitur tambah materi akan segera hadir! 🎉", "info");
    });
  }
}

// ========== MATERI APP CLASS ==========
class MateriApp {
  constructor() {
    // Prevent multiple initialization
    if (window.__materiAppInitialized) {
      console.warn("MateriApp already initialized");
      return;
    }

    this.notificationManager = NotificationManager.getInstance();
    this.sidebar = SidebarManager.getInstance();
    this.chatbot = ChatbotManager.getInstance(); // Akses chatbot singleton
    
    const filterCallback = (searchTerm) => this.filter.filterMateri(searchTerm);
    this.search = new SearchManager('searchInput', 'searchClear', filterCallback);
    
    this.filter = new MateriFilterManager();
    this.cardHandler = new MateriCardHandler(this.notificationManager);
    this.actionHandler = new MateriActionHandler(this.notificationManager);
    
    this.init();
    window.__materiAppInitialized = true;
  }

  init() {
    console.log("✅ Materi Page Loaded Successfully");
  }
}

// ========== INITIALIZE MATERI APP ==========
let materiAppInstance = null;

function initMateriApp() {
  if (!materiAppInstance && document.querySelector('.materi-card')) {
    materiAppInstance = new MateriApp();
    window.materiApp = materiAppInstance;
  }
  return materiAppInstance;
}

// Auto-initialize
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initMateriApp);
} else {
  initMateriApp();
}

export default MateriApp;
export { initMateriApp };