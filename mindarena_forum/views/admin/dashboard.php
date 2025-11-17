<h1 class="mb-4">Tableau de Bord</h1>

<!-- STAT CARDS -->
<div class="row g-4">

    <div class="col-md-4">
        <div class="card-dark text-center stat-card" data-target="<?= $totalForums ?>">
            <i class="ri-folder-3-line" style="font-size:42px; color:#d6a5ff;"></i>
            <h4 class="mt-2">Forums</h4>
            <h1 class="stat-value">0</h1>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-dark text-center stat-card" data-target="<?= $totalPubs ?>">
            <i class="ri-file-list-3-line" style="font-size:42px; color:#f4b2ff;"></i>
            <h4 class="mt-2">Publications</h4>
            <h1 class="stat-value">0</h1>
        </div>
    </div>

</div>

<!-- CHARTS SECTION -->
<h2 class="mt-5 mb-3">Statistiques Graphiques</h2>

<div class="row g-4">
    <!-- LINE CHART -->
    <div class="col-md-6">
        <div class="card-dark p-4">
            <h5>Activité Hebdomadaire</h5>
            <canvas id="lineChart" height="140"></canvas>
        </div>
    </div>

    <!-- BAR CHART -->
    <div class="col-md-6">
        <div class="card-dark p-4">
            <h5>Publications / Forums</h5>
            <canvas id="barChart" height="140"></canvas>
        </div>
    </div>
</div>

<!-- DONUT CHART -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card-dark p-4">
            <h5>Répartition Totale</h5>
            <canvas id="donutChart"></canvas>
        </div>
    </div>

    <!-- ACTIVITY TIMELINE -->
    <div class="col-md-6">
        <div class="card-dark p-4">
            <h5>Activité Récente</h5>

            <ul class="timeline mt-3">
                <li><span>12:30</span> Nouvelle publication ajoutée</li>
                <li><span>11:10</span> Forum “Gaming News” modifié</li>
                <li><span>09:55</span> Nouvelle publication dans “Tech Updates”</li>
                <li><span>Yesterday</span> 2 nouveaux forums créés</li>
            </ul>
        </div>
    </div>
</div>


<script>
/* ------------------------------
   Animated Stat Counters
------------------------------ */
document.querySelectorAll(".stat-card").forEach(card => {
    let target = +card.dataset.target;
    let el = card.querySelector(".stat-value");

    let count = 0;
    let speed = 20;

    let update = () => {
        count += Math.ceil(target / 50);
        if (count > target) count = target;

        el.textContent = count;

        if (count < target) requestAnimationFrame(update);
    };

    update();
});

/* ------------------------------
   Neon Gradient Line Chart
------------------------------ */
const lineCtx = document.getElementById('lineChart').getContext('2d');

const gradientLine = lineCtx.createLinearGradient(0,0,0,300);
gradientLine.addColorStop(0, 'rgba(199,125,255,0.8)');
gradientLine.addColorStop(1, 'rgba(255,235,255,0.1)');

new Chart(lineCtx, {
    type: 'line',
    data: {
        labels: ["Lun","Mar","Mer","Jeu","Ven","Sam","Dim"],
        datasets: [{
            label: "Publications",
            data: [5,8,6,15,10,4,9],
            fill: true,
            backgroundColor: gradientLine,
            borderColor: "#e6b3ff",
            tension: 0.4
        }]
    },
    options: { plugins:{legend:{labels:{color:"white"}}}, scales:{x:{ticks:{color:"#ddd"}}, y:{ticks:{color:"#ddd"}}}}
});

/* ------------------------------
   Neon Bar Chart
------------------------------ */
const barCtx = document.getElementById('barChart').getContext('2d');

new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: ["Forums", "Publications"],
        datasets: [{
            data: [<?= $totalForums ?>, <?= $totalPubs ?>],
            backgroundColor: ["#d6a5ff", "#fbbdff"],
            borderRadius: 8
        }]
    },
    options: { plugins:{legend:{display:false}}, scales:{x:{ticks:{color:"#ddd"}}, y:{ticks:{color:"#ddd"}}}}
});

/* ------------------------------
   Neon Donut Chart
------------------------------ */
const donutCtx = document.getElementById('donutChart').getContext('2d');

new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: ["Forums", "Publications"],
        datasets: [{
            data: [<?= $totalForums ?>, <?= $totalPubs ?>],
            backgroundColor: ["#d6a5ff", "#fbbdff"],
            hoverBackgroundColor: ["#e6c2ff", "#ffd6ff"]
        }]
    },
    options: { plugins:{legend:{labels:{color:"white"}}}}
});
</script>

<style>
.timeline {
    list-style: none;
    padding: 0;
}

.timeline li {
    position: relative;
    padding-left: 25px;
    margin-bottom: 12px;
    opacity: .85;
}

.timeline li span {
    font-weight: bold;
    color: #f2b5ff;
}
.timeline li:before {
    content: "";
    position: absolute;
    left: 5px;
    top: 6px;
    width: 8px;
    height: 8px;
    background: #f2b5ff;
    border-radius: 50%;
    box-shadow: 0 0 8px #f2b5ff;
}
.timeline li:hover {
    opacity: 1;
    transform: translateX(4px);
    transition: .25s;
}
</style>
