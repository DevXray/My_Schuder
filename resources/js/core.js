// ============================================
// CORE.JS - Shared Classes untuk Semua Page
// ============================================

// ========== CONFIG CLASS ==========
export class AppConfig {
  static GEMINI_API_URL = '/api/chat';
  static GEMINI_TEST_URL = '/api/chat/test';
  static MAX_MESSAGE_LENGTH = 1000;
  static MAX_CONVERSATION_HISTORY = 20;
  static TYPING_DELAY = 500;
  static ANIMATION_DURATION = 1000;
  static NOTIFICATION_DURATION = 3000;
  static MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
}

// ========== UI MANAGER BASE CLASS ==========
export class UIManager {
  constructor(elementId) {
    this.element = typeof elementId === 'string' 
      ? document.getElementById(elementId) 
      : elementId;
  }

  show() {
    this.element?.classList.add('active');
  }

  hide() {
    this.element?.classList.remove('active');
  }

  toggle() {
    this.element?.classList.toggle('active');
  }

  isActive() {
    return this.element?.classList.contains('active') || false;
  }
}

// ========== SIDEBAR MANAGER (SINGLETON) ==========
export class SidebarManager {
  static instance = null;

  constructor(menuBtnId = 'menuBtn', sidebarId = 'sidebar', overlayId = 'overlay') {
    if (SidebarManager.instance) {
      return SidebarManager.instance;
    }

    this.menuBtn = document.getElementById(menuBtnId);
    this.sidebar = new UIManager(sidebarId);
    this.overlay = new UIManager(overlayId);
    
    this.initListeners();
    SidebarManager.instance = this;
  }

  initListeners() {
    // Remove existing listeners untuk prevent duplikasi
    const newMenuBtn = this.menuBtn?.cloneNode(true);
    this.menuBtn?.parentNode?.replaceChild(newMenuBtn, this.menuBtn);
    this.menuBtn = newMenuBtn;

    const newOverlay = this.overlay.element?.cloneNode(true);
    this.overlay.element?.parentNode?.replaceChild(newOverlay, this.overlay.element);
    this.overlay.element = newOverlay;

    this.menuBtn?.addEventListener("click", () => this.toggle());
    this.overlay.element?.addEventListener("click", () => this.close());
    
    // Single resize listener
    if (!window.__sidebarResizeListenerAdded) {
      window.addEventListener("resize", () => this.handleResize());
      window.__sidebarResizeListenerAdded = true;
    }
  }

  toggle() {
    this.sidebar.toggle();
    this.overlay.toggle();
  }

  close() {
    this.sidebar.hide();
    this.overlay.hide();
  }

  handleResize() {
    if (window.innerWidth > 768) {
      this.close();
    }
  }

  static getInstance() {
    if (!SidebarManager.instance) {
      SidebarManager.instance = new SidebarManager();
    }
    return SidebarManager.instance;
  }
}

// ========== SEARCH MANAGER ==========
export class SearchManager {
  constructor(inputId, clearBtnId, filterCallback = null) {
    this.searchInput = document.getElementById(inputId);
    this.searchClear = document.getElementById(clearBtnId);
    this.filterCallback = filterCallback;
    this.initListeners();
  }

  initListeners() {
    if (!this.searchInput) return;

    this.searchInput.addEventListener("input", (e) => {
      const searchTerm = e.target.value;
      
      if (this.searchClear) {
        this.searchClear.style.display = searchTerm.length > 0 ? "block" : "none";
      }
      
      this.filterCallback?.();
    });

    this.searchClear?.addEventListener("click", () => {
      if (this.searchInput) {
        this.searchInput.value = "";
        this.searchInput.focus();
      }
      if (this.searchClear) {
        this.searchClear.style.display = "none";
      }
      this.filterCallback?.();
    });
  }

  getSearchTerm() {
    return this.searchInput?.value.toLowerCase().trim() || "";
  }

  clear() {
    if (this.searchInput) {
      this.searchInput.value = "";
    }
    if (this.searchClear) {
      this.searchClear.style.display = "none";
    }
  }
}

