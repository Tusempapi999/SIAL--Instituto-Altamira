<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instituto Altamira</title>
    <link rel="stylesheet" href="visual_index.css">
    <style>
        /* Estilos integrados para la galería y el modal flotante */
        .galeria-seccion {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .galeria-seccion h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .galeria-item {
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
        }
        .galeria-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }
        .galeria-item img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }
        /* Modal Ventana Flotante */
        .modal {
            display: none; 
            position: fixed;
            z-index: 2000;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            max-width: 85%;
            max-height: 75vh;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(255,255,255,0.2);
        }
        .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #fff;
            font-size: 50px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }
        .close:hover {
            color: #bbb;
        }
        /* Separadores limpios reemplazando los excesivos <br> */
        .espaciador {
            margin-top: 80px;
        }
    </style>
</head>

<body>

<div class="fondo-galeria" id="fondo"></div>

<header class="encabezado">
    <h1>Instituto Altamira</h1>
    <div class="botones">
        <a href="#informacion" class="link-info">Información</a>
        <a href="#eventos" class="link-info">Eventos</a>
        <a href="#galeria" class="link-info">Galería</a>
        <a href="#lugar" class="link-info">Ubicación</a>
        <a href="#nuestra-informacion" class="link-info">Nuestra información</a>
        <a href="inicio.php" class="btn">
            <button type="button">Ingresar</button>
        </a>
    </div>
</header>

<div style="margin-top: 450px;"></div>

<main>
    <section id="informacion">
        <h1>Bienvenid@s al Instituto Altamira</h1>
        <h2>Nuestra Institución</h2>
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
                    Formar estudiantes críticos, creative y responsables, capaces de transformar
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
    </section>
</main>
<br>
<main>
    <section id="eventos" class="espaciador">
        <h2>Eventos Próximos</h2>
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
    </section>
</main>
<br>
<main>
    <section id="galeria" class="galeria-seccion espaciador">
        <h2>Nuestra Galería</h2>
        <div class="galeria-grid">
            <?php
            // Revisa automáticamente tu carpeta de imágenes para renderizar la galería
            $carpeta = "galeria/"; 
            
            if (is_dir($carpeta)) {
                if ($dir = opendir($carpeta)) {
                    while (($archivo = readdir($dir)) !== false) {
                        $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                        if ($archivo != '.' && $archivo != '..' && in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            echo '
                            <div class="galeria-item" onclick="abrirModal(\''.$carpeta.$archivo.'\')">
                                <img src="'.$carpeta.$archivo.'" alt="Campus Altamira">
                            </div>';
                        }
                    }
                    closedir($dir);
                }
            } else {
                echo "<p style='text-align:center; width:100%;'>Por favor, crea una carpeta llamada 'imagenes/' al lado de este archivo e introduce tus fotos allí.</p>";
            }
            ?>
        </div>
    </section>
</main>
<br>
<main>
    <div id="miModal" class="modal">
        <span class="close" onclick="cerrarModal()">×</span>
        <img class="modal-content" id="imgGrande">
    </div>

    <section id="lugar" class="espaciador">
        <h2>Ubicación</h2>
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
    </section>
</main>

<script>
// Lista de imágenes corregida según los nombres reales que subiste
const imagenes = [
    "FondoIndex.jpg",
    "galeria1.jpeg",
    "galeria2.jpeg",
    "galeria3.jpeg",
    "galeria4.png",
    "galeria5.png"
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
setInterval(cambiarFondo, 10000);

// Funciones para abrir y cerrar el visualizador interactivo
function abrirModal(rutaImagen) {
    document.getElementById("miModal").style.display = "block";
    document.getElementById("imgGrande").src = rutaImagen;
}

function cerrarModal() {
    document.getElementById("miModal").style.display = "none";
}

// Cerrar ventana si se hace clic fuera de la foto ampliada
window.onclick = function(event) {
    const modal = document.getElementById("miModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<footer class="encabezado espaciador">
    <h2 id="nuestra-informacion">Contacto</h2>
    <p>Teléfono: (123) 456-7890</p>
    <p>Email: info@altamira.edu.mx</p>
</footer>

</body>
</html>