/* =======================================================
   SIDEBAR COLLAPSE
   ======================================================= */
const sidebar = document.getElementById("sidebar");
const sidebarBtn = document.getElementById("toggleSidebar");

if (sidebarBtn) {
    sidebarBtn.onclick = () => {
        sidebar.classList.toggle("collapsed");
    };
}

/* =======================================================
   DARK / LIGHT MODE TOGGLE (FULLY WORKING VERSION)
   ======================================================= */
const body = document.body;
const themeToggle = document.getElementById("themeToggle");

// Load saved preference
let savedTheme = localStorage.getItem("theme");
if (savedTheme === "light") {
    body.classList.add("light-mode");
}

// **Working Event Listener**
if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        body.classList.toggle("light-mode");

        if (body.classList.contains("light-mode")) {
            localStorage.setItem("theme", "light");
        } else {
            localStorage.setItem("theme", "dark");
        }
    });
}

/* =======================================================
   PAGE TRANSITION
   ======================================================= */
document.querySelector(".page-transition")?.classList.add("loaded");

/* =======================================================
   CHART.JS
   ======================================================= */
function getThemeColor() {
    return body.classList.contains("light-mode") ? "#000" : "#fff";
}

if (window.Chart) {
    // Line Chart
    const ctx1 = document.getElementById("lineChart");
    if (ctx1) {
        new Chart(ctx1, {
            type: "line",
            data: {
                labels: ["Lun","Mar","Mer","Jeu","Ven","Sam","Dim"],
                datasets: [{
                    label: "Activité",
                    data: [12, 18, 7, 15, 22, 30, 18],
                    borderColor: "#ff53f4",
                    backgroundColor: "rgba(255,83,244,0.10)",
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: { legend: { labels: { color: getThemeColor() } } },
                scales: {
                    x: { ticks: { color: getThemeColor() } },
                    y: { ticks: { color: getThemeColor() } }
                }
            }
        });
    }

    // Bar Chart
    const ctx2 = document.getElementById("barChart");
    if (ctx2) {
        new Chart(ctx2, {
            type: "bar",
            data: {
                labels: ["Forums", "Publications", "Users"],
                datasets: [{
                    label: "Statistiques",
                    data: window.dashboardStats || [0, 0, 0],
                    backgroundColor: ["#a855f7","#f472b6","#38bdf8"]
                }]
            },
            options: {
                plugins: { legend: { labels: { color: getThemeColor() } } },
                scales: {
                    x: { ticks: { color: getThemeColor() } },
                    y: { ticks: { color: getThemeColor() } }
                }
            }
        });
    }
}
