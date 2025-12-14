<?php
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
    <p class="help-text">Colaboradores por sexo, dirección y rangos de edad.</p>
</div>

<div class="grid two-cols">
    <div class="card">
        <h3>Por sexo</h3>
        <canvas id="sexoChart" height="180"></canvas>
    </div>

    <div class="card">
        <h3>Por dirección</h3>
        <canvas id="direccionChart" height="180"></canvas>
    </div>
</div>

<div class="card">
    <h3>Por rango de edad</h3>
    <canvas id="edadChart" height="180"></canvas>
    <p class="help-text">Ejemplo: 25–30 años.</p>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ===================== SEXO =====================
(function () {
    const el = document.getElementById('sexoChart');
    if (!el) return;

    new Chart(el, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($estadisticas['por_sexo'], 'sexo')) ?>,
            datasets: [{
                label: 'Colaboradores',
                data: <?= json_encode(array_column($estadisticas['por_sexo'], 'total')) ?>,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
})();

// ===================== DIRECCIÓN =====================
(function () {
    const el = document.getElementById('direccionChart');
    if (!el) return;

    new Chart(el, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($estadisticas['por_direccion'], 'direccion')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($estadisticas['por_direccion'], 'total')) ?>,
                backgroundColor: [
                    '#1cc88a', '#36b9cc', '#f6c23e',
                    '#e74a3b', '#858796', '#20c997'
                ]
            }]
        },
        options: { responsive: true }
    });
})();

// ===================== EDAD =====================
(function () {
    const el = document.getElementById('edadChart');
    if (!el) return;

    new Chart(el, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($estadisticas['por_rango_edad'], 'rango')) ?>,
            datasets: [{
                label: 'Colaboradores',
                data: <?= json_encode(array_column($estadisticas['por_rango_edad'], 'total')) ?>,
                backgroundColor: '#36b9cc'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
})();
</script>

<?php
$content = ob_get_clean();
