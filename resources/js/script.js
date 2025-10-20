// DOM Elements
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const chatbotToggle = document.getElementById("chatbotToggle");
const chatbot = document.getElementById("chatbot");
const chatbotClose = document.getElementById("chatbotClose");
const chatbotMinimize = document.getElementById("chatbotMinimize");
const chatInput = document.getElementById("chatInput");
const chatSendBtn = document.getElementById("chatSendBtn");
const chatVoiceBtn = document.getElementById("chatVoiceBtn");
const chatbotBody = document.getElementById("chatbotBody");
const chatbotTyping = document.getElementById("chatbotTyping");
const searchInput = document.getElementById("searchInput");
const searchClear = document.getElementById("searchClear");
const loadingScreen = document.getElementById("loadingScreen");

// ========== GEMINI API CONFIGURATION (BACKEND ONLY) ==========
const GEMINI_API_URL = '/api/chat';
const GEMINI_TEST_URL = '/api/chat/test';

let conversationHistory = [];
let geminiAvailable = false;



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

// ========== AI STATUS INDICATOR ==========
function updateAIStatus(status) {
  if (status === 'gemini') {
    document.body.classList.add('proxy-available');
    document.body.classList.remove('proxy-unavailable');
    if (chatbotToggle) chatbotToggle.title = 'Chat dengan Gemini AI ✨';
    console.log("✅ Gemini AI Active");
  } else {
    document.body.classList.remove('proxy-available');
    document.body.classList.add('proxy-unavailable');
    if (chatbotToggle) chatbotToggle.title = 'Chat (Local AI Fallback) ⚠️';
    console.log("⚠️ Using Local AI Fallback");
  }
}

// ========== SEND MESSAGE TO BACKEND ==========
async function sendMessageToBackend(userMessage) {
  try {
    const response = await fetch(GEMINI_API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({
        message: userMessage,
        conversation_history: conversationHistory
      })
    });

    const data = await response.json();
    
    if (data.success) {
      return {
        response: data.response,
        source: data.source,
        fallback: data.fallback || false
      };
    } else {
      throw new Error(data.message || 'Unknown error');
    }
  } catch (error) {
    console.error('Backend chat error:', error);
    throw error;
  }
}

// ========== TEST GEMINI API ==========
async function testGeminiAPI() {
  try {
    const response = await fetch(GEMINI_TEST_URL, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    const data = await response.json();

    if (data.success) {
      geminiAvailable = true;
      updateAIStatus('gemini');
      console.log("✅ Gemini API tersedia!");
      return true;
    } else {
      throw new Error(data.message || 'API not available');
    }
  } catch (error) {
    console.warn("⚠️ Gemini API tidak tersedia:", error.message);
    geminiAvailable = false;
    updateAIStatus('local');
    return false;
  }
}

// Test connection on load
window.addEventListener("load", async () => {
  hideLoadingScreen();
  setTimeout(() => {
    testGeminiAPI();
  }, 1000);
});

// ========== SIDEBAR TOGGLE ==========
if (menuBtn && sidebar && overlay) {
  menuBtn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
  });
}

if (overlay && sidebar) {
  overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  });
}

// ========== CHATBOT TOGGLE ==========
if (chatbotToggle && chatbot) {
  chatbotToggle.addEventListener("click", () => {
    const isActive = chatbot.classList.contains("active");
    
    if (isActive) {
      chatbot.classList.remove("active");
    } else {
      chatbot.classList.add("active");
      if (chatInput) {
        setTimeout(() => chatInput.focus(), 300);
      }
    }
  });
}

if (chatbotClose && chatbot) {
  chatbotClose.addEventListener("click", () => {
    chatbot.classList.remove("active");
  });
}

if (chatbotMinimize && chatbot) {
  chatbotMinimize.addEventListener("click", () => {
    chatbot.classList.remove("active");
  });
}

// ========== SEARCH FUNCTIONALITY ==========
if (searchInput) {
  searchInput.addEventListener("input", (e) => {
    if (e.target.value.length > 0) {
      if (searchClear) searchClear.style.display = "block";
    } else {
      if (searchClear) searchClear.style.display = "none";
    }
  });

  if (searchClear) {
    searchClear.addEventListener("click", () => {
      searchInput.value = "";
      searchClear.style.display = "none";
      searchInput.focus();
    });
  }
}

