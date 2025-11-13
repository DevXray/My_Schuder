// ========================================
// CORE.JS - Fungsi Umum untuk Semua Halaman
// ========================================

// Global Variables
const AppConfig = {
    GEMINI_API_URL: '/api/chat',
    GEMINI_TEST_URL: '/api/chat/test',
    conversationHistory: [],
    geminiAvailable: false
};

// ========================================
// DOM Element Getters (Lazy)
// ========================================
const DOM = {
    get menuBtn() { return document.getElementById("menuBtn"); },
    get sidebar() { return document.getElementById("sidebar"); },
    get overlay() { return document.getElementById("overlay"); },
    get searchInput() { return document.getElementById("searchInput"); },
    get searchClear() { return document.getElementById("searchClear"); },
    get loadingScreen() { return document.getElementById("loadingScreen"); },
    get notificationBtn() { return document.getElementById("notificationBtn"); },
    get themeToggle() { return document.getElementById("themeToggle"); }
};

// ========================================
// LOADING SCREEN
// ========================================
function hideLoadingScreen() {
    const loadingScreen = DOM.loadingScreen;
    if (loadingScreen && !loadingScreen.classList.contains("hidden")) {
        loadingScreen.classList.add("hidden");
        setTimeout(() => {
            loadingScreen.style.display = "none";
        }, 500);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    window.addEventListener("load", hideLoadingScreen);
    setTimeout(hideLoadingScreen, 3000);
});

// ========================================
// SIDEBAR TOGGLE
// ========================================
function initSidebar() {
    const { menuBtn, sidebar, overlay } = DOM;
    
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
}

// ========================================
// SEARCH FUNCTIONALITY
// ========================================
function initSearch(filterCallback) {
    const { searchInput, searchClear } = DOM;
    
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            const searchTerm = e.target.value.toLowerCase();
            
            if (searchClear) {
                searchClear.style.display = searchTerm.length > 0 ? "block" : "none";
            }

            if (filterCallback && typeof filterCallback === 'function') {
                filterCallback(searchTerm);
            }
        });

        if (searchClear) {
            searchClear.addEventListener("click", () => {
                searchInput.value = "";
                searchClear.style.display = "none";
                if (filterCallback && typeof filterCallback === 'function') {
                    filterCallback("");
                }
            });
        }
    }
}

// ========================================
// NOTIFICATION SYSTEM
// ========================================
function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    
    const icon = type === "success" ? "check-circle" : 
                 type === "warning" ? "exclamation-triangle" : "info-circle";
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    const bgColor = type === "success" ? "var(--success)" : 
                    type === "warning" ? "var(--warning)" : "var(--primary-blue)";
    
    notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 20px;
        background: ${bgColor};
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
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = "slideOut 0.3s ease";
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ========================================
// STATS ANIMATION
// ========================================
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

function initStatsAnimation() {
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
}

// ========================================
// RESPONSIVE HANDLING
// ========================================
let resizeTimer;
function handleResize() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        const { sidebar, overlay } = DOM;
        if (window.innerWidth > 768 && sidebar && overlay) {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        }
    }, 250);
}

// ========================================
// THEME TOGGLE
// ========================================
function initThemeToggle() {
    const themeToggle = DOM.themeToggle;
    if (themeToggle) {
        themeToggle.addEventListener("change", () => {
            if (themeToggle.checked) {
                showNotification("Fitur dark mode sedang dalam pengembangan. Coming soon! 🌙", "info");
                setTimeout(() => {
                    themeToggle.checked = false;
                }, 500);
            }
        });
    }
}

// ========================================
// NOTIFICATION BUTTON
// ========================================
function initNotificationButton() {
    const notificationBtn = DOM.notificationBtn;
    if (notificationBtn) {
        notificationBtn.addEventListener("click", () => {
            showNotification("📬 Anda memiliki 3 notifikasi baru", "info");
        });
    }
}

// ========================================
// CSS ANIMATIONS
// ========================================
function addCSSAnimations() {
    if (document.querySelector("#coreAnimations")) return;
    
    const style = document.createElement('style');
    style.id = "coreAnimations";
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

// ========================================
// INITIALIZATION
// ========================================
function initCore() {
    initSidebar();
    initThemeToggle();
    initNotificationButton();
    initStatsAnimation();
    addCSSAnimations();
    
    window.addEventListener("resize", handleResize);
    
    console.log("%c✅ Core.js Loaded Successfully", "color: #10b981; font-size: 14px; font-weight: bold;");
}

// Auto-init when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCore);
} else {
    initCore();
}

// Export functions for use in other modules
window.AppCore = {
    showNotification,
    hideLoadingScreen,
    animateValue,
    DOM
};