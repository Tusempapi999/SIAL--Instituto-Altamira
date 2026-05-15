<!DOCTYPE html>
<!-- Indica que el documento es HTML5 -->
<html lang="es">
    <head>
        <!-- Define la codificación de caracteres -->
        <meta charset="UTF-8">

        <!-- Descripción de la página (útil para buscadores) -->
        <meta name="description" content="Escuela">

        <!-- Título que aparece en la pestaña del navegador -->
        <title>Escuela</title>

        <!-- Enlace al archivo CSS para los estilos -->
        <link rel="stylesheet" href="visual_index.css">
    </head>

    <body>
        <!-- 
            ENCABEZADO DE LA PÁGINA
            Aquí va el nombre de la escuela y los botones de navegación
        -->
        <header class="encabezado">
            
            <!-- Nombre de la escuela -->
            <h1>Instituto Altamira</h1>

            <!-- Contenedor de los botones/enlaces -->
            <div class="botones">
                <!-- Enlace que baja a la sección Información -->
                <a href="#informacion" class="link-info">Información</a>

                <!-- Enlace que baja a la sección Eventos -->
                <a href="#eventos" class="link-info">Eventos</a>

                <!-- Enlace que baja a la sección Lugar -->
                <a href="#lugar" class="link-info">Ubicación</a>

                <!-- Enlace que redirige a otra página HTML -->
                <a href="inicio.php" class="btn">Ingresar</a>
            </div>
        </header>

        <!-- Saltos de línea para separar visualmente el contenido -->

        <!-- CONTENIDO PRINCIPAL: INFORMACIÓN -->
        <main >
            <h1>Bienvenid@s al Instituto Altamira</h1>
            <br>
            <h2 id="informacion">Nuestra Institución</h2>
            <div class="info-grid">
                <section>
                    <h3>¿Quiénes Somos?</h3>
                    <p>El <strong>Instituto Altamira</strong> es una comunidad educativa dedicada a la formación integral de líderes con valores sólidos y excelencia académica. Con más de 20 años de experiencia, nos enfocamos en el desarrollo humano y tecnológico.</p>
                </section>
                
                <section>
                    <h3>Nuestra Misión</h3>
                    <p>Formar estudiantes críticos, creativos y responsables, capaces de transformar su entorno positivamente a través de un modelo educativo bilingüe y constructivista.</p>
                </section>

                <section>
                    <h3>Valores</h3>
                    <ul>
                        <li><strong>Respeto:</strong> Valoramos la diversidad y la dignidad humana.</li>
                        <li><strong>Innovación:</strong> Aplicamos las mejores herramientas tecnológicas en el aula.</li>
                        <li><strong>Integridad:</strong> Actuamos con honestidad en cada proceso educativo.</li>
                    </ul>
                </section>
            </div>
        </main>

        <br><br><br>

        <!-- CONTENIDO PRINCIPAL: EVENTOS -->
        <main >
            <h2 id="eventos">Eventos Próximos</h2>
            <div >
                <article>
                    <h4>Entrega de Boletas - 1er Parcial</h4>
                    <p><strong>Fecha:</strong> 25 de Septiembre | <strong>Hora:</strong> 08:00 AM</p>
                    <p>Reunión obligatoria para padres de familia en el auditorio principal.</p>
                </article>

                <article >
                    <h4>Torneo Interescolar de Ajedrez</h4>
                    <p><strong>Fecha:</strong> 10 de Octubre | <strong>Lugar:</strong> Sala de Usos Múltiples</p>
                    <p>Invitamos a todos nuestros alumnos a participar en las eliminatorias del torneo regional.</p>
                </article>

                <article >
                    <h4>Festival de Talentos "Altamira 2026"</h4>
                    <p><strong>Fecha:</strong> 30 de Octubre | <strong>Hora:</strong> 06:00 PM</p>
                    <p>Una noche dedicada a la música, el arte y el teatro. ¡No te lo pierdas!</p>
                </article>
            </div>
</main>
        <main>
            <h2 id="lugar">Ubicación</h2>

            <p>Nos encontramos en la siguiente ubicación:</p>

            <iframe
                src="https://www.google.com/maps?q=19.301907,-103.679573&z=18&output=embed"
                width="100%"
                height="400"
                style="border:0; border-radius:15px;"
                allowfullscreen
                loading="lazy">
            </iframe>

            <p style="text-align:center; margin-top:15px;">
                <a href="https://maps.app.goo.gl/JiwKBXFeiasEstB38"
                target="_blank"
                class="btn">
                Abrir en Google Maps
                </a>
            </p>
        </main>

    </body>
    
</html>