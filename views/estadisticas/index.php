<?php // Vista: tablero de estadísticas (sexo, dirección, edad)
// Seguridad defensiva
$estadisticas = is_array($estadisticas ?? null) ? $estadisticas : [
    'por_sexo' => [],
    'por_direccion' => [],
    'por_rango_edad' => [],
];

ob_start();
?>

<div class="page-header">
    <h1>Estadísticas</h1>
    <p class="help-text">
        Colaboradores por sexo, dirección y rangos de edad.
    </p>
</div>

<div class="stats-grid">
    <div class="stats-card">
        <h3>Por sexo</h3>
        <div class="chart-wrap">
            <canvas id="sexoChart"></canvas>
        </div>
    </div>

    <div class="stats-card">
        <h3>Por dirección</h3>
        <div class="chart-wrap">
            <canvas id="direccionChart"></canvas>
        </div>
    </div>
</div>

<div class="stats-card full">
    <h3>Por rango de edad</h3>
    <div class="chart-wrap">
        <canvas id="edadChart"></canvas>
    </div>
    <p class="help-text">Ejemplo: 25–30 años.</p>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const palette = ['#4F46E5', '#22C55E', '#F59E0B', '#EF4444', '#3B82F6', '#8B5CF6'];

    const barOptions = () => ({
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: 8 },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (ctx) => `${ctx.label}: ${ctx.parsed} colaboradores`
                }
            }
        },
        scales: {
            x: {
                ticks: { color: '#444', font: { size: 12 } },
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#444', stepSize: 1 },
                grid: { color: '#eee' }
            }
        }
    });

    const pieOptions = () => ({
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: 8 },
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: { usePointStyle: true, padding: 12 }
            },
            tooltip: {
                callbacks: {
                    label: (ctx) => {
                        const total = (ctx.dataset.data || []).reduce((a, b) => a + b, 0);
                        const val = ctx.parsed;
                        const pct = total ? ((val / total) * 100).toFixed(1) : 0;
                        return `${ctx.label}: ${val} (${pct}%)`;
                    }
                }
            }
        }
    });

    // ===================== SEXO =====================
    const sexoEl = document.getElementById('sexoChart');
    if (sexoEl) {
        new Chart(sexoEl, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($estadisticas['por_sexo'], 'sexo')) ?>,
                datasets: [{
                    label: 'Colaboradores',
                    data: <?= json_encode(array_column($estadisticas['por_sexo'], 'total')) ?>,
                    backgroundColor: palette[0] ?? '#4F46E5',
                    borderRadius: 4
                }]
            },
            options: barOptions()
        });
    }

    // ===================== DIRECCIÓN =====================
    const dirEl = document.getElementById('direccionChart');
    if (dirEl) {
        const dirLabels = <?= json_encode(array_column($estadisticas['por_direccion'], 'direccion')) ?>;
        new Chart(dirEl, {
            type: 'pie',
            data: {
                labels: dirLabels,
                datasets: [{
                    data: <?= json_encode(array_column($estadisticas['por_direccion'], 'total')) ?>,
                    backgroundColor: dirLabels.map((_, i) => palette[i % palette.length])
                }]
            },
            options: pieOptions()
        });
    }

    // ===================== EDAD =====================
    const edadEl = document.getElementById('edadChart');
    if (edadEl) {
        new Chart(edadEl, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($estadisticas['por_rango_edad'], 'rango')) ?>,
                datasets: [{
                    label: 'Colaboradores',
                    data: <?= json_encode(array_column($estadisticas['por_rango_edad'], 'total')) ?>,
                    backgroundColor: palette[2] ?? '#F59E0B',
                    borderRadius: 4
                }]
            },
            options: barOptions()
        });
    }

});
</script>
