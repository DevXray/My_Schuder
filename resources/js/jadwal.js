// ============================================
// JADWAL.JS - Jadwal Page Module (FIXED)
// ============================================

import { 
  AppConfig,
  SidebarManager,
  SearchManager,
  NotificationManager,
  ChatbotManager
} from './core.js';

// ========== WEEK NAVIGATOR ==========
class WeekNavigator {
  constructor() {
    this.prevBtn = document.getElementById('prevWeek');
    this.nextBtn = document.getElementById('nextWeek');
    this.currentWeekText = document.getElementById('currentWeek');
    this.currentWeekOffset = 0;
    
    this.prevHandler = () => this.navigatePrev();
    this.nextHandler = () => this.navigateNext();
    
    this.initListeners();
  }

  initListeners() {
    this.prevBtn?.addEventListener("click", this.prevHandler);
    this.nextBtn?.addEventListener("click", this.nextHandler);
  }

  navigatePrev() {
    this.currentWeekOffset--;
    this.updateWeekDisplay();
    NotificationManager.getInstance().show("Menampilkan minggu sebelumnya", "info");
  }

  navigateNext() {
    this.currentWeekOffset++;
    this.updateWeekDisplay();
    NotificationManager.getInstance().show("Menampilkan minggu berikutnya", "info");
  }

  updateWeekDisplay() {
    const today = new Date();
    today.setDate(today.getDate() + (this.currentWeekOffset * 7));
    
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - today.getDay() + 1);
    
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);
    
    const startDay = startOfWeek.getDate();
    const endDay = endOfWeek.getDate();
    const startMonth = AppConfig.MONTHS[startOfWeek.getMonth()];
    const endMonth = AppConfig.MONTHS[endOfWeek.getMonth()];
    const year = endOfWeek.getFullYear();
    
    let weekText = `${startDay}`;
    if (startMonth === endMonth) {
      weekText += ` - ${endDay} ${endMonth} ${year}`;
    } else {
      weekText += ` ${startMonth} - ${endDay} ${endMonth} ${year}`;
    }
    
    if (this.currentWeekText) {
      this.currentWeekText.textContent = weekText;
    }

    this.updateDayHeaders(startOfWeek);
  }

  updateDayHeaders(startOfWeek) {
    const dayHeaders = document.querySelectorAll(".day-column .day-header");
    dayHeaders.forEach((header, index) => {
      const currentDay = new Date(startOfWeek);
      currentDay.setDate(startOfWeek.getDate() + index);
      
      const dayDate = header.querySelector(".day-date");
      if (dayDate) {
        dayDate.textContent = `${currentDay.getDate()} ${AppConfig.MONTHS[currentDay.getMonth()].substring(0, 3)}`;
      }

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
        header.querySelector(".today-badge")?.remove();
      }
    });
  }

  init() {
    this.updateWeekDisplay();
  }

  destroy() {
    this.prevBtn?.removeEventListener("click", this.prevHandler);
    this.nextBtn?.removeEventListener("click", this.nextHandler);
  }
}

// ========== VIEW SWITCHER ==========
class ViewSwitcher {
  constructor(notificationManager) {
    this.notificationManager = notificationManager;
    this.viewBtns = document.querySelectorAll('.view-btn');
    this.boundHandlers = new Map();
    this.initListeners();
  }

  initListeners() {
    this.viewBtns.forEach(btn => {
      const handler = () => this.handleViewChange(btn);
      this.boundHandlers.set(btn, handler);
      btn.addEventListener("click", handler);
    });
  }

  handleViewChange(btn) {
    const view = btn.getAttribute("data-view");
    
    this.viewBtns.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    
    if (view === "day") {
      this.notificationManager.show("Tampilan harian sedang dalam pengembangan", "info");
    } else if (view === "month") {
      this.notificationManager.show("Tampilan bulanan sedang dalam pengembangan", "info");
    }
  }

  destroy() {
    this.viewBtns.forEach(btn => {
      const handler = this.boundHandlers.get(btn);
      if (handler) {
        btn.removeEventListener("click", handler);
      }
    });
    this.boundHandlers.clear();
  }
}

// ========== EXPORT DIALOG MANAGER ==========
class ExportDialogManager {
  constructor(notificationManager) {
    this.notificationManager = notificationManager;
    this.dialog = null;
    this.boundHandlers = {
      close: null,
      cancel: null,
      overlayClick: null,
      exportHandlers: []
    };
    this.ensureStyles();
  }

