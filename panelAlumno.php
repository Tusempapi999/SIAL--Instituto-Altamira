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

            <?php
            // SIMULACIÓN (luego va por SESSION)
            $alumno_id = 1; // ESTE ES alumno.id (NO usuario.id)
            ?>

            <nav class="menu-lateral">
                <a href="?accion=listara">Listar compañeros</a>
                <a href="?accion=listarp">Listar profesores</a>
                <a href="?accion=vercalificaciones">Ver calificaciones</a>
            </nav>
        </aside>

        <!-- CONTENIDO -->
        <main class="contenido">

            <header class="barra-superior">
                <h2>Panel del Alumno</h2>
                <div class="perfil">
                <span class="nombre">Alumno</span>
                <div class="circulo"></div>

                <!-- MENÚ DESPLEGABLE -->
                <div class="menu">
                    <div class="notificaciones">
                        Opciones
                    </div>        
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
    
            </div>
            </header>
            

            <div class="panel-vacio">

            <?php
            include('clases/claseAlumno.php');
            $objAlumno = new alumno();

            $accion   = $_GET['accion']   ?? '';
            $grupo_id = $_GET['grupo_id'] ?? null;

            /* =====================================================
            LISTAR COMPAÑEROS (USA matriculado + alumno + usuario)
            ===================================================== */
            if ($accion === 'listara') {

                $grupo_id = 1; // solo lectura, NO cambia BD
                $res = $objAlumno->Listar_alumnos($grupo_id);

                echo "<h2>Compañeros de clase</h2>";

                if ($res && $res->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead>
                                <tr>
                                    <th>ID Alumno</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                </tr>
                            </thead>";

                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['alumno_id']}</td>
                                <td>{$fila['nombre']}</td>
                                <td>{$fila['email']}</td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No hay compañeros.</p>";
                }
            }

            /* =====================================================
            LISTAR PROFESORES (MISMA BD)
            ===================================================== */
            elseif ($accion === 'listarp') {

                $res = $objAlumno->Listar_profesores($alumno_id);

                echo "<h2>Tus profesores</h2>";

                if ($res && $res->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead>
                                <tr>
                                    <th>Profesor</th>
                                    <th>Especialidad</th>
                                    <th>Asignatura</th>
                                </tr>
                            </thead>";

                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['nombre']}</td>
                                <td>{$fila['especialidad']}</td>
                                <td>{$fila['asignatura']}</td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No se encontraron profesores.</p>";
                }
            }

            /* =====================================================
            LISTAR ASIGNATURAS + BOTÓN
            ===================================================== */
            elseif ($accion === 'vercalificaciones' && !$grupo_id) {

                $res = $objAlumno->setCalificacion($alumno_id);

                echo "<h2>Mis asignaturas</h2>";

                if ($res && $res->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead>
                                <tr>
                                    <th>Asignatura</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>";
                            

                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['asignatura']}</td>
                                <td>
                                    <a class='btn'
                                    href='?accion=vercalificaciones&grupo_id={$fila['grupo_id']}'>
                                    Ver calificaciones
                                    </a>
                                </td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No hay asignaturas.</p>";
                }
            }

            /* =====================================================
            MOSTRAR CALIFICACIONES (FINAL DEL PANEL)
            ===================================================== */
            elseif ($accion === 'vercalificaciones' && $grupo_id) {

                $res = $objAlumno->verCalificacion($alumno_id, $grupo_id);

                echo "<h2>Detalle de calificaciones</h2>";

                if ($res && $res->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead>
                            <tr>
                                <th>Asignatura</th>
                                <th>Parcial 1</th>
                                <th>Parcial 2</th>
                                <th>Parcial 3</th>
                                <th>Promedio Final</th>
                            </tr>
                            </thead>";

                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['asignatura']}</td>
                                <td>{$fila['calificacion_1']}</td>
                                <td>{$fila['calificacion_2']}</td>
                                <td>{$fila['calificacion_3']}</td>
                                <td><strong>{$fila['promedio_final']}</strong></td>
                            </tr>";
                    }
                    echo "</table>";

                    echo "<input type='hidden' name='accion' value='vercalificaciones'>
                        <button type='submit' class='btn'>Volver</button>";
                } else {
                    echo "<p>No hay calificaciones.</p>";
                }
            }

            /* =====================================================
            PANEL INICIAL
            ===================================================== */
            else {
                echo "<h3>Seleccione una opción del menú</h3>";
            }
            ?>

            </div>
        </main>
    </div>

    </body>
</html>