import MySchuderApp from "./script";

// ========== JADWAL CONFIG ==========
class JadwalConfig {
  static NOTIFICATION_DURATION = 3000;
  static ANIMATION_DURATION = 300;
  static MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
}

// ========== JADWAL SEARCH MANAGER ==========
class JadwalSearchManager {
  constructor(searchInputId, searchClearId) {
    this.searchInput = document.getElementById(searchInputId);
    this.searchClear = document.getElementById(searchClearId);
    this.initListeners();
  }

  initListeners() {
    if (this.searchInput) {
      this.searchInput.addEventListener("input", (e) => this.handleSearch(e));
    }

    if (this.searchClear) {
      this.searchClear.addEventListener("click", () => this.clearSearch());
    }
  }

  handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    if (this.searchClear) {
      this.searchClear.style.display = searchTerm.length > 0 ? "block" : "none";
    }

    this.filterSchedule(searchTerm);
  }

  clearSearch() {
    if (this.searchInput) {
      this.searchInput.value = "";
      this.searchInput.focus();
    }
    if (this.searchClear) {
      this.searchClear.style.display = "none";
    }
    this.filterSchedule("");
  }

  filterSchedule(searchTerm) {
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
}

// ========== WEEK NAVIGATOR ==========
class WeekNavigator {
  constructor(prevBtnId, nextBtnId, currentWeekTextId) {
    this.prevBtn = document.getElementById(prevBtnId);
    this.nextBtn = document.getElementById(nextBtnId);
    this.currentWeekText = document.getElementById(currentWeekTextId);
    this.currentWeekOffset = 0;
    
    this.initListeners();
  }

  initListeners() {
    if (this.prevBtn) {
      this.prevBtn.addEventListener("click", () => this.navigatePrev());
    }

    if (this.nextBtn) {
      this.nextBtn.addEventListener("click", () => this.navigateNext());
    }
  }

  navigatePrev() {
    this.currentWeekOffset--;
    this.updateWeekDisplay();
    JadwalNotification.show("Menampilkan minggu sebelumnya", "info");
  }

  navigateNext() {
    this.currentWeekOffset++;
    this.updateWeekDisplay();
    JadwalNotification.show("Menampilkan minggu berikutnya", "info");
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
    const startMonth = JadwalConfig.MONTHS[startOfWeek.getMonth()];
    const endMonth = JadwalConfig.MONTHS[endOfWeek.getMonth()];
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
        dayDate.textContent = `${currentDay.getDate()} ${JadwalConfig.MONTHS[currentDay.getMonth()].substring(0, 3)}`;
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
        const badge = header.querySelector(".today-badge");
        if (badge) badge.remove();
      }
    });
  }

  init() {
    this.updateWeekDisplay();
  }
}

// ========== VIEW SWITCHER ==========
class ViewSwitcher {
  constructor(viewBtnsSelector) {
    this.viewBtns = document.querySelectorAll(viewBtnsSelector);
    this.initListeners();
  }

  initListeners() {
    this.viewBtns.forEach(btn => {
      btn.addEventListener("click", () => this.handleViewChange(btn));
    });
  }

  handleViewChange(btn) {
    const view = btn.getAttribute("data-view");
    
    this.viewBtns.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    
    if (view === "day") {
      JadwalNotification.show("Tampilan harian sedang dalam pengembangan", "info");
    } else if (view === "month") {
      JadwalNotification.show("Tampilan bulanan sedang dalam pengembangan", "info");
    }
  }
}

// ========== EXPORT DIALOG MANAGER ==========
class ExportDialogManager {
  constructor() {
    this.dialog = null;
  }

  show() {
    this.dialog = document.createElement("div");
    this.dialog.className = "export-dialog";
    this.dialog.innerHTML = this.getDialogHTML();
    
    document.body.appendChild(this.dialog);
    this.initDialogListeners();
    this.addDialogStyles();
  }

