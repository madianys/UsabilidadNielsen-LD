<?php
session_start();

// ─── Validar datos recibidos ──────────────────────────────────────────────────
if (empty($_POST['respuestas']) || empty($_POST['ponderacion'])) {
  header('Location: index.php');
  exit;
}

$evaluador   = htmlspecialchars(trim($_POST['evaluador'] ?? ''));
$aplicacion  = htmlspecialchars(trim($_POST['aplicacion'] ?? ''));
$fecha       = htmlspecialchars(trim($_POST['fecha'] ?? ''));
$respuestas  = $_POST['respuestas'];
$ponderacion = $_POST['ponderacion'];

// ─── Definición de heurísticas ────────────────────────────────────────────────
$heuristicas = [
  1 => 'Visibilidad del estado del sistema',
  2 => 'Consistencia y estándares',
  3 => 'Prevención de errores',
  4 => 'Diseño minimalista y estético',
  5 => 'Control y libertad del usuario',
];

// ─── Validación PHP de ponderaciones ─────────────────────────────────────────
$totalPond = 0;
foreach ($ponderacion as $p) {
  $totalPond += (int)$p;
}
if ($totalPond !== 100) {
  header('Location: cuestionario.php?error_pond=1');
  exit;
}

// ─── Calcular promedios y puntajes ponderados ─────────────────────────────────
$resultados = [];
$promedioGeneral = 0;

foreach ($heuristicas as $id => $nombre) {
  $preguntas    = $respuestas[$id] ?? [];
  $suma         = array_sum(array_map('intval', $preguntas));
  $cantidad     = count($preguntas);
  $promedio     = $cantidad > 0 ? round($suma / $cantidad, 2) : 0;
  $pond         = (int)($ponderacion[$id] ?? 0);
  $ponderado    = round($promedio * ($pond / 100), 4);

  $resultados[$id] = [
    'nombre'    => $nombre,
    'promedio'  => $promedio,
    'ponderado' => $ponderado,
    'pond'      => $pond,
    'maximo'    => round(5 * ($pond / 100), 4),
  ];

  $promedioGeneral += $ponderado;
}

$promedioGeneral = round($promedioGeneral, 2);

// ─── Clasificación del resultado ──────────────────────────────────────────────
function clasificar(float $score): array {
  if ($score >= 4.5) return ['nivel' => 'Excelente',   'clase' => 'nivel-excelente',  'icono' => '★'];
  if ($score >= 3.5) return ['nivel' => 'Bueno',       'clase' => 'nivel-bueno',       'icono' => '●'];
  if ($score >= 2.5) return ['nivel' => 'Aceptable',   'clase' => 'nivel-aceptable',   'icono' => '◆'];
  if ($score >= 1.5) return ['nivel' => 'Deficiente',  'clase' => 'nivel-deficiente',  'icono' => '▼'];
  return               ['nivel' => 'Muy deficiente', 'clase' => 'nivel-muy-deficiente','icono' => '✕'];
}

$clasificacionGeneral = clasificar($promedioGeneral);

// Clasificar cada heurística individualmente
foreach ($resultados as $id => &$r) {
  $r['clasificacion'] = clasificar($r['promedio']);
}
unset($r);

// ─── Preparar datos para Chart.js ────────────────────────────────────────────
$chartLabels    = array_column($resultados, 'nombre');
$chartPromedios = array_column($resultados, 'promedio');
$chartPond      = array_column($resultados, 'pond');