// ========== NAVIGATION ==========
const navItems = document.querySelectorAll(".nav-item:not(.logout)");
if (navItems.length) {
  navItems.forEach((item) => {
    item.addEventListener("click", (e) => {
      // If link is an absolute path (starts with '/') let browser navigate normally
      const href = item.getAttribute('href');
      const isAbsolute = href && href.startsWith('/');

      // Update active classes for UI
      navItems.forEach((nav) => nav.classList.remove("active"));
      item.classList.add("active");

      // Close responsive sidebar
      if (window.innerWidth <= 768 && sidebar && overlay) {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
      }

      if (isAbsolute) {
        // allow default navigation to server route
        return; // don't preventDefault
      }

      // For hash/SPA navigation prevent default and handle in-page
      e.preventDefault();
      const page = item.getAttribute('data-page');
      if (page) {
        // If you have in-page sections, implement show/hide here.
        console.log(`Navigate in-page to: ${page}`);
      }
    });
  });
}

// ========== LOCAL AI FALLBACK RESPONSES ==========
const botPersonality = {
  name: "Asisten Virtual AI",
  greeting: [
    "Halo! 👋 Saya siap membantu Anda.",
    "Hi! Ada yang bisa saya bantu?",
    "Selamat datang kembali! 🎉",
  ],
};

const aiResponses = {
  greeting: ["halo", "hi", "hey", "hai", "hello", "selamat pagi", "selamat siang", "selamat malam"],
  materi: {
    keywords: ["materi", "pelajaran", "bahan", "konten", "pembelajaran"],
    responses: [
      "Saat ini ada 24 materi tersedia di sistem. Materi terbaru meliputi:\n\n✅ Algoritma & Struktur Data\n✅ Pemrograman Web\n✅ Basis Data\n✅ Matematika Lanjut\n\nMateri mana yang ingin Anda akses?",
      "Berikut informasi materi yang tersedia:\n\n📚 Total: 24 materi\n🆕 Baru ditambahkan: 3 materi minggu ini\n📈 Progress Anda: 77.5%\n\nApakah Anda ingin melihat detail materi tertentu?",
    ],
  },
  tugas: {
    keywords: ["tugas", "assignment", "pekerjaan rumah", "pr", "deadline"],
    responses: [
      "📝 Status tugas Anda:\n\n✅ Selesai: 12 tugas\n⏳ Aktif: 5 tugas\n⚠ Deadline minggu ini: 2 tugas\n\nTugas yang perlu segera diselesaikan:\n1. Project Akhir Semester (15 Des 2025)\n2. Laporan Praktikum Web (18 Des 2025)\n\nIngin saya tampilkan detail tugasnya?",
    ],
  },
  jadwal: {
    keywords: ["jadwal", "schedule", "kelas", "jam", "waktu"],
    responses: [
      "🗓 Jadwal hari ini:\n\n09:00 - Matematika Lanjut (Ruang A101)\n11:00 - Pemrograman Web (Lab Komp 2)\n14:00 - Basis Data (Ruang B202)\n\nSemua kelas bisa diakses melalui tombol 'Join' di dashboard.",
    ],
  },
  nilai: {
    keywords: ["nilai", "grade", "score", "hasil", "rapor"],
    responses: [
      "📊 Ringkasan Nilai Anda:\n\n🏆 IPK: 3.75\n📈 Semester ini: 3.82\n\nNilai per mata kuliah:\n• Algoritma: 85 (A)\n• Pemrograman Web: 92 (A)\n• Basis Data: 78 (B+)\n• Matematika: 88 (A)\n\nPrestasi yang bagus! 🎉",
    ],
  },
  bantuan: {
    keywords: ["help", "bantuan", "tolong", "gimana", "bagaimana", "cara"],
    responses: [
      "🤖 Saya dapat membantu Anda dengan:\n\n📚 Informasi materi pembelajaran\n📝 Status dan deadline tugas\n🗓 Jadwal kelas harian\n📊 Informasi nilai dan progress\n👥 Info peserta kelas\n⚙ Panduan penggunaan sistem\n\nSilakan tanyakan apa yang Anda butuhkan!",
    ],
  },
  progress: {
    keywords: ["progress", "kemajuan", "perkembangan", "pencapaian"],
    responses: [
      "📈 Progress Pembelajaran Anda:\n\n• Algoritma & Struktur Data: 75%\n• Pemrograman Web: 90%\n• Basis Data: 60%\n• Matematika Lanjut: 85%\n\nRata-rata progress: 77.5%\n\nTerus semangat! 🎯",
    ],
  },
  motivasi: {
    keywords: ["capek", "lelah", "semangat", "motivasi", "tired"],
    responses: [
      "💪 Jangan menyerah! Setiap usaha yang Anda lakukan hari ini adalah investasi untuk masa depan yang lebih baik. Anda sudah sampai sejauh ini, terus lanjutkan! 🌟",
    ],
  },
};

function getAIResponse(userMessage) {
  const message = userMessage.toLowerCase().trim();

  if (aiResponses.greeting.some((greet) => message.includes(greet))) {
    return botPersonality.greeting[Math.floor(Math.random() * botPersonality.greeting.length)] + " Ada yang bisa saya bantu hari ini?";
  }

  for (const [category, data] of Object.entries(aiResponses)) {
    if (category === "greeting") continue;
    const keywords = data.keywords || [];
    const responses = data.responses || [];
    if (keywords.some((keyword) => message.includes(keyword))) {
      return responses[Math.floor(Math.random() * responses.length)];
    }
  }

  if (message.includes("?")) {
    return "Pertanyaan yang menarik! Saya mencoba memahami pertanyaan Anda. Bisa Anda jelaskan lebih detail? Atau ketik 'help' untuk melihat apa saja yang bisa saya bantu. 🤔";
  }

  if (message.includes("terima kasih") || message.includes("thanks")) {
    return "Sama-sama! Senang bisa membantu Anda. 😊 Ada hal lain yang bisa saya bantu?";
  }

  return "Maaf, saya belum sepenuhnya memahami maksud Anda. 🤔\n\nCoba tanyakan tentang:\n• Materi pembelajaran\n• Tugas dan deadline\n• Jadwal kelas\n• Nilai dan progress\n\nAtau ketik 'help' untuk melihat semua yang bisa saya bantu!";
}

// ========== CHAT MESSAGE FUNCTIONS ==========
function addMessage(message, isUser = false) {
  if (!chatbotBody) {
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

  const quickReplies = chatbotBody.querySelector(".quick-replies");
  if (quickReplies && isUser) {
    quickReplies.remove();
  }

  chatbotBody.appendChild(messageDiv);
  chatbotBody.scrollTop = chatbotBody.scrollHeight;
}

function showTypingIndicator() {
  if (chatbotTyping) chatbotTyping.style.display = "flex";
  if (chatbotBody) chatbotBody.scrollTop = chatbotBody.scrollHeight;
}

function hideTypingIndicator() {
  if (chatbotTyping) chatbotTyping.style.display = "none";
}

// ========== MAIN SEND MESSAGE FUNCTION ==========
async function sendMessage(message = null) {
  const userMessage = message || (chatInput ? chatInput.value.trim() : "");

  if (!userMessage) return;

  if (userMessage.length > 1000) {
    addMessage("⚠️ Pesan terlalu panjang! Maksimal 1000 karakter.", false);
    return;
  }

  addMessage(userMessage, true);
  if (!message && chatInput) {
    chatInput.value = "";
  }

  showTypingIndicator();

  try {
    // Call backend API
    const result = await sendMessageToBackend(userMessage);
    
    // Update conversation history
    conversationHistory.push({ role: 'user', text: userMessage });
    conversationHistory.push({ role: 'model', text: result.response });
    
    // Keep only last 20 messages
    if (conversationHistory.length > 20) {
      conversationHistory = conversationHistory.slice(-20);
    }

    setTimeout(() => {
      hideTypingIndicator();
      addMessage(result.response, false);
      updateAIStatus(result.source);
    }, 500);

  } catch (error) {
    console.warn("⚠️ Backend API error, using local fallback:", error.message);
    
    geminiAvailable = false;
    updateAIStatus('local');
    
    setTimeout(() => {
      hideTypingIndicator();
      const localResponse = getAIResponse(userMessage);
      addMessage(localResponse, false);
    }, 500);
  }
}

// ========== EVENT LISTENERS ==========
if (chatSendBtn) {
  chatSendBtn.addEventListener("click", () => sendMessage());
}

if (chatInput) {
  chatInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      sendMessage();
    }
  });
}

document.addEventListener("click", (e) => {
  const btn = e.target.closest && e.target.closest(".quick-reply-btn");
  if (btn) {
    const message = btn.getAttribute("data-message");
    sendMessage(message);
  }
});

if (chatVoiceBtn) {
  chatVoiceBtn.addEventListener("click", () => {
    addMessage("Maaf, fitur voice input sedang dalam pengembangan. Silakan ketik pesan Anda. 🎤", false);
  });
}

// ========== STATS ANIMATION ==========
function animateValue(element, start, end, duration) {
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

const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      if (entry.target.classList.contains("stat-number")) {
        const target = parseInt(entry.target.getAttribute("data-target"));
        animateValue(entry.target, 0, target, 1000);
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

document.querySelectorAll(".stat-number").forEach((stat) => {
  observer.observe(stat);
});

document.querySelectorAll(".progress-fill").forEach((progress) => {
  observer.observe(progress);
});

// ========== PROGRESS FILTERS ==========
const filterBtns = document.querySelectorAll(".filter-btn");
if (filterBtns.length) {
  filterBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      filterBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
    });
  });
}

// ========== THEME TOGGLE ==========
const themeToggle = document.getElementById("themeToggle");
if (themeToggle) {
  themeToggle.addEventListener("change", () => {
    if (themeToggle.checked) {
      addMessage("Fitur dark mode sedang dalam pengembangan. Coming soon! 🌙", false);
      if (chatbot && !chatbot.classList.contains("active")) {
        chatbot.classList.add("active");
      }
      setTimeout(() => {
        themeToggle.checked = false;
      }, 500);
    }
  });
}

// ========== NOTIFICATION BUTTON ==========
const notificationBtn = document.getElementById("notificationBtn");
if (notificationBtn) {
  notificationBtn.addEventListener("click", () => {
    addMessage("📬 Anda memiliki 3 notifikasi baru:\n\n1. ⚠ Deadline tugas: 3 hari lagi\n2. 📚 Materi baru telah ditambahkan\n3. 🎉 Selamat! Anda mencapai progress 75%", false);
    if (chatbot && !chatbot.classList.contains("active")) {
      chatbot.classList.add("active");
    }
  });
}

// ========== SCHEDULE JOIN BUTTONS ==========
document.querySelectorAll(".schedule-join").forEach((btn) => {
  btn.addEventListener("click", (e) => {
    e.stopPropagation();
    const scheduleItem = btn.closest(".schedule-item");
    const className = scheduleItem.querySelector("h4").textContent;

    btn.textContent = "Joining...";
    btn.disabled = true;

    setTimeout(() => {
      btn.textContent = "Joined ✓";
      btn.style.opacity = "0.7";

      setTimeout(() => {
        addMessage(`✅ Anda berhasil join kelas "${className}". Selamat belajar! 📚`, false);
        if (!chatbot.classList.contains("active")) {
          chatbot.classList.add("active");
        }
      }, 500);
    }, 1500);
  });
});

// ========== AUTO-CLOSE SIDEBAR ON RESIZE ==========
let resizeTimer;
window.addEventListener("resize", () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    if (window.innerWidth > 768 && sidebar && overlay) {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    }
  }, 250);
});

// ========== CONSOLE MESSAGES ==========
console.log("%c🎓 Selamat datang di My Schuder!", "color: #3b82f6; font-size: 24px; font-weight: bold;");
console.log("%c✨ Portal Pembelajaran Modern dengan Gemini AI", "color: #f97316; font-size: 16px; font-weight: bold;");
console.log("%c💡 Tip: Coba chat dengan AI assistant untuk bantuan cepat!", "color: #10b981; font-size: 14px;");

// ========== EASTER EGG - KONAMI CODE ==========
let konamiCode = [];
const konamiPattern = ["ArrowUp", "ArrowUp", "ArrowDown", "ArrowDown", "ArrowLeft", "ArrowRight", "ArrowLeft", "ArrowRight", "b", "a"];

document.addEventListener("keydown", (e) => {
  konamiCode.push(e.key);
  konamiCode = konamiCode.slice(-10);

  if (konamiCode.join("") === konamiPattern.join("")) {
    addMessage("🎮 Easter Egg Found! Anda menemukan kode rahasia! Selamat! 🎉✨", false);
    if (!chatbot.classList.contains("active")) {
      chatbot.classList.add("active");
    }
    konamiCode = [];
  }
});