  getDialogHTML() {
    return `
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
  }

  initDialogListeners() {
    const closeBtn = this.dialog.querySelector(".dialog-close");
    const dialogOverlay = this.dialog.querySelector(".dialog-overlay");
    const exportOptions = this.dialog.querySelectorAll(".export-option");
    
    const closeDialog = () => this.close();
    
    closeBtn.addEventListener("click", closeDialog);
    dialogOverlay.addEventListener("click", closeDialog);
    
    exportOptions.forEach(option => {
      option.addEventListener("click", () => this.handleExport(option));
    });
  }

  handleExport(option) {
    const format = option.getAttribute("data-format");
    this.close();
    JadwalNotification.show(`Mengekspor jadwal ke format ${format.toUpperCase()}...`, "success");
    
    setTimeout(() => {
      JadwalNotification.show(`Jadwal berhasil diekspor! ✅`, "success");
    }, 2000);
  }

  close() {
    if (this.dialog) {
      this.dialog.remove();
      this.dialog = null;
    }
  }

  addDialogStyles() {
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
}

// ========== CLASS DETAIL HANDLER ==========
class ClassDetailHandler {
  constructor() {
    this.initListeners();
  }

  initListeners() {
    document.addEventListener("click", (e) => {
      const classItem = e.target.closest(".class-item");
      if (classItem) {
        this.showClassDetail(classItem);
      }

      const joinBtn = e.target.closest(".btn-join");
      if (joinBtn) {
        this.handleJoin(joinBtn);
      }
    });
  }

  showClassDetail(classItem) {
    const className = classItem.querySelector(".class-name").textContent;
    const time = classItem.querySelector(".class-time").textContent;
    const room = classItem.querySelector(".class-room").textContent;
    const lecturer = classItem.querySelector(".class-lecturer").textContent;
    
    const detail = `
📚 ${className}

⏰ ${time}
📍 ${room}
👨‍🏫 ${lecturer}

Klik OK untuk join kelas atau Cancel untuk kembali.
    `;
    
    if (confirm(detail)) {
      JadwalNotification.show(`Joining kelas: ${className}`, "success");
    }
  }

  handleJoin(joinBtn) {
    const card = joinBtn.closest(".upcoming-card");
    const className = card.querySelector("h4").textContent;
    
    if (joinBtn.classList.contains("active")) {
      JadwalNotification.show(`Joining kelas: ${className}`, "success");
      setTimeout(() => {
        JadwalNotification.show("Anda telah terhubung ke kelas! 🎓", "success");
      }, 1500);
    } else if (joinBtn.textContent.includes("Reminder")) {
      JadwalNotification.show(`Reminder diset untuk: ${className}`, "success");
    } else {
      const time = card.querySelector(".card-info span:first-child").textContent;
      const room = card.querySelector(".card-info span:last-child").textContent;
      const lecturer = card.querySelector(".card-lecturer").textContent;
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
      JadwalNotification.show(`Joining kelas: ${className}`, "success");
    }
  }
}

// ========== JADWAL NOTIFICATION ==========
class JadwalNotification {
  static show(message, type = "info") {
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
    }, JadwalConfig.NOTIFICATION_DURATION);
  }
}

// ========== JADWAL ACTION HANDLER ==========
class JadwalActionHandler {
  constructor() {
    this.exportBtn = document.getElementById('exportBtn');
    this.addScheduleBtn = document.getElementById('addScheduleBtn');
    this.exportDialog = new ExportDialogManager();
    
    this.initListeners();
  }

  initListeners() {
    if (this.exportBtn) {
      this.exportBtn.addEventListener("click", () => this.exportDialog.show());
    }

    if (this.addScheduleBtn) {
      this.addScheduleBtn.addEventListener("click", () => {
        JadwalNotification.show("Fitur tambah jadwal akan segera hadir! 🎉", "info");
      });
    }
  }
}

// ========== JADWAL ANIMATION STYLES ==========
class JadwalAnimationStyles {
  static init() {
    if (document.querySelector("#jadwalAnimationStyles")) return;
    
    const style = document.createElement("style");
    style.id = "jadwalAnimationStyles";
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
}

// ========== JADWAL APP CLASS ==========
class JadwalApp {
  constructor() {
    
    this.search = new JadwalSearchManager('searchInput', 'searchClear');
    this.weekNavigator = new WeekNavigator('prevWeek', 'nextWeek', 'currentWeek');
    this.viewSwitcher = new ViewSwitcher('.view-btn');
    this.classDetailHandler = new ClassDetailHandler();
    this.actionHandler = new JadwalActionHandler();
    
    this.init();
  }

  init() {
    JadwalAnimationStyles.init();
    this.weekNavigator.init();
    this.initResponsive();
    console.log("✅ Jadwal Page Loaded Successfully");
  }

  initResponsive() {
    window.addEventListener("resize", () => {
      if (window.innerWidth > 768) {
        this.sidebar.close();
      }
    });
  }
}

// ========== INITIALIZE JADWAL APP ==========
window.addEventListener("load", () => {
  window.jadwalApp = new JadwalApp();
});