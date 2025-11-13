(function() {
    'use strict';

    let currentWeekOffset = 0;

    const DOM = {
        get prevWeekBtn() { return document.getElementById("prevWeek"); },
        get nextWeekBtn() { return document.getElementById("nextWeek"); },
        get currentWeekText() { return document.getElementById("currentWeek"); },
        get exportBtn() { return document.getElementById("exportBtn"); },
        get addScheduleBtn() { return document.getElementById("addScheduleBtn"); }
    };

    // Initialize search with filter callback
    function filterSchedule(searchTerm) {
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

    // Week Navigation
    function initWeekNavigation() {
        const { prevWeekBtn, nextWeekBtn } = DOM;

        if (prevWeekBtn) {
            prevWeekBtn.addEventListener("click", () => {
                currentWeekOffset--;
                updateWeekDisplay();
                if (window.AppCore) {
                    window.AppCore.showNotification("Menampilkan minggu sebelumnya", "info");
                }
            });
        }

        if (nextWeekBtn) {
            nextWeekBtn.addEventListener("click", () => {
                currentWeekOffset++;
                updateWeekDisplay();
                if (window.AppCore) {
                    window.AppCore.showNotification("Menampilkan minggu berikutnya", "info");
                }
            });
        }
    }

    // Update Week Display
    function updateWeekDisplay() {
        const { currentWeekText } = DOM;
        if (!currentWeekText) return;

        const today = new Date();
        today.setDate(today.getDate() + (currentWeekOffset * 7));
        
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay() + 1);
        
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        
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
        
        currentWeekText.textContent = weekText;

        // Update day dates
        const dayHeaders = document.querySelectorAll(".day-column .day-header");
        dayHeaders.forEach((header, index) => {
            const currentDay = new Date(startOfWeek);
            currentDay.setDate(startOfWeek.getDate() + index);
            
            const dayDate = header.querySelector(".day-date");
            if (dayDate) {
                dayDate.textContent = `${currentDay.getDate()} ${months[currentDay.getMonth()].substring(0, 3)}`;
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

    // View Switcher
    function initViewSwitcher() {
        const viewBtns = document.querySelectorAll(".view-btn");
        viewBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                const view = btn.getAttribute("data-view");
                
                viewBtns.forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                
                if (view === "day") {
                    if (window.AppCore) {
                        window.AppCore.showNotification("Tampilan harian sedang dalam pengembangan", "info");
                    }
                } else if (view === "month") {
                    if (window.AppCore) {
                        window.AppCore.showNotification("Tampilan bulanan sedang dalam pengembangan", "info");
                    }
                }
            });
        });
    }

    // Export & Add buttons
    function initButtons() {
        const { exportBtn, addScheduleBtn } = DOM;

        if (exportBtn) {
            exportBtn.addEventListener("click", () => {
                if (window.AppCore) {
                    window.AppCore.showNotification("Fitur export sedang dalam pengembangan", "info");
                }
            });
        }

        if (addScheduleBtn) {
            addScheduleBtn.addEventListener("click", () => {
                if (window.AppCore) {
                    window.AppCore.showNotification("Fitur tambah jadwal akan segera hadir! 🎉", "info");
                }
            });
        }
    }

    // Initialize Jadwal
    function initJadwal() {
        if (window.AppCore) {
            window.AppCore.DOM.searchInput?.addEventListener('input', () => {
                const searchTerm = window.AppCore.DOM.searchInput.value.toLowerCase();
                filterSchedule(searchTerm);
            });
        }

        initWeekNavigation();
        initViewSwitcher();
        initButtons();
        updateWeekDisplay();

        console.log("✅ Jadwal.js Loaded");
    }

    // Auto-init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initJadwal);
    } else {
        initJadwal();
    }
})();
