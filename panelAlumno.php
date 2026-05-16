<?php
    session_start();

    include('clases/claseAlumno.php');
    $objAlumno = new alumno();

    /* ===============================
    OBTENER ID DE ALUMNO
    =============================== */
    $id_alumno_url = isset($_GET['alumno_id']) ? $_GET['alumno_id'] : null;

    $grupo_id = isset($_GET['grupo_id']) ? $_GET['grupo_id'] : null;

    /* usar sesión si no hay GET */
    if (!$id_alumno_url && isset($_SESSION['id_usuario'])) {
        $id_alumno_url = $_SESSION['id_usuario'];
    }

    /* acción */
    $accion = isset($_GET['accion']) ? $_GET['accion'] : '';

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Panel Alumno</title>
        <link rel="stylesheet" href="visual_maestro.css">
    </head>

    <body>

        <div class="contenedor">

            <!-- SIDEBAR -->
            <aside class="sidebar">

                <div class="logo">
                    SIAL<br><span>Altamira</span>
                </div>

                <nav class="menu-lateral">

                    <h3 style="color:white; padding:10px;">Opciones</h3>

                    <a href="?accion=listara&alumno_id=<?php echo $id_alumno_url; ?>">
                        Listar alumnos
                    </a>

                    <a href="?accion=listarp&alumno_id=<?php echo $id_alumno_url; ?>">
                        Profesores
                    </a>

                    <hr style="border:1px solid rgba(255,255,255,0.2); margin:10px 0;">

                    <h4 style="color:white; padding:10px;">Califiaciones</h4>

                    <?php
                    if ($id_alumno_url) {

                        $resultado = $objAlumno->setCalificacion($id_alumno_url);

                        if ($resultado && $resultado->num_rows > 0) {

                            while ($datos = $resultado->fetch_assoc()) {

                                echo "<a href='?accion=vercalificaciones&alumno_id=$id_alumno_url&grupo_id={$datos['grupo_id']}'>
                                        {$datos['asignatura']}
                                    </a>";

                                echo "<a href='?accion=listara&alumno_id=$id_alumno_url&grupo_id={$datos['grupo_id']}'>
                                        {$datos['asignatura']}
                                    </a>";
                                
                                echo "<a href='?accion=listarp&alumno_id=$id_alumno_url&grupo_id={$datos['grupo_id']}'>
                                        {$datos['asignatura']}
                                    </a>";
                            }

                        } else {
                            echo "<p style='color:white; padding:10px;'>Sin asignaturas</p>";
                        }

                    } else {
                        echo "<p style='color:white; padding:10px;'>Sin sesión</p>";
                    }
                    ?>

                </nav>
            </aside>

            <!-- CONTENIDO -->
            <main class="contenido">

                <header class="barra-superior">
                    <h2>Bienvenido al panel del Alumno</h2>

                    <div class="perfil">
                <span class="nombre">Alumno</span>
                <div class="circulo"></div>
                <div class="menu">
                    <div class="notificaciones">Opciones</div>
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
            </div>
                </header>

                <div class="panel-vacio">

                <?php

                /* ===============================
                LISTAR ALUMNOS
                ================================ */
                if ($accion == 'listara') {
                    
                    $res = $objAlumno->Listar_alumnos($grupo_id);

                    if ($res && $res->num_rows > 0) {

                        echo "<h2>Compañeros de clase</h2>
                            <table class='tabla-alumno'>
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>";

                        while ($fila = $res->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$fila['alumno_id']}</td>
                                    <td>{$fila['nombre']}</td>
                                    <td>{$fila['email']}</td>
                                </tr>";
                        }

                        echo "</tbody></table>";

                    } else {
                        echo "<p>No hay alumnos en este grupo.</p>";
                    }
                }

                /* ===============================
                LISTAR PROFESORES
                ================================ */
                elseif ($accion == 'listarp') {

                    if ($id_alumno_url) {

                        $resultado = $objAlumno->Listar_profesores($id_alumno_url);

                        if ($resultado && $resultado->num_rows > 0) {

                            echo "<h2>Profesores</h2>
                                <table class='tabla-alumno'>
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Especialidad</th>
                                        <th>Asignatura</th>
                                    </tr>
                                </thead>
                                <tbody>";

                            while ($datos = $resultado->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$datos['nombre']}</td>
                                        <td>{$datos['especialidad']}</td>
                                        <td>{$datos['asignatura']}</td>
                                    </tr>";
                            }

                            echo "</tbody></table>";

                        } else {
                            echo "<p>No hay profesores.</p>";
                        }

                    } else {
                        echo "<p>Falta ID de alumno.</p>";
                    }
                }


                /* ===============================
                VER CALIFICACIONES
                ================================ */
                elseif ($accion == 'vercalificaciones') {

                    if (isset($_GET['grupo_id']) && $id_alumno_url) {

                        $grupo_id = $_GET['grupo_id'];
                        $resultado = $objAlumno->verCalificacion($id_alumno_url, $grupo_id);

                        if ($resultado && $resultado->num_rows > 0) {

                            echo "<h2>Calificaciones</h2>
                                <table class='tabla-alumno'>
                                <thead>
                                    <tr>
                                        <th>Asignatura</th>
                                        <th>P1</th>
                                        <th>P2</th>
                                        <th>P3</th>
                                        <th>Promedio</th>
                                    </tr>
                                </thead>
                                <tbody>";

                            while ($datos = $resultado->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$datos['asignatura']}</td>
                                        <td>{$datos['calificacion_1']}</td>
                                        <td>{$datos['calificacion_2']}</td>
                                        <td>{$datos['calificacion_3']}</td>
                                        <td>{$datos['promedio_final']}</td>
                                    </tr>";
                            }

                            echo "</tbody></table>";

                        } else {
                            echo "<p>No se encontraron calificaciones</p>";
                        }

                    } else {
                        echo "<p>Falta información</p>";
                    }
                }
                else {
                    echo "<h3>Seleccione una opción del menú</h3>";
                }
                ?>

                </div>

            </main>
        </div>

    </body>
</html>