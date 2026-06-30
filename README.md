# Evaluador de Usabilidad — Heurísticas de Nielsen

Herramienta web desarrollada en PHP para evaluar la usabilidad de aplicaciones web mediante las 5 heurísticas de Nielsen.

## Archivos del proyecto

```
evaluador-usabilidad/
├── index.php        → Página inicial (nombre evaluador + aplicación)
├── cuestionario.php → 15 preguntas + ponderaciones por heurística
├── resultados.php   → Cálculos, tabla de resultados y gráfica radar
├── estilos.css      → Estilos visuales (diseño minimalista)
└── railway.toml     → Configuración para deploy en Railway
```

## Heurísticas evaluadas

1. Visibilidad del estado del sistema
2. Consistencia y estándares
3. Prevención de errores
4. Diseño minimalista y estético
5. Control y libertad del usuario

## Cómo publicar en Railway (para compartir el link)

1. Crea cuenta en [railway.app](https://railway.app)
2. Sube estos archivos a un repositorio en GitHub
3. En Railway → "New Project" → "Deploy from GitHub repo"
4. Selecciona el repositorio → Railway detecta PHP automáticamente
5. Copia el link público que te genera Railway
6. Comparte ese link con tu evaluador

## Uso local (con XAMPP)

1. Instala XAMPP
2. Copia la carpeta a `C:/xampp/htdocs/evaluador-usabilidad/`
3. Abre `http://localhost/evaluador-usabilidad/`

## Tecnologías

- PHP (sesiones, procesamiento de formularios, cálculos)
- HTML5 / CSS3
- JavaScript (validaciones del lado cliente)
- Chart.js 4.4 (gráfica radar de resultados)