// ========== NOTIFICATION MANAGER (SINGLETON) ==========
export class NotificationManager {
  static instance = null;
  
  constructor() {
    if (NotificationManager.instance) {
      return NotificationManager.instance;
    }
    
    this.notifications = [];
    this.ensureStyles();
    NotificationManager.instance = this;
  }

  static getInstance() {
    if (!NotificationManager.instance) {
      NotificationManager.instance = new NotificationManager();
    }
    return NotificationManager.instance;
  }

  show(message, type = "info") {
    const notification = this.createNotification(message, type);
    document.body.appendChild(notification);
    this.notifications.push(notification);
    
    setTimeout(() => {
      notification.style.animation = "slideOut 0.3s ease";
      setTimeout(() => {
        notification.remove();
        this.notifications = this.notifications.filter(n => n !== notification);
        this.repositionNotifications();
      }, 300);
    }, AppConfig.NOTIFICATION_DURATION);
  }

  createNotification(message, type) {
    const notification = document.createElement("div");
    notification.className = `app-notification ${type}`;
    
    const icons = {
      success: "check-circle",
      warning: "exclamation-triangle",
      error: "exclamation-circle",
      info: "info-circle"
    };

    const colors = {
      success: "#10b981",
      warning: "#f59e0b",
      error: "#ef4444",
      info: "#3b82f6"
    };
    
    notification.innerHTML = `
      <i class="fas fa-${icons[type] || icons.info}"></i>
      <span>${message}</span>
    `;
    
    notification.style.cssText = `
      position: fixed;
      top: ${90 + (this.notifications.length * 80)}px;
      right: 20px;
      background: ${colors[type] || colors.info};
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
    
    return notification;
  }

  repositionNotifications() {
    this.notifications.forEach((notif, index) => {
      notif.style.top = `${90 + (index * 80)}px`;
    });
  }

  ensureStyles() {
    if (document.querySelector("#notificationStyles")) return;
    
    const style = document.createElement("style");
    style.id = "notificationStyles";
    style.textContent = `
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
    `;
    document.head.appendChild(style);
  }
}

// ========== LOADING SCREEN MANAGER ==========
export class LoadingScreenManager extends UIManager {
  constructor(elementId = 'loadingScreen') {
    super(elementId);
    this.initialized = false;
  }

  init() {
    if (this.initialized) return;
    
    document.addEventListener("DOMContentLoaded", () => {
      window.addEventListener("load", () => this.hideWithTransition());
    });
    
    setTimeout(() => this.hideWithTransition(), 3000);
    this.initialized = true;
  }

  hideWithTransition() {
    if (this.element && !this.element.classList.contains("hidden")) {
      this.element.classList.add("hidden");
      setTimeout(() => {
        this.element.style.display = "none";
      }, 500);
    }
  }
}

// resources/js/core.js - NAVIGATION MANAGER (FIXED UNTUK SPA)

// ========== NAVIGATION MANAGER (Client-Side) ==========
export class NavigationManager {
  constructor(selector = '.nav-item:not(.logout)') {
    this.navItems = document.querySelectorAll(selector);
    this.initListeners();
  }

  initListeners() {
    // ⚠️ PENTING: Jangan add listener di sini untuk SPA
    // Router.js akan handle interception via event delegation
    
    // Hanya set initial active state
    this.setInitialActiveState();
  }

  setInitialActiveState() {
    const currentPath = window.location.pathname;
    this.navItems.forEach(item => {
      const href = item.getAttribute('href');
      if (href === currentPath) {
        item.classList.add('active');
      }
    });
  }

  // Method untuk update active state (dipanggil oleh router)
  updateActive(path) {
    this.navItems.forEach(item => {
      const href = item.getAttribute('href');
      if (href === path) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });
  }
}

// ========== ANIMATION MANAGER ==========
export class AnimationManager {
  static animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
      current += increment;
      if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
        element.textContent = end;
        clearInterval(timer);
      } else {
        element.textContent = Math.floor(current);
      }
    }, 16);
  }

  static initStatsAnimation() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          if (entry.target.classList.contains("stat-number")) {
            const target = parseInt(entry.target.getAttribute("data-target"));
            AnimationManager.animateValue(entry.target, 0, target, AppConfig.ANIMATION_DURATION);
            observer.unobserve(entry.target);
          }

          if (entry.target.classList.contains("progress-fill")) {
            const progress = entry.target.getAttribute("data-progress");
            setTimeout(() => {
              entry.target.style.width = progress + "%";
            }, 200);
            observer.unobserve(entry.target);
          }
        }
      });
    }, observerOptions);

    document.querySelectorAll(".stat-number").forEach(stat => observer.observe(stat));
    document.querySelectorAll(".progress-fill").forEach(progress => observer.observe(progress));
  }
}

// ========== API SERVICE CLASS ==========
export class APIService {
  constructor() {
    this.conversationHistory = [];
    this.geminiAvailable = false;
  }

  async testGeminiAPI() {
    try {
      const response = await fetch(AppConfig.GEMINI_TEST_URL, {
        method: 'GET',
        headers: this.getHeaders()
      });

      const data = await response.json();

      if (data.success) {
        this.geminiAvailable = true;
        console.log("✅ Gemini API tersedia!");
        return true;
      }
      throw new Error(data.message || 'API not available');
    } catch (error) {
      console.warn("⚠️ Gemini API tidak tersedia:", error.message);
      this.geminiAvailable = false;
      return false;
    }
  }

  async sendMessage(userMessage) {
    try {
      const response = await fetch(AppConfig.GEMINI_API_URL, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          message: userMessage,
          conversation_history: this.conversationHistory
        })
      });

      const data = await response.json();
      
      if (data.success) {
        this.updateHistory(userMessage, data.response);
        return {
          response: data.response,
          source: data.source,
          fallback: data.fallback || false
        };
      }
      throw new Error(data.message || 'Unknown error');
    } catch (error) {
      console.error('Backend chat error:', error);
      throw error;
    }
  }

  updateHistory(userMessage, botResponse) {
    this.conversationHistory.push(
      { role: 'user', text: userMessage },
      { role: 'model', text: botResponse }
    );

    if (this.conversationHistory.length > AppConfig.MAX_CONVERSATION_HISTORY) {
      this.conversationHistory = this.conversationHistory.slice(-AppConfig.MAX_CONVERSATION_HISTORY);
    }
  }

  getHeaders() {
    return {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    };
  }

  isAvailable() {
    return this.geminiAvailable;
  }

  clearHistory() {
    this.conversationHistory = [];
  }
}

// ========== AI FALLBACK SERVICE ==========
export class AIFallbackService {
  constructor() {
    this.personality = {
      name: "Asisten Virtual AI",
      greeting: [
        "Halo! 👋 Saya siap membantu Anda.",
        "Hi! Ada yang bisa saya bantu?",
        "Selamat datang kembali! 🎉",
      ],
    };

    this.responses = {
      greeting: ["halo", "hi", "hey", "hai", "hello", "selamat pagi", "selamat siang", "selamat malam"],
      materi: {
        keywords: ["materi", "pelajaran", "bahan", "konten", "pembelajaran"],
        responses: [
          "Saat ini ada 24 materi tersedia di sistem. Materi terbaru meliputi:\n\n✅ Algoritma & Struktur Data\n✅ Pemrograman Web\n✅ Basis Data\n✅ Matematika Lanjut\n\nMateri mana yang ingin Anda akses?",
        ],
      },
      tugas: {
        keywords: ["tugas", "assignment", "pekerjaan rumah", "pr", "deadline"],
        responses: [
          "📝 Status tugas Anda:\n\n✅ Selesai: 12 tugas\n⏳ Aktif: 5 tugas\n⚠ Deadline minggu ini: 2 tugas\n\nIngin saya tampilkan detail tugasnya?",
        ],
      },
      jadwal: {
        keywords: ["jadwal", "schedule", "kelas", "jam", "waktu"],
        responses: [
          "🗓 Jadwal hari ini:\n\n09:00 - Matematika Lanjut (Ruang A101)\n11:00 - Pemrograman Web (Lab Komp 2)\n14:00 - Basis Data (Ruang B202)",
        ],
      },
      bantuan: {
        keywords: ["help", "bantuan", "tolong", "gimana", "bagaimana", "cara"],
        responses: [
          "🤖 Saya dapat membantu Anda dengan:\n\n📚 Informasi materi pembelajaran\n📝 Status dan deadline tugas\n🗓 Jadwal kelas harian\n📊 Informasi nilai dan progress",
        ],
      },
    };
  }

  getResponse(userMessage) {
    const message = userMessage.toLowerCase().trim();

    if (this.responses.greeting.some(greet => message.includes(greet))) {
      const randomGreeting = this.personality.greeting[Math.floor(Math.random() * this.personality.greeting.length)];
      return `${randomGreeting} Ada yang bisa saya bantu hari ini?`;
    }

    for (const [category, data] of Object.entries(this.responses)) {
      if (category === "greeting") continue;
      const keywords = data.keywords || [];
      const responses = data.responses || [];
      if (keywords.some(keyword => message.includes(keyword))) {
        return responses[Math.floor(Math.random() * responses.length)];
      }
    }

    if (message.includes("?")) {
      return "Pertanyaan yang menarik! Bisa Anda jelaskan lebih detail? Atau ketik 'help' untuk melihat apa saja yang bisa saya bantu. 🤔";
    }

    if (message.includes("terima kasih") || message.includes("thanks")) {
      return "Sama-sama! Senang bisa membantu Anda. 😊";
    }

    return "Maaf, saya belum sepenuhnya memahami maksud Anda. 🤔\n\nCoba tanyakan tentang:\n• Materi pembelajaran\n• Tugas dan deadline\n• Jadwal kelas\n\nAtau ketik 'help' untuk bantuan!";
  }
}

// ========== CHATBOT MANAGER (SINGLETON) ==========
export class ChatbotManager {
  static instance = null;

  constructor(config = {}) {
    if (ChatbotManager.instance) {
      return ChatbotManager.instance;
    }

    this.chatbot = document.getElementById(config.chatbotId || 'chatbot');
    this.chatbotBody = document.getElementById(config.bodyId || 'chatbotBody');
    this.chatbotTyping = document.getElementById(config.typingId || 'chatbotTyping');
    this.chatInput = document.getElementById(config.inputId || 'chatInput');
    this.chatSendBtn = document.getElementById(config.sendBtnId || 'chatSendBtn');
    this.chatVoiceBtn = document.getElementById(config.voiceBtnId || 'chatVoiceBtn');
    this.chatbotToggle = document.getElementById(config.toggleId || 'chatbotToggle');
    this.chatbotClose = document.getElementById(config.closeId || 'chatbotClose');
    this.chatbotMinimize = document.getElementById(config.minimizeId || 'chatbotMinimize');
    
    this.apiService = new APIService();
    this.fallbackService = new AIFallbackService();
    this.notificationManager = NotificationManager.getInstance();
    this.initialized = false;
    
    ChatbotManager.instance = this;
  }

  static getInstance() {
    if (!ChatbotManager.instance) {
      ChatbotManager.instance = new ChatbotManager();
    }
    return ChatbotManager.instance;
  }

  initListeners() {
    if (this.initialized) return;

    this.chatbotToggle?.addEventListener("click", () => this.toggle());
    this.chatbotClose?.addEventListener("click", () => this.close());
    this.chatbotMinimize?.addEventListener("click", () => this.close());
    this.chatSendBtn?.addEventListener("click", () => this.sendMessage());
    
    this.chatInput?.addEventListener("keypress", (e) => {
      if (e.key === "Enter") this.sendMessage();
    });

    this.chatVoiceBtn?.addEventListener("click", () => {
      this.addMessage("Maaf, fitur voice input sedang dalam pengembangan. 🎤", false);
    });

    // Quick reply buttons
    document.addEventListener("click", (e) => {
      const btn = e.target.closest('.quick-reply-btn');
      if (btn) {
        const message = btn.getAttribute("data-message");
        this.sendMessage(message);
      }
    });

    this.initialized = true;
  }

  toggle() {
    if (this.chatbot?.classList.contains('active')) {
      this.close();
    } else {
      this.open();
    }
  }

  open() {
    this.chatbot?.classList.add('active');
    setTimeout(() => this.chatInput?.focus(), 300);
  }

  close() {
    this.chatbot?.classList.remove('active');
  }

  async sendMessage(message = null) {
    const userMessage = message || this.chatInput?.value.trim() || "";

    if (!userMessage) return;

    if (userMessage.length > AppConfig.MAX_MESSAGE_LENGTH) {
      this.addMessage("⚠️ Pesan terlalu panjang! Maksimal 1000 karakter.", false);
      return;
    }

    this.addMessage(userMessage, true);
    if (!message && this.chatInput) {
      this.chatInput.value = "";
    }

    this.showTyping();

    try {
      const result = await this.apiService.sendMessage(userMessage);
      
      setTimeout(() => {
        this.hideTyping();
        this.addMessage(result.response, false);
        this.updateAIStatus(result.source);
      }, AppConfig.TYPING_DELAY);

    } catch (error) {
      console.warn("⚠️ Using fallback AI:", error.message);
      
      setTimeout(() => {
        this.hideTyping();
        const localResponse = this.fallbackService.getResponse(userMessage);
        this.addMessage(localResponse, false);
        this.updateAIStatus('local');
      }, AppConfig.TYPING_DELAY);
    }
  }

  addMessage(message, isUser = false) {
    if (!this.chatbotBody) return;

    const messageDiv = document.createElement("div");
    messageDiv.className = `chat-message ${isUser ? "user" : "bot"}`;

    const currentTime = new Date().toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
    });

    messageDiv.innerHTML = `
      <div class="message-avatar">
        <i class="fas fa-${isUser ? "user" : "robot"}"></i>
      </div>
      <div class="message-content">
        <p>${message.replace(/\n/g, "<br>")}</p>
        <span class="message-time">${currentTime}</span>
      </div>
    `;

    const quickReplies = this.chatbotBody.querySelector(".quick-replies");
    if (quickReplies && isUser) {
      quickReplies.remove();
    }

    this.chatbotBody.appendChild(messageDiv);
    this.chatbotBody.scrollTop = this.chatbotBody.scrollHeight;
  }

  showTyping() {
    if (this.chatbotTyping) this.chatbotTyping.style.display = "flex";
    if (this.chatbotBody) this.chatbotBody.scrollTop = this.chatbotBody.scrollHeight;
  }

  hideTyping() {
    if (this.chatbotTyping) this.chatbotTyping.style.display = "none";
  }

  updateAIStatus(status) {
    if (status === 'gemini') {
      document.body.classList.add('proxy-available');
      document.body.classList.remove('proxy-unavailable');
      if (this.chatbotToggle) this.chatbotToggle.title = 'Chat dengan Gemini AI ✨';
    } else {
      document.body.classList.remove('proxy-available');
      document.body.classList.add('proxy-unavailable');
      if (this.chatbotToggle) this.chatbotToggle.title = 'Chat (Local AI Fallback) ⚠️';
    }
  }

  async init() {
    this.initListeners();
    const isAvailable = await this.apiService.testGeminiAPI();
    this.updateAIStatus(isAvailable ? 'gemini' : 'local');
  }

  // Public API methods
  clearChat() {
    if (this.chatbotBody) {
      this.chatbotBody.innerHTML = '';
    }
    this.apiService.clearHistory();
  }

  isOpen() {
    return this.chatbot?.classList.contains('active') || false;
  }
}

// ========== UTILITY FUNCTIONS ==========
export const Utils = {
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  throttle(func, limit) {
    let inThrottle;
    return function() {
      const args = arguments;
      const context = this;
      if (!inThrottle) {
        func.apply(context, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  },

  formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
  },

  formatTime(date) {
    return new Date(date).toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit'
    });
  }
};