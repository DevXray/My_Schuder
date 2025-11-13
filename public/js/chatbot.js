// ========================================
// CHATBOT.JS - Gemini AI Integration
// ========================================

const ChatbotModule = (() => {
    // Private variables
    let conversationHistory = [];
    let geminiAvailable = false;

    // DOM Elements
    const getDOM = () => ({
        chatbot: document.getElementById("chatbot"),
        chatbotToggle: document.getElementById("chatbotToggle"),
        chatbotClose: document.getElementById("chatbotClose"),
        chatbotMinimize: document.getElementById("chatbotMinimize"),
        chatInput: document.getElementById("chatInput"),
        chatSendBtn: document.getElementById("chatSendBtn"),
        chatVoiceBtn: document.getElementById("chatVoiceBtn"),
        chatbotBody: document.getElementById("chatbotBody"),
        chatbotTyping: document.getElementById("chatbotTyping")
    });

    // ========================================
    // AI STATUS INDICATOR
    // ========================================
    function updateAIStatus(status) {
        if (status === 'gemini') {
            document.body.classList.add('proxy-available');
            document.body.classList.remove('proxy-unavailable');
            const toggle = getDOM().chatbotToggle;
            if (toggle) toggle.title = 'Chat dengan Gemini AI ✨';
            console.log("✅ Gemini AI Active");
        } else {
            document.body.classList.remove('proxy-available');
            document.body.classList.add('proxy-unavailable');
            const toggle = getDOM().chatbotToggle;
            if (toggle) toggle.title = 'Chat (Local AI Fallback) ⚠️';
            console.log("⚠️ Using Local AI Fallback");
        }
    }

    // ========================================
    // BACKEND API CALLS
    // ========================================
    async function sendMessageToBackend(userMessage) {
        try {
            const response = await fetch('/api/chat', {
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

    async function testGeminiAPI() {
        try {
            const response = await fetch('/api/chat/test', {
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

    // ========================================
    // LOCAL AI FALLBACK
    // ========================================
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

    // ========================================
    // CHAT UI FUNCTIONS
    // ========================================
    function addMessage(message, isUser = false) {
        const chatbotBody = getDOM().chatbotBody;
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
        const chatbotTyping = getDOM().chatbotTyping;
        if (chatbotTyping) chatbotTyping.style.display = "flex";
        const chatbotBody = getDOM().chatbotBody;
        if (chatbotBody) chatbotBody.scrollTop = chatbotBody.scrollHeight;
    }

    function hideTypingIndicator() {
        const chatbotTyping = getDOM().chatbotTyping;
        if (chatbotTyping) chatbotTyping.style.display = "none";
    }

    // ========================================
    // SEND MESSAGE
    // ========================================
    async function sendMessage(message = null) {
        const chatInput = getDOM().chatInput;
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
            const result = await sendMessageToBackend(userMessage);
            
            conversationHistory.push({ role: 'user', text: userMessage });
            conversationHistory.push({ role: 'model', text: result.response });
            
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

    // ========================================
    // EVENT LISTENERS
    // ========================================
    function initChatbotUI() {
        const dom = getDOM();

        // Toggle chatbot
        if (dom.chatbotToggle && dom.chatbot) {
            dom.chatbotToggle.addEventListener("click", () => {
                const isActive = dom.chatbot.classList.contains("active");
                
                if (isActive) {
                    dom.chatbot.classList.remove("active");
                } else {
                    dom.chatbot.classList.add("active");
                    if (dom.chatInput) {
                        setTimeout(() => dom.chatInput.focus(), 300);
                    }
                }
            });
        }

        // Close button
        if (dom.chatbotClose && dom.chatbot) {
            dom.chatbotClose.addEventListener("click", () => {
                dom.chatbot.classList.remove("active");
            });
        }

        // Minimize button
        if (dom.chatbotMinimize && dom.chatbot) {
            dom.chatbotMinimize.addEventListener("click", () => {
                dom.chatbot.classList.remove("active");
            });
        }

        // Send button
        if (dom.chatSendBtn) {
            dom.chatSendBtn.addEventListener("click", () => sendMessage());
        }

        // Enter key
        if (dom.chatInput) {
            dom.chatInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    sendMessage();
                }
            });
        }

        // Quick reply buttons
        document.addEventListener("click", (e) => {
            const btn = e.target.closest && e.target.closest(".quick-reply-btn");
            if (btn) {
                const message = btn.getAttribute("data-message");
                sendMessage(message);
            }
        });

        // Voice button
        if (dom.chatVoiceBtn) {
            dom.chatVoiceBtn.addEventListener("click", () => {
                addMessage("Maaf, fitur voice input sedang dalam pengembangan. Silakan ketik pesan Anda. 🎤", false);
            });
        }
    }

    // ========================================
    // INITIALIZATION
    // ========================================
    function init() {
        initChatbotUI();
        
        // Test Gemini API after 1 second
        setTimeout(() => {
            testGeminiAPI();
        }, 1000);

        console.log("✅ Chatbot Module Loaded");
    }

    // Auto-init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Public API
    return {
        sendMessage,
        addMessage,
        testGeminiAPI,
        updateAIStatus
    };
})();

// Export for global access
window.Chatbot = ChatbotModule;