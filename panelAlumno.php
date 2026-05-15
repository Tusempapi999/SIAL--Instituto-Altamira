<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Alumno</title>
    <link rel="stylesheet" href="visual_maestro.css">

</head>
<body>

<div class="contenedor">

    <!-- Barra lateral -->
    <aside class="sidebar">
        <div class="logo">
            SIAL<br><span> Altamira</span>
        </div>

        <nav class="menu-lateral">
            <a href="?accion=listara&alumno_id=1">Listar alumnos de clase</a>
            
            <a href="?accion=listarp&alumno_id=1">Listar profesores</a>
            
            <a href="setCalificacion.php?alumno_id=1">Ver calificaciones</a>
        </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenido">

        <!-- BARRA SUPERIOR -->
        <header class="barra-superior">

            <span><h2>Bienvenido al panel del Alumno</h2></span>

            <!-- PERFIL -->
            <div class="perfil">
                <span class="nombre">Alumnos</span>
                <div class="circulo"></div>

                <!-- MENÚ DESPLEGABLE -->
                <div class="menu">
                    <div class="notificaciones">
                        Notificaciones
                    </div>        
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
            </div>

        </header>
    <div class="panel-vacio">
        <!-- PANEL VACÍO -->
        <?php
            include('clases/claseAlumno.php');
            $objAlumno = new alumno();

            // Verificamos qué acción se solicitó por la URL
            $accion = isset($_GET['accion']) ? $_GET['accion'] : '';
            $id_alumno_url = isset($_GET['alumno_id']) ? $_GET['alumno_id'] : null;

            // CASO 1: LISTAR ALUMNOS
            if ($accion == 'listara') {
                $grupo_id = 1; // Debería ser dinámico según el alumno
                $res = $objAlumno->Listar_alumnos($grupo_id);

                if ($res && $res->num_rows > 0) {
                    echo "<h2>Compañeros de clase</h2> <br>";
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Matrícula</th><th>Nombre</th><th>Email</th></tr></thead>
                            <tbody>";
                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['id']}</td>
                                <td>{$fila['nombre']}</td>
                                <td>{$fila['email']}</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No hay alumnos en este grupo.</p>";
                }
            } 
            // CASO 2: LISTAR PROFESORES
            elseif ($accion == 'listarp' && $id_alumno_url) {
                $resultado = $objAlumno->Listar_profesores($id_alumno_url);

                if ($resultado && $resultado->num_rows > 0) {
                    echo "<h2>Tus profesores</h2> <br>";
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Nombre</th><th>Especialidad</th><th>Asignatura</th></tr></thead>
                            <tbody>";
                    while ($datos = $resultado->fetch_assoc()) {
                        echo "<tr>
                                <td>".$datos['nombre']."</td>
                                <td>".$datos['especialidad']."</td>
                                <td>".$datos['asignatura']."</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No se encontraron profesores para este alumno.</p>";
                }
            } 
            // CASO POR DEFECTO: PANEL VACÍO
            else {
                echo "<h3>Seleccione una opción del menú para comenzar</h3>";
            }
            ?>
        </div>

    </main>

</div>

</body>
</html>
        