// Labels cortos para el radar
$labelsCortos = [
  'Visibilidad del sistema',
  'Consistencia',
  'Prevención de errores',
  'Diseño minimalista',
  'Control del usuario',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultados — Evaluador de Usabilidad</title>
  <link rel="stylesheet" href="estilos.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
  <div class="container container--wide">

    <!-- Encabezado -->
    <header class="site-header">
      <div class="logo-mark">EU</div>
      <h1>Evaluador de Usabilidad</h1>
      <p class="subtitle">Informe de resultados</p>
    </header>

    <!-- Meta de la evaluación -->
    <div class="card card--flat eval-meta">
      <div class="meta-grid">
        <div><span class="meta-label">Evaluador</span><strong><?php echo $evaluador; ?></strong></div>
        <div><span class="meta-label">Aplicación</span><strong><?php echo $aplicacion; ?></strong></div>
        <div><span class="meta-label">Fecha</span><strong><?php echo date('d/m/Y', strtotime($fecha)); ?></strong></div>
      </div>
    </div>

    <!-- Resultado general -->
    <div class="card resultado-general <?php echo $clasificacionGeneral['clase']; ?>">
      <div class="resultado-icono"><?php echo $clasificacionGeneral['icono']; ?></div>
      <div>
        <p class="resultado-etiqueta">Puntaje general</p>
        <p class="resultado-score"><?php echo number_format($promedioGeneral, 2); ?> / 5.00</p>
        <p class="resultado-nivel"><?php echo $clasificacionGeneral['nivel']; ?></p>
      </div>
    </div>

    <!-- Tabla de resultados por heurística -->
    <section class="card">
      <h2 class="card-title">Resultados por heurística</h2>
      <div class="tabla-wrap">
        <table class="tabla-resultados">
          <thead>
            <tr>
              <th>Heurística</th>
              <th>Ponderación</th>
              <th>Promedio (1–5)</th>
              <th>Puntaje ponderado</th>
              <th>Máximo posible</th>
              <th>Nivel</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($resultados as $id => $r): ?>
            <tr>
              <td><?php echo $r['nombre']; ?></td>
              <td class="text-center"><?php echo $r['pond']; ?>%</td>
              <td class="text-center">
                <div class="barra-mini-wrap">
                  <div class="barra-mini" style="width:<?php echo ($r['promedio'] / 5 * 100); ?>%"></div>
                </div>
                <?php echo number_format($r['promedio'], 2); ?>
              </td>
              <td class="text-center"><?php echo number_format($r['ponderado'], 2); ?></td>
              <td class="text-center"><?php echo number_format($r['maximo'], 2); ?></td>
              <td class="text-center">
                <span class="badge <?php echo $r['clasificacion']['clase']; ?>">
                  <?php echo $r['clasificacion']['nivel']; ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th>Total</th>
              <th class="text-center">100%</th>
              <th class="text-center">—</th>
              <th class="text-center"><strong><?php echo number_format($promedioGeneral, 2); ?></strong></th>
              <th class="text-center">5.00</th>
              <th class="text-center">
                <span class="badge <?php echo $clasificacionGeneral['clase']; ?>">
                  <?php echo $clasificacionGeneral['nivel']; ?>
                </span>
              </th>
            </tr>
          </tfoot>
        </table>
      </div>
    </section>

    <!-- Gráfica radar -->
    <section class="card">
      <h2 class="card-title">Visualización por heurística</h2>
      <div class="chart-wrap">
        <canvas id="radarChart" width="420" height="420"></canvas>
      </div>
    </section>

    <!-- Escala de clasificación -->
    <section class="card">
      <h2 class="card-title">Escala de clasificación</h2>
      <div class="escala-grid">
        <div class="escala-item nivel-excelente">
          <strong>Excelente</strong><span>4.5 – 5.0</span>
          <p>La aplicación cumple de forma sobresaliente con la heurística.</p>
        </div>
        <div class="escala-item nivel-bueno">
          <strong>Bueno</strong><span>3.5 – 4.4</span>
          <p>Buen desempeño con aspectos menores a mejorar.</p>
        </div>
        <div class="escala-item nivel-aceptable">
          <strong>Aceptable</strong><span>2.5 – 3.4</span>
          <p>Cumplimiento básico; requiere mejoras importantes.</p>
        </div>
        <div class="escala-item nivel-deficiente">
          <strong>Deficiente</strong><span>1.5 – 2.4</span>
          <p>Problemas significativos de usabilidad presentes.</p>
        </div>
        <div class="escala-item nivel-muy-deficiente">
          <strong>Muy deficiente</strong><span>1.0 – 1.4</span>
          <p>La aplicación no cumple con la heurística evaluada.</p>
        </div>
      </div>
    </section>

    <!-- Acciones -->
    <div class="form-actions">
      <a href="index.php" class="btn btn-secondary">← Nueva evaluación</a>
      <button onclick="window.print()" class="btn btn-primary">Imprimir informe</button>
    </div>

    <footer class="site-footer">
      <p>Heurísticas de Nielsen · Evaluación de Usabilidad Web</p>
    </footer>
  </div>

  <script>
    const labels   = <?php echo json_encode($labelsCortos); ?>;
    const datos    = <?php echo json_encode(array_values($chartPromedios)); ?>;
    const ponds    = <?php echo json_encode(array_values($chartPond)); ?>;

    const ctx = document.getElementById('radarChart').getContext('2d');

    new Chart(ctx, {
      type: 'radar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Promedio por heurística',
          data: datos,
          backgroundColor: 'rgba(99, 102, 241, 0.12)',
          borderColor: 'rgba(99, 102, 241, 0.9)',
          borderWidth: 2,
          pointBackgroundColor: 'rgba(99, 102, 241, 1)',
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
          r: {
            min: 0,
            max: 5,
            ticks: {
              stepSize: 1,
              font: { size: 11 },
              color: '#6b7280',
            },
            pointLabels: {
              font: { size: 12 },
              color: '#374151',
            },
            grid: { color: '#e5e7eb' },
            angleLines: { color: '#e5e7eb' },
          }
        },
        plugins: {
          legend: {
            labels: { font: { size: 13 }, color: '#374151' }
          },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                const pond = ponds[ctx.dataIndex];
                return ` Promedio: ${ctx.raw.toFixed(2)} / 5  (Ponderación: ${pond}%)`;
              }
            }
          }
        }
      }
    });
  </script>
</body>
</html>
