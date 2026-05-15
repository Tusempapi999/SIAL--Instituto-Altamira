32243567gw2<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="visual_maestro.css">

</head>
<body>

<div class="contenedor">

    <!-- Barra lateral -->
    <aside class="sidebar">
        <div class="logo">
            Colegio<br><span>Nuevo Futuro</span>
            <br>
        </div>
        ______________________<br>
        <nav class="menu-lateral">
            <a href="">Usuario</a>
            ______________________<br>
            <a href="pruebaAsignaturas.php">Asignatura</a>
            ______________________<br>
            <a href="-">Matricular alumno en asignatura</a>
            ______________________<br>
        </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenido">

        <!-- BARRA SUPERIOR -->
        <header class="barra-superior">

            <span><h2>Bienvenido al panel del Administrador</h2></span>

            <!-- PERFIL -->
            <div class="perfil">
                <span class="nombre">---</span>
                <div class="circulo"></div>

                <!-- MENÚ DESPLEGABLE -->
                <div class="menu">
                    <div class="notificaciones">
                        Notificaciones
                    </div>        
                    <a href="#">Configuración</a>
                    <a href="#">Ayuda</a>
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
    
            </div>

        </header>

        <!-- PANEL VACÍO -->
        <div class="panel-vacio">
            <tbody>
                <tr>
                    <td>id</td>
                    <td>nombre</td>
                    <td>email</td>
                    <td>rol</td>
                    <td>fecha_nacimiento</td>
                </tr>
    
            </tbody>
        </div>

    </main>

</div>

</body>
</html>
        