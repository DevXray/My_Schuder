// ============================================
// SCRIPT.JS - Main Application Entry Point
// ============================================

import { 
  AppConfig, 
  SidebarManager, 
  SearchManager,
  NotificationManager,
  LoadingScreenManager,
  NavigationManager,
  AnimationManager,
  ChatbotManager  // Import ChatbotManager dari core
} from './core.js';

// ========== EVENT HANDLERS ==========
class EventHandlers {
  static initScheduleJoinButtons() {
    document.querySelectorAll(".schedule-join").forEach(btn => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const scheduleItem = btn.closest(".schedule-item");
        const className = scheduleItem?.querySelector("h4")?.textContent;

        btn.textContent = "Joining...";
        btn.disabled = true;

        setTimeout(() => {
          btn.textContent = "Joined ✓";
          btn.style.opacity = "0.7";
        }, 1500);
      });
    });
  }

  static initFilterButtons() {
    const filterBtns = document.querySelectorAll(".filter-btn");
    filterBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        filterBtns.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
      });
    });
  }

  static initNotificationButton(chatbot) {
    const notificationBtn = document.getElementById("notificationBtn");
    notificationBtn?.addEventListener("click", () => {
      chatbot.addMessage("📬 Anda memiliki 3 notifikasi baru:\n\n1. ⚠ Deadline tugas: 3 hari lagi\n2. 📚 Materi baru telah ditambahkan\n3. 🎉 Progress 75%", false);
      chatbot.open();
    });
  }

  static initKonamiCode(chatbot) {
    let konamiCode = [];
    const konamiPattern = ["ArrowUp", "ArrowUp", "ArrowDown", "ArrowDown", "ArrowLeft", "ArrowRight", "ArrowLeft", "ArrowRight", "b", "a"];

    document.addEventListener("keydown", (e) => {
      konamiCode.push(e.key);
      konamiCode = konamiCode.slice(-10);

      if (konamiCode.join("") === konamiPattern.join("")) {
        chatbot.addMessage("🎮 Easter Egg Found! Anda menemukan kode rahasia! 🎉✨", false);
        chatbot.open();
        konamiCode = [];
      }
    });
  }
}

// ========== MAIN APP CLASS ==========
class MySchuderApp {
  constructor() {
    // Prevent multiple initialization
    if (window.__mySchuderAppInitialized) {
      console.warn("MySchuderApp already initialized");
      return;
    }

    this.loadingScreen = new LoadingScreenManager('loadingScreen');
    this.sidebar = SidebarManager.getInstance();
    this.chatbot = ChatbotManager.getInstance(); // Gunakan singleton
    this.search = new SearchManager('searchInput', 'searchClear');
    this.navigation = new NavigationManager();
    this.notificationManager = NotificationManager.getInstance();
    
    this.init();
    window.__mySchuderAppInitialized = true;
  }

  async init() {
    console.log("%c🎓 Selamat datang di My Schuder!", "color: #3b82f6; font-size: 24px; font-weight: bold;");
    
    this.loadingScreen.init();
    await this.chatbot.init();
    
    AnimationManager.initStatsAnimation();
    EventHandlers.initScheduleJoinButtons();
    EventHandlers.initFilterButtons();
    EventHandlers.initNotificationButton(this.chatbot);
    EventHandlers.initKonamiCode(this.chatbot);
  }

  // Public API
  getChatbot() {
    return this.chatbot;
  }

  getSidebar() {
    return this.sidebar;
  }

  getNotificationManager() {
    return this.notificationManager;
  }
}

// ========== INITIALIZE APP ==========
let appInstance = null;

function initApp() {
  if (!appInstance) {
    appInstance = new MySchuderApp();
    window.mySchuderApp = appInstance;
  }
  return appInstance;
}

// Auto-initialize on load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}

export default MySchuderApp;
export { initApp };