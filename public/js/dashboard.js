(function() {
    'use strict';

    // Filter button functionality
    function initFilterButtons() {
        const filterBtns = document.querySelectorAll(".filter-btn");
        if (filterBtns.length) {
            filterBtns.forEach((btn) => {
                btn.addEventListener("click", () => {
                    filterBtns.forEach((b) => b.classList.remove("active"));
                    btn.classList.add("active");
                });
            });
        }
    }

    // Schedule join buttons
    function initScheduleButtons() {
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
                        if (window.AppCore) {
                            window.AppCore.showNotification(`✅ Anda berhasil join kelas "${className}". Selamat belajar! 📚`, "success");
                        }
                    }, 500);
                }, 1500);
            });
        });
    }

    // Initialize dashboard
    function initDashboard() {
        initFilterButtons();
        initScheduleButtons();
        console.log("✅ Dashboard.js Loaded");
    }

    // Auto-init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboard);
    } else {
        initDashboard();
    }
})();