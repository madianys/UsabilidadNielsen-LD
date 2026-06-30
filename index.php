<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Evaluador de Usabilidad — Heurísticas de Nielsen</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <div class="container">
    <header class="site-header">
      <div class="logo-mark">EU</div>
      <h1>Evaluador de Usabilidad</h1>
      <p class="subtitle">Basado en las Heurísticas de Nielsen</p>
      <p class="subtitle">por: Luis Diego Lezcano</p>
    </header>

    <main class="card">
      <h2 class="card-title">Información de la evaluación</h2>
      <p class="card-desc">Completa los datos antes de comenzar el cuestionario.</p>

      <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error">Por favor completa todos los campos.</div>
      <?php endif; ?>

      <form action="cuestionario.php" method="POST" id="form-inicio" novalidate>
        <div class="field">
          <label for="evaluador">Nombre del evaluador</label>
          <input
            type="text"
            id="evaluador"
            name="evaluador"
            placeholder="Ej. María García"
            autocomplete="name"
            required
          >
          <span class="field-hint">Tu nombre completo</span>
        </div>

        <div class="field">
          <label for="aplicacion">Nombre de la aplicación evaluada</label>
          <input
            type="text"
            id="aplicacion"
            name="aplicacion"
            placeholder="Ej. Portal Estudiantil UTP"
            required
          >
          <span class="field-hint">El sistema o sitio web que vas a evaluar</span>
        </div>

        <div class="field">
          <label for="fecha">Fecha de evaluación</label>
          <input
            type="date"
            id="fecha"
            name="fecha"
            value="<?php echo date('Y-m-d'); ?>"
            required
          >
        </div>

        <button type="submit" class="btn btn-primary">
          Comenzar evaluación →
        </button>
      </form>
    </main>

    <footer class="site-footer">
      <p>Heurísticas de Nielsen · Evaluación de Usabilidad Web</p>
    </footer>
  </div>

  <script>
    document.getElementById('form-inicio').addEventListener('submit', function(e) {
      const evaluador = document.getElementById('evaluador').value.trim();
      const aplicacion = document.getElementById('aplicacion').value.trim();
      const fecha = document.getElementById('fecha').value.trim();

      if (!evaluador || !aplicacion || !fecha) {
        e.preventDefault();
        const existing = document.querySelector('.alert');
        if (!existing) {
          const alert = document.createElement('div');
          alert.className = 'alert alert-error';
          alert.textContent = 'Por favor completa todos los campos.';
          document.querySelector('.card-desc').after(alert);
        }
      }
    });
  </script>
</body>
</html>
