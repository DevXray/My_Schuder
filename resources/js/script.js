// ========== CONFIG CLASS ==========
class AppConfig {
  static GEMINI_API_URL = '/api/chat';
  static GEMINI_TEST_URL = '/api/chat/test';
  static MAX_MESSAGE_LENGTH = 1000;
  static MAX_CONVERSATION_HISTORY = 20;
  static TYPING_DELAY = 500;
  static ANIMATION_DURATION = 1000;
}

// ========== API SERVICE CLASS ==========
class APIService {
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
}

// ========== AI FALLBACK SERVICE ==========
class AIFallbackService {
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

// ========== UI MANAGER CLASS ==========
class UIManager {
  constructor(elementId) {
    this.element = document.getElementById(elementId);
  }

  show() {
    if (this.element) {
      this.element.classList.add('active');
    }
  }

  hide() {
    if (this.element) {
      this.element.classList.remove('active');
    }
  }

  toggle() {
    if (this.element) {
      this.element.classList.toggle('active');
    }
  }

  isActive() {
    return this.element?.classList.contains('active') || false;
  }
}

// ========== LOADING SCREEN MANAGER ==========
export class LoadingScreenManager extends UIManager {
  constructor(elementId) {
    super(elementId);
    this.initListeners();
  }

  initListeners() {
    document.addEventListener("DOMContentLoaded", () => {
      window.addEventListener("load", () => this.hideWithTransition());
    });
    
    setTimeout(() => this.hideWithTransition(), 3000);
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

// ========== SIDEBAR MANAGER ==========
class SidebarManager {
  constructor(menuBtnId, sidebarId, overlayId) {
    this.menuBtn = document.getElementById(menuBtnId);
    this.sidebar = new UIManager(sidebarId);
    this.overlay = new UIManager(overlayId);
    this.initListeners();
  }

  initListeners() {
    this.menuBtn?.addEventListener("click", () => this.toggle());
    this.overlay.element?.addEventListener("click", () => this.close());
    
    window.addEventListener("resize", this.handleResize.bind(this));
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
}

// ========== CHATBOT MANAGER ==========
class ChatbotManager {
  constructor(config = {}) {
    this.chatbot = new UIManager(config.chatbotId || 'chatbot');
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
    
    this.initListeners();
  }

  initListeners() {
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

    document.addEventListener("click", (e) => {
      const btn = e.target.closest('.quick-reply-btn');
      if (btn) {
        const message = btn.getAttribute("data-message");
        this.sendMessage(message);
      }
    });
  }

  toggle() {
    if (this.chatbot.isActive()) {
      this.close();
    } else {
      this.open();
    }
  }

  open() {
    this.chatbot.show();
    setTimeout(() => this.chatInput?.focus(), 300);
  }

  close() {
    this.chatbot.hide();
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
    if (!this.chatbotBody) {
      console.log((isUser ? "User: " : "Bot: ") + message);
      return;
    }

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
      console.log("✅ Gemini AI Active");
    } else {
      document.body.classList.remove('proxy-available');
      document.body.classList.add('proxy-unavailable');
      if (this.chatbotToggle) this.chatbotToggle.title = 'Chat (Local AI Fallback) ⚠️';
      console.log("⚠️ Using Local AI Fallback");
    }
  }

  async init() {
    const isAvailable = await this.apiService.testGeminiAPI();
    this.updateAIStatus(isAvailable ? 'gemini' : 'local');
  }
}

// ========== SEARCH MANAGER ==========
class SearchManager {
  constructor(inputId, clearBtnId) {
    this.searchInput = document.getElementById(inputId);
    this.searchClear = document.getElementById(clearBtnId);
    this.initListeners();
  }

  initListeners() {
    this.searchInput?.addEventListener("input", (e) => {
      if (this.searchClear) {
        this.searchClear.style.display = e.target.value.length > 0 ? "block" : "none";
      }
    });

    this.searchClear?.addEventListener("click", () => {
      if (this.searchInput) {
        this.searchInput.value = "";
        this.searchInput.focus();
      }
      if (this.searchClear) {
        this.searchClear.style.display = "none";
      }
    });
  }
}

// ========== ANIMATION MANAGER ==========
class AnimationManager {
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

// ========== NAVIGATION MANAGER ==========
class NavigationManager {
  constructor(selector = '.nav-item:not(.logout)') {
    this.navItems = document.querySelectorAll(selector);
    this.initListeners();
  }

  initListeners() {
    this.navItems.forEach(item => {
      item.addEventListener("click", (e) => this.handleNavClick(e, item));
    });
  }

  handleNavClick(e, item) {
    const href = item.getAttribute('href');
    const isAbsolute = href && href.startsWith('/');

    this.navItems.forEach(nav => nav.classList.remove("active"));
    item.classList.add("active");

    if (window.innerWidth <= 768) {
      const sidebar = new UIManager('sidebar');
      const overlay = new UIManager('overlay');
      sidebar.hide();
      overlay.hide();
    }

    if (!isAbsolute) {
      e.preventDefault();
      const page = item.getAttribute('data-page');
      if (page) {
        console.log(`Navigate to: ${page}`);
      }
    }
  }
}

// ========== EVENT HANDLERS ==========
class EventHandlers {
  static initScheduleJoinButtons() {
    document.querySelectorAll(".schedule-join").forEach(btn => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const scheduleItem = btn.closest(".schedule-item");
        const className = scheduleItem.querySelector("h4").textContent;

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
    this.loadingScreen = new LoadingScreenManager('loadingScreen');
    this.sidebar = new SidebarManager('menuBtn', 'sidebar', 'overlay');
    this.chatbot = new ChatbotManager();
    this.search = new SearchManager('searchInput', 'searchClear');
    this.navigation = new NavigationManager();
    
    this.init();
  }

  async init() {
    console.log("%c🎓 Selamat datang di My Schuder!", "color: #3b82f6; font-size: 24px; font-weight: bold;");
    
    await this.chatbot.init();
    
    AnimationManager.initStatsAnimation();
    EventHandlers.initScheduleJoinButtons();
    EventHandlers.initFilterButtons();
    EventHandlers.initNotificationButton(this.chatbot);
    EventHandlers.initKonamiCode(this.chatbot);
  }
}

export default MySchuderApp;

// ========== INITIALIZE APP ==========
window.addEventListener("load", () => {
  window.mySchuderApp = new MySchuderApp();
});