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

                <!-- Enlace que redirige a otra página HTML -->
                <a href="inicio.php" class="btn">Ingresar</a>
            </div>
        </header>

        <!-- Saltos de línea para separar visualmente el contenido -->
        
        <!-- CONTENIDO PRINCIPAL: INFORMACIÓN -->
        <main>
            <!-- Título de la sección Información -->
            <h2 id="informacion">Información escuela</h2>

            <!-- Texto informativo -->
            <p>Aquí va la información de la escuela.</p>
        </main>

        <br><br><br>

        <!-- CONTENIDO PRINCIPAL: EVENTOS -->
        <main>
            <!-- Título de la sección Eventos -->
            <h2 id="eventos">Eventos</h2>

            <!-- Texto de la sección eventos -->
            <p>Aquí va la información de la escuela.</p>
        </main>

    </body>
</html>