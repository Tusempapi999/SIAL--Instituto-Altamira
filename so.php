<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Escuela</title>
    <link rel="stylesheet" href="visual_index.css">
</head>

<body>

<!-- 🔥 FONDO DINÁMICO (ESTO FALTABA) -->
<div class="fondo-galeria" id="fondo"></div>

<header class="encabezado">
    <h1>Instituto Altamira</h1>

    <div class="botones">
        <a href="#informacion" class="link-info">Información</a>
        <a href="#eventos" class="link-info">Eventos</a>
        <a href="#lugar" class="link-info">Ubicación</a>
        <a href="inicio.php" class="btn">
  <button type="button">Ingresar</button>
</a>
    </div>
</header>
<br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br>
<main>
    <h1 id="informacion">Bienvenid@s al Instituto Altamira</h1>
        
    <h2 >Nuestra Institución</h2>

    <div class="info-grid">
        <section>
            <h3>¿Quiénes Somos?</h3>
            <p>
                El <strong>Instituto Altamira</strong> es una comunidad educativa dedicada
                a la formación integral de líderes con valores sólidos y excelencia académica.
            </p>
        </section>

        <section>
            <h3>Nuestra Misión</h3>
            <p>
                Formar estudiantes críticos, creativos y responsables, capaces de transformar
                su entorno positivamente.
            </p>
        </section>

        <section>
            <h3>Valores</h3>
            <ul>
                <li><strong>Respeto</strong></li>
                <li><strong>Innovación</strong></li>
                <li><strong>Integridad</strong></li>
            </ul>
        </section>
    </div>
</main>
<br><br><br><br><br><br>
<main>
    <h2 id="eventos">Eventos Próximos</h2>

    <article>
        <h4>Entrega de Boletas</h4>
        <p><strong>Fecha:</strong> 25 de Septiembre</p>
    </article>

    <article>
        <h4>Torneo de Ajedrez</h4>
        <p><strong>Fecha:</strong> 10 de Octubre</p>
    </article>

    <article>
        <h4>Festival de Talentos</h4>
        <p><strong>Fecha:</strong> 30 de Octubre</p>
    </article>
</main>
<br><br><br><br><br><br>
<main>
    <h2 id="lugar">Ubicación</h2>

    <iframe
        src="https://www.google.com/maps?q=19.301907,-103.679573&z=18&output=embed"
        width="100%"
        height="400"
        style="border:0; border-radius:15px;"
        loading="lazy">
    </iframe>

    <p style="text-align:center; margin-top:15px;">
        <a href="https://maps.app.goo.gl/JiwKBXFeiasEstB38" target="_blank" class="btn">
            Abrir en Google Maps
        </a>
    </p>
</main>


<script>
const imagenes = [
    "fondoIndex.png",
    "galeria1.jpeg",
    "galeria3.jpeg",
    "galeria2.jpeg"
];

let indice = 0;
const fondo = document.getElementById("fondo");

function cambiarFondo() {
    fondo.style.opacity = 0;

    setTimeout(() => {
        fondo.style.backgroundImage = `url('${imagenes[indice]}')`;
        fondo.style.opacity = 1;
        indice = (indice + 1) % imagenes.length;
    }, 800);
}

cambiarFondo();
setInterval(cambiarFondo, 20000);
</script>

</body>

<footer class="encabezado">
    <h2>Contacto</h2>
    <p>Teléfono: (123) 456-7890</p>
    <p>Email: info@altamira.edu.mx</p>
</footer>
</html>