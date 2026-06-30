<?php
session_start();

// Validar que vengan datos del formulario inicial
if (empty($_POST['evaluador']) || empty($_POST['aplicacion'])) {
  header('Location: index.php?error=1');
  exit;
}

$evaluador = htmlspecialchars(trim($_POST['evaluador']));
$aplicacion = htmlspecialchars(trim($_POST['aplicacion']));
$fecha      = htmlspecialchars(trim($_POST['fecha']));

// Guardar en sesión para usarlos en resultados
$_SESSION['evaluador'] = $evaluador;
$_SESSION['aplicacion'] = $aplicacion;
$_SESSION['fecha']      = $fecha;

// Definición de heurísticas y preguntas
$heuristicas = [
  1 => [
    'nombre' => 'Visibilidad del estado del sistema',
    'codigo' => 'H1',
    'preguntas' => [
      '¿El sistema informa al usuario cuando está procesando o guardando información?',
      '¿Se muestran claramente los resultados de las acciones del usuario?',
      '¿El sistema indica de forma visible cuándo ocurre un error?',
    ]
  ],
  2 => [
    'nombre' => 'Consistencia y estándares',
    'codigo' => 'H2',
    'preguntas' => [
      '¿Los elementos de interfaz (botones, iconos, colores) son consistentes en todas las páginas?',
      '¿El sistema usa terminología familiar y coherente para el usuario?',
      '¿Los controles similares se comportan de la misma manera en toda la aplicación?',
    ]
  ],
  3 => [
    'nombre' => 'Prevención de errores',
    'codigo' => 'H3',
    'preguntas' => [
      '¿El sistema solicita confirmación antes de ejecutar acciones irreversibles?',
      '¿Los formularios validan datos antes de ser enviados y muestran mensajes claros?',
      '¿El sistema evita que el usuario llegue a estados de error mediante guía o restricciones?',
    ]
  ],
  4 => [
    'nombre' => 'Diseño minimalista y estético',
    'codigo' => 'H4',
    'preguntas' => [
      '¿La interfaz presenta solo la información necesaria, sin elementos irrelevantes?',
      '¿El diseño visual facilita la lectura y no genera distracción?',
      '¿Se evita el uso excesivo de colores, fuentes o decoraciones que dificulten la navegación?',
    ]
  ],
  5 => [
    'nombre' => 'Control y libertad del usuario',
    'codigo' => 'H5',
    'preguntas' => [
      '¿El usuario puede deshacer o revertir acciones fácilmente?',
      '¿Existe una forma clara de cancelar procesos o salir de secciones no deseadas?',
      '¿El usuario puede navegar libremente sin sentirse atrapado en flujos obligatorios?',
    ]
  ],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cuestionario — Evaluador de Usabilidad</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <div class="container container--wide">
    <header class="site-header">
      <div class="logo-mark">EU</div>
      <h1>Evaluador de Usabilidad</h1>
      <p class="subtitle">Cuestionario de Heurísticas de Nielsen</p>
    </header>

    <div class="eval-meta card card--flat">
      <div class="meta-grid">
        <div><span class="meta-label">Evaluador</span><strong><?php echo $evaluador; ?></strong></div>
        <div><span class="meta-label">Aplicación</span><strong><?php echo $aplicacion; ?></strong></div>
        <div><span class="meta-label">Fecha</span><strong><?php echo date('d/m/Y', strtotime($fecha)); ?></strong></div>
      </div>
    </div>

    <div class="alert alert-info" id="ponderacion-aviso">
      <strong>Ponderación:</strong> Asigna un porcentaje a cada heurística. La suma debe ser exactamente <strong>100%</strong>.
    </div>

    <div id="error-global" class="alert alert-error" style="display:none;"></div>

    <form action="resultados.php" method="POST" id="form-cuestionario" novalidate>
      <!-- Campo oculto para total de ponderación -->
      <input type="hidden" name="evaluador" value="<?php echo $evaluador; ?>">
      <input type="hidden" name="aplicacion" value="<?php echo $aplicacion; ?>">
      <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">

      <div class="progress-bar-wrap">
        <div class="progress-bar" id="progress-bar"></div>
        <span class="progress-label" id="progress-label">Suma de ponderaciones: <strong id="suma-display">0%</strong></span>
      </div>

      <?php foreach ($heuristicas as $id => $h): ?>
      <section class="heuristica-card card" id="seccion-h<?php echo $id; ?>">
        <div class="heuristica-header">
          <div class="heuristica-badge"><?php echo $h['codigo']; ?></div>
          <div>
            <h2 class="heuristica-titulo"><?php echo $h['nombre']; ?></h2>
            <div class="ponderacion-wrap">
              <label for="pond_<?php echo $id; ?>">Ponderación:</label>
              <input
                type="number"
                id="pond_<?php echo $id; ?>"
                name="ponderacion[<?php echo $id; ?>]"
                min="0"
                max="100"
                value="20"
                class="input-ponderacion"
                required
              >
              <span class="pct-label">%</span>
            </div>
          </div>
        </div>

        <div class="escala-legend">
          <span>1 = Muy deficiente</span>
          <span>2 = Deficiente</span>
          <span>3 = Aceptable</span>
          <span>4 = Bueno</span>
          <span>5 = Excelente</span>
        </div>

        <?php foreach ($h['preguntas'] as $qi => $pregunta): ?>
        <div class="pregunta-row">
          <p class="pregunta-texto">
            <span class="pregunta-num"><?php echo ($qi + 1); ?>.</span>
            <?php echo $pregunta; ?>
          </p>
          <div class="escala-btns" role="group" aria-label="Puntuación del 1 al 5">
            <?php for ($v = 1; $v <= 5; $v++): ?>
            <label class="escala-btn">
              <input
                type="radio"
                name="respuestas[<?php echo $id; ?>][<?php echo $qi; ?>]"
                value="<?php echo $v; ?>"
                required
              >
              <span><?php echo $v; ?></span>
            </label>
            <?php endfor; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </section>
      <?php endforeach; ?>

      <div class="form-actions">
        <a href="index.php" class="btn btn-secondary">← Volver</a>
        <button type="submit" class="btn btn-primary" id="btn-enviar">Ver resultados →</button>
      </div>
    </form>

    <footer class="site-footer">
      <p>Heurísticas de Nielsen · Evaluación de Usabilidad Web</p>
    </footer>
  </div>

  <script>
    // ─── Actualizar barra de progreso de ponderación ─────────────────────────
    const inputs = document.querySelectorAll('.input-ponderacion');
    const sumaDisplay = document.getElementById('suma-display');
    const progressBar = document.getElementById('progress-bar');

    function actualizarSuma() {
      let total = 0;
      inputs.forEach(inp => total += (parseInt(inp.value) || 0));
      sumaDisplay.textContent = total + '%';
      progressBar.style.width = Math.min(total, 100) + '%';
      progressBar.className = 'progress-bar ' + (total === 100 ? 'ok' : total > 100 ? 'over' : '');
    }

    inputs.forEach(inp => inp.addEventListener('input', actualizarSuma));
    actualizarSuma();

    // ─── Validación al enviar ─────────────────────────────────────────────────
    document.getElementById('form-cuestionario').addEventListener('submit', function(e) {
      const errorDiv = document.getElementById('error-global');
      const errores = [];

      // 1. Validar ponderaciones suman 100
      let totalPond = 0;
      inputs.forEach(inp => totalPond += (parseInt(inp.value) || 0));
      if (totalPond !== 100) {
        errores.push('La suma de las ponderaciones debe ser exactamente 100%. Actualmente es ' + totalPond + '%.');
      }

      // 2. Validar que todas las preguntas estén respondidas
      const grupos = document.querySelectorAll('.pregunta-row');
      let sinResponder = 0;
      grupos.forEach(grupo => {
        const radios = grupo.querySelectorAll('input[type="radio"]');
        const marcado = Array.from(radios).some(r => r.checked);
        if (!marcado) {
          sinResponder++;
          grupo.classList.add('pregunta-error');
        } else {
          grupo.classList.remove('pregunta-error');
        }
      });

      if (sinResponder > 0) {
        errores.push('Faltan ' + sinResponder + ' pregunta(s) por responder. Se resaltan en rojo.');
      }

      if (errores.length > 0) {
        e.preventDefault();
        errorDiv.innerHTML = errores.map(err => '<p>⚠ ' + err + '</p>').join('');
        errorDiv.style.display = 'block';
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });

    // Quitar resaltado de error al seleccionar respuesta
    document.querySelectorAll('input[type="radio"]').forEach(r => {
      r.addEventListener('change', function() {
        this.closest('.pregunta-row').classList.remove('pregunta-error');
      });
    });
  </script>
</body>
</html>
