// Jadwal Page JavaScript

// DOM Elements
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const searchInput = document.getElementById("searchInput");
const searchClear = document.getElementById("searchClear");
const prevWeekBtn = document.getElementById("prevWeek");
const nextWeekBtn = document.getElementById("nextWeek");
const currentWeekText = document.getElementById("currentWeek");
const viewBtns = document.querySelectorAll(".view-btn");
const exportBtn = document.getElementById("exportBtn");
const addScheduleBtn = document.getElementById("addScheduleBtn");

// Current week state
let currentWeekOffset = 0;

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

        filterSchedule(searchTerm);
    });

    if (searchClear) {
        searchClear.addEventListener("click", () => {
            searchInput.value = "";
            searchClear.style.display = "none";
            filterSchedule("");
        });
    }
}

// Filter Schedule
function filterSchedule(searchTerm) {
    const classItems = document.querySelectorAll(".class-item");
    const upcomingCards = document.querySelectorAll(".upcoming-card");
    
    classItems.forEach(item => {
        const className = item.querySelector(".class-name").textContent.toLowerCase();
        const lecturer = item.querySelector(".class-lecturer").textContent.toLowerCase();
        const room = item.querySelector(".class-room").textContent.toLowerCase();
        
        if (searchTerm === "" || className.includes(searchTerm) || 
            lecturer.includes(searchTerm) || room.includes(searchTerm)) {
            item.style.display = "flex";
            item.style.animation = "fadeIn 0.3s ease";
        } else {
            item.style.display = "none";
        }
    });

    upcomingCards.forEach(card => {
        const title = card.querySelector("h4").textContent.toLowerCase();
        const lecturer = card.querySelector(".card-lecturer").textContent.toLowerCase();
        
        if (searchTerm === "" || title.includes(searchTerm) || lecturer.includes(searchTerm)) {
            card.style.display = "block";
            card.style.animation = "fadeIn 0.3s ease";
        } else {
            card.style.display = "none";
        }
    });
}

// Week Navigation
if (prevWeekBtn && nextWeekBtn) {
    prevWeekBtn.addEventListener("click", () => {
        currentWeekOffset--;
        updateWeekDisplay();
        showNotification("Menampilkan minggu sebelumnya", "info");
    });

    nextWeekBtn.addEventListener("click", () => {
        currentWeekOffset++;
        updateWeekDisplay();
        showNotification("Menampilkan minggu berikutnya", "info");
    });
}

// Update Week Display
function updateWeekDisplay() {
    const today = new Date();
    today.setDate(today.getDate() + (currentWeekOffset * 7));
    
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - today.getDay() + 1); // Monday
    
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6); // Sunday
    
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const startDay = startOfWeek.getDate();
    const endDay = endOfWeek.getDate();
    const startMonth = months[startOfWeek.getMonth()];
    const endMonth = months[endOfWeek.getMonth()];
    const year = endOfWeek.getFullYear();
    
    let weekText = `${startDay}`;
    if (startMonth === endMonth) {
        weekText += ` - ${endDay} ${endMonth} ${year}`;
    } else {
        weekText += ` ${startMonth} - ${endDay} ${endMonth} ${year}`;
    }
    
    if (currentWeekText) {
        currentWeekText.textContent = weekText;
    }

    // Update day dates
    const dayHeaders = document.querySelectorAll(".day-column .day-header");
    dayHeaders.forEach((header, index) => {
        const currentDay = new Date(startOfWeek);
        currentDay.setDate(startOfWeek.getDate() + index);
        
        const dayDate = header.querySelector(".day-date");
        if (dayDate) {
            dayDate.textContent = `${currentDay.getDate()} ${months[currentDay.getMonth()].substring(0, 3)}`;
        }

        // Update today badge
        const isToday = currentDay.toDateString() === new Date().toDateString();
        if (isToday && !header.classList.contains("today")) {
            header.classList.add("today");
            if (!header.querySelector(".today-badge")) {
                const badge = document.createElement("span");
                badge.className = "today-badge";
                badge.textContent = "Hari Ini";
                header.appendChild(badge);
            }
        } else if (!isToday) {
            header.classList.remove("today");
            const badge = header.querySelector(".today-badge");
            if (badge) badge.remove();
        }
    });
}

// View Switcher
viewBtns.forEach(btn => {
    btn.addEventListener("click", () => {
        const view = btn.getAttribute("data-view");
        
        viewBtns.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        
        if (view === "day") {
            showNotification("Tampilan harian sedang dalam pengembangan", "info");
        } else if (view === "month") {
            showNotification("Tampilan bulanan sedang dalam pengembangan", "info");
        }
    });
});

// Export Button
if (exportBtn) {
    exportBtn.addEventListener("click", () => {
        showExportDialog();
    });
}

// Show Export Dialog
function showExportDialog() {
    const dialog = document.createElement("div");
    dialog.className = "export-dialog";
    dialog.innerHTML = `
        <div class="dialog-overlay"></div>
        <div class="dialog-content">
            <div class="dialog-header">
                <h3><i class="fas fa-download"></i> Export Jadwal</h3>
                <button class="dialog-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="dialog-body">
                <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">
                    Pilih format export untuk jadwal Anda:
                </p>
                <div class="export-options">
                    <button class="export-option" data-format="pdf">
                        <i class="fas fa-file-pdf"></i>
                        <span>PDF Document</span>
                    </button>
                    <button class="export-option" data-format="excel">
                        <i class="fas fa-file-excel"></i>
                        <span>Excel Spreadsheet</span>
                    </button>
                    <button class="export-option" data-format="ical">
                        <i class="fas fa-calendar-alt"></i>
                        <span>iCalendar (.ics)</span>
                    </button>
                    <button class="export-option" data-format="image">
                        <i class="fas fa-image"></i>
                        <span>Image (PNG)</span>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(dialog);
    
    const closeBtn = dialog.querySelector(".dialog-close");
    const dialogOverlay = dialog.querySelector(".dialog-overlay");
    const exportOptions = dialog.querySelectorAll(".export-option");
    
    const closeDialog = () => dialog.remove();
    
    closeBtn.addEventListener("click", closeDialog);
    dialogOverlay.addEventListener("click", closeDialog);
    
    exportOptions.forEach(option => {
        option.addEventListener("click", () => {
            const format = option.getAttribute("data-format");
            closeDialog();
            showNotification(`Mengekspor jadwal ke format ${format.toUpperCase()}...`, "success");
            
            // Simulate export
            setTimeout(() => {
                showNotification(`Jadwal berhasil diekspor! ✅`, "success");
            }, 2000);
        });
    });
    
    addExportDialogStyles();
}

// Add Export Dialog Styles
function addExportDialogStyles() {
    if (document.querySelector("#exportDialogStyles")) return;
    
    const style = document.createElement("style");
    style.id = "exportDialogStyles";
    style.textContent = `
        .export-dialog {
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        
        .dialog-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        .dialog-content {
            position: relative;
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.3s ease;
        }
        
        .dialog-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8fafc, #e0f2fe);
        }
        
        .dialog-header h3 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-primary);
            font-size: 1.3rem;
        }
        
        .dialog-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .dialog-close:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .dialog-body {
            padding: 2rem;
        }
        
        .export-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .export-option {
            padding: 1.5rem;
            background: #f8fafc;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            text-align: center;
        }
        
        .export-option:hover {
            background: var(--light-blue);
            border-color: var(--primary-blue);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        
        .export-option i {
            font-size: 2.5rem;
            color: var(--primary-blue);
        }
        
        .export-option span {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
}

// Add Schedule Button
if (addScheduleBtn) {
    addScheduleBtn.addEventListener("click", () => {
        showNotification("Fitur tambah jadwal akan segera hadir! 🎉", "info");
    });
}

// Class Item Click
document.addEventListener("click", (e) => {
    const classItem = e.target.closest(".class-item");
    if (classItem) {
        const className = classItem.querySelector(".class-name").textContent;
        const time = classItem.querySelector(".class-time").textContent;
        const room = classItem.querySelector(".class-room").textContent;
        const lecturer = classItem.querySelector(".class-lecturer").textContent;
        
        showClassDetail(className, time, room, lecturer);
    }
});

// Show Class Detail
function showClassDetail(className, time, room, lecturer) {
    const detail = `
📚 ${className}

⏰ ${time}
📍 ${room}
👨‍🏫 ${lecturer}

Klik OK untuk join kelas atau Cancel untuk kembali.
    `;
    
    if (confirm(detail)) {
        showNotification(`Joining kelas: ${className}`, "success");
    }
}

// Join Button Click
document.addEventListener("click", (e) => {
    const joinBtn = e.target.closest(".btn-join");
    if (joinBtn) {
        const card = joinBtn.closest(".upcoming-card");
        const className = card.querySelector("h4").textContent;
        
        if (joinBtn.classList.contains("active")) {
            showNotification(`Joining kelas: ${className}`, "success");
            setTimeout(() => {
                showNotification("Anda telah terhubung ke kelas! 🎓", "success");
            }, 1500);
        } else if (joinBtn.textContent.includes("Reminder")) {
            showNotification(`Reminder diset untuk: ${className}`, "success");
        } else {
            showClassDetail(
                className,
                card.querySelector(".card-info span:first-child").textContent,
                card.querySelector(".card-info span:last-child").textContent,
                card.querySelector(".card-lecturer").textContent
            );
        }
    }
});

// Notification System
function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    
    const icon = type === "success" ? "check-circle" : type === "warning" ? "exclamation-triangle" : "info-circle";
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    const bgColor = type === "success" ? "var(--success)" : type === "warning" ? "var(--warning)" : "var(--primary-blue)";
    
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

// Add animations
const style = document.createElement("style");
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

// Initialize
document.addEventListener("DOMContentLoaded", () => {
    updateWeekDisplay();
});

console.log("✅ Jadwal Page Loaded Successfully");