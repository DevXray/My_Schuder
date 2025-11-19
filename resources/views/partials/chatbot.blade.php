<!-- Chatbot -->
    <div class="chatbot" id="chatbot">
        <div class="chatbot-header">
            <div class="chatbot-title">
                <i class="fas fa-robot"></i>
                <span>AI kecerdasan buatan artificial intelligence</span>
            </div>
            
        </div>
        <div class="chatbot-body" id="chatbotBody">
            <div class="chat-message bot">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <p>Halo! 👋 Saya Asisten Virtual AI. Ada yang bisa saya bantu hari ini?</p>
                    <span class="message-time">Baru saja</span>
                </div>
            </div>
            <div class="quick-replies">
                <button class="quick-reply-btn" data-message="Info materi">
                    <i class="fas fa-book"></i> Info Materi
                </button>
                <button class="quick-reply-btn" data-message="Status tugas">
                    <i class="fas fa-tasks"></i> Status Tugas
                </button>
                <button class="quick-reply-btn" data-message="Jadwal hari ini">
                    <i class="fas fa-calendar"></i> Jadwal
                </button>
            </div>
        </div>
        <div class="chatbot-typing" id="chatbotTyping" style="display: none;">
            <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <span class="typing-text">Asisten sedang mengetik...</span>
        </div>
        <div class="chatbot-footer">
            <input type="text" id="chatInput" placeholder="Ketik pesan..." autocomplete="off">
            <button class="chat-voice-btn" id="chatVoiceBtn" aria-label="Voice Input">
                <i class="fas fa-microphone"></i>
            </button>
            <button class="chat-send-btn" id="chatSendBtn" aria-label="Send Message">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <button class="chatbot-toggle" id="chatbotToggle" aria-label="Open Chat">
        <i class="fas fa-comments"></i>
        <span class="chat-notification">1</span>
        <span class="status-dot" aria-hidden="true"></span>
    </button>