  show() {
    // ✅ PENTING: Hapus dialog lama jika ada
    this.close();
    
    this.dialog = document.createElement("div");
    this.dialog.className = "export-dialog";
    this.dialog.innerHTML = `
      <div class="dialog-overlay"></div>
      <div class="dialog-content">
        <div class="dialog-header">
          <h3><i class="fas fa-download"></i> Export Jadwal</h3>
          <button class="dialog-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="dialog-body">
          <p style="margin-bottom: 1.5rem; color: #64748b;">
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
    
    document.body.appendChild(this.dialog);
    this.initDialogListeners();
  }

  initDialogListeners() {
    const closeBtn = this.dialog.querySelector(".dialog-close");
    const dialogOverlay = this.dialog.querySelector(".dialog-overlay");
    const exportOptions = this.dialog.querySelectorAll(".export-option");
    
    // ✅ Store handlers
    this.boundHandlers.close = () => this.close();
    this.boundHandlers.overlayClick = () => this.close();
    
    closeBtn?.addEventListener("click", this.boundHandlers.close);
    dialogOverlay?.addEventListener("click", this.boundHandlers.overlayClick);
    
    exportOptions.forEach(option => {
      const handler = () => this.handleExport(option);
      this.boundHandlers.exportHandlers.push({ element: option, handler });
      option.addEventListener("click", handler);
    });
  }

  handleExport(option) {
    const format = option.getAttribute("data-format");
    this.close();
    this.notificationManager.show(`Mengekspor jadwal ke format ${format.toUpperCase()}...`, "success");
    
    setTimeout(() => {
      this.notificationManager.show(`Jadwal berhasil diekspor! ✅`, "success");
    }, 2000);
  }

  close() {
    // ✅ Remove ALL event listeners
    if (this.dialog) {
      const closeBtn = this.dialog.querySelector(".dialog-close");
      const dialogOverlay = this.dialog.querySelector(".dialog-overlay");
      
      closeBtn?.removeEventListener("click", this.boundHandlers.close);
      dialogOverlay?.removeEventListener("click", this.boundHandlers.overlayClick);
      
      this.boundHandlers.exportHandlers.forEach(({ element, handler }) => {
        element?.removeEventListener("click", handler);
      });
      
      this.dialog.remove();
      this.dialog = null;
      this.boundHandlers.exportHandlers = [];
    }
  }

  ensureStyles() {
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
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #f8fafc, #e0f2fe);
      }
      
      .dialog-header h3 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.3rem;
      }
      
      .dialog-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s;
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
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        text-align: center;
      }
      
      .export-option:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        transform: translateY(-3px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      
      .export-option i {
        font-size: 2.5rem;
        color: #3b82f6;
      }
      
      .export-option span {
        font-weight: 600;
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

  destroy() {
    this.close();
  }
}

// ========== CLASS DETAIL HANDLER ==========
class ClassDetailHandler {
  constructor(notificationManager) {
    this.notificationManager = notificationManager;
    this.boundHandler = (e) => this.handleClick(e);
    this.initListeners();
  }

  initListeners() {
    // ✅ SINGLE delegated listener
    document.addEventListener("click", this.boundHandler);
  }

  handleClick(e) {
    const classItem = e.target.closest(".class-item");
    if (classItem) {
      this.showClassDetail(classItem);
    }

    const joinBtn = e.target.closest(".btn-join");
    if (joinBtn) {
      this.handleJoin(joinBtn);
    }
  }

  showClassDetail(classItem) {
    const className = classItem.querySelector(".class-name")?.textContent || "";
    const time = classItem.querySelector(".class-time")?.textContent || "";
    const room = classItem.querySelector(".class-room")?.textContent || "";
    const lecturer = classItem.querySelector(".class-lecturer")?.textContent || "";
    
    const detail = `
📚 ${className}

⏰ ${time}
📍 ${room}
👨‍🏫 ${lecturer}

Klik OK untuk join kelas atau Cancel untuk kembali.
    `;
    
    if (confirm(detail)) {
      this.notificationManager.show(`Joining kelas: ${className}`, "success");
    }
  }

  handleJoin(joinBtn) {
    const card = joinBtn.closest(".upcoming-card");
    const className = card?.querySelector("h4")?.textContent || "";
    
    if (joinBtn.classList.contains("active")) {
      this.notificationManager.show(`Joining kelas: ${className}`, "success");
      setTimeout(() => {
        this.notificationManager.show("Anda telah terhubung ke kelas! 🎓", "success");
      }, 1500);
    } else if (joinBtn.textContent.includes("Reminder")) {
      this.notificationManager.show(`Reminder diset untuk: ${className}`, "success");
    } else {
      const time = card?.querySelector(".card-info span:first-child")?.textContent || "";
      const room = card?.querySelector(".card-info span:last-child")?.textContent || "";
      const lecturer = card?.querySelector(".card-lecturer")?.textContent || "";
      this.showClassDetailFromCard(className, time, room, lecturer);
    }
  }

  showClassDetailFromCard(className, time, room, lecturer) {
    const detail = `
📚 ${className}

⏰ ${time}
📍 ${room}
👨‍🏫 ${lecturer}

Klik OK untuk join kelas atau Cancel untuk kembali.
    `;
    
    if (confirm(detail)) {
      NotificationManager.getInstance().show(`Joining kelas: ${className}`, "success");
    }
  }

  destroy() {
    document.removeEventListener("click", this.boundHandler);
  }
}

// ========== JADWAL FILTER ==========
class JadwalFilter {
  constructor(searchManager) {
    this.searchManager = searchManager;
  }

  filter(searchTerm) {
    const classItems = document.querySelectorAll(".class-item");
    const upcomingCards = document.querySelectorAll(".upcoming-card");
    
    classItems.forEach(item => {
      const className = item.querySelector(".class-name")?.textContent.toLowerCase() || "";
      const lecturer = item.querySelector(".class-lecturer")?.textContent.toLowerCase() || "";
      const room = item.querySelector(".class-room")?.textContent.toLowerCase() || "";
      
      if (searchTerm === "" || className.includes(searchTerm) || 
          lecturer.includes(searchTerm) || room.includes(searchTerm)) {
        item.style.display = "flex";
        item.style.animation = "fadeIn 0.3s ease";
      } else {
        item.style.display = "none";
      }
    });

    upcomingCards.forEach(card => {
      const title = card.querySelector("h4")?.textContent.toLowerCase() || "";
      const lecturer = card.querySelector(".card-lecturer")?.textContent.toLowerCase() || "";
      
      if (searchTerm === "" || title.includes(searchTerm) || lecturer.includes(searchTerm)) {
        card.style.display = "block";
        card.style.animation = "fadeIn 0.3s ease";
      } else {
        card.style.display = "none";
      }
    });
  }

  destroy() {
    // No cleanup needed
  }
}

// ========== JADWAL ACTION HANDLER ==========
class JadwalActionHandler {
  constructor(notificationManager, exportDialog) {
    this.notificationManager = notificationManager;
    this.exportDialog = exportDialog;
    
    this.exportHandler = () => this.exportDialog.show();
    this.addScheduleHandler = () => {
      this.notificationManager.show("Fitur tambah jadwal akan segera hadir! 🎉", "info");
    };
    
    this.initListeners();
  }

  initListeners() {
    const exportBtn = document.getElementById('exportBtn');
    const addScheduleBtn = document.getElementById('addScheduleBtn');
    
    if (exportBtn) {
      exportBtn.removeEventListener("click", this.exportHandler);
      exportBtn.addEventListener("click", this.exportHandler);
    }
    
    if (addScheduleBtn) {
      addScheduleBtn.removeEventListener("click", this.addScheduleHandler);
      addScheduleBtn.addEventListener("click", this.addScheduleHandler);
    }
  }

  destroy() {
    const exportBtn = document.getElementById('exportBtn');
    const addScheduleBtn = document.getElementById('addScheduleBtn');
    
    exportBtn?.removeEventListener("click", this.exportHandler);
    addScheduleBtn?.removeEventListener("click", this.addScheduleHandler);
  }
}

// ========== JADWAL APP CLASS ==========
class JadwalApp {
  constructor() {
    this.notificationManager = NotificationManager.getInstance();
    this.sidebar = SidebarManager.getInstance();
    this.chatbot = ChatbotManager.getInstance();
    this.exportDialog = new ExportDialogManager(this.notificationManager);
    
    this.jadwalFilter = new JadwalFilter();
    const filterCallback = () => {
      const searchTerm = this.searchManager.getSearchTerm();
      this.jadwalFilter.filter(searchTerm);
    };
    
    this.searchManager = new SearchManager('searchInput', 'searchClear', filterCallback);
    this.weekNavigator = new WeekNavigator();
    this.viewSwitcher = new ViewSwitcher(this.notificationManager);
    this.classDetailHandler = new ClassDetailHandler(this.notificationManager);
    this.actionHandler = new JadwalActionHandler(this.notificationManager, this.exportDialog);
    
    this.init();
  }

  init() {
    this.weekNavigator.init();
    console.log("✅ Jadwal Page Loaded Successfully");
  }

  // ✅ PENTING: Tambahkan destroy method
  destroy() {
    console.log("🧹 Cleaning up Jadwal Page...");
    
    this.weekNavigator?.destroy();
    this.viewSwitcher?.destroy();
    this.classDetailHandler?.destroy();
    this.actionHandler?.destroy();
    this.exportDialog?.destroy();
    
    this.weekNavigator = null;
    this.viewSwitcher = null;
    this.classDetailHandler = null;
    this.actionHandler = null;
    this.exportDialog = null;
  }
}

// ========== INITIALIZE JADWAL APP ==========
let jadwalAppInstance = null;

function initJadwalApp() {
  // ✅ Cleanup instance lama
  if (jadwalAppInstance) {
    jadwalAppInstance.destroy();
    jadwalAppInstance = null;
  }
  
  // ✅ Hanya init jika element ada
  if (document.querySelector('.class-item, .upcoming-card')) {
    jadwalAppInstance = new JadwalApp();
    window.jadwalApp = jadwalAppInstance;
    console.log("✅ JadwalApp initialized");
  }
  
  return jadwalAppInstance;
}

// Auto-initialize
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initJadwalApp);
} else {
  initJadwalApp();
}

export default JadwalApp;
export { initJadwalApp };