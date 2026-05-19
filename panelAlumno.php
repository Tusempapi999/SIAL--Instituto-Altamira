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
                <a href="?accion=verhorario">Ver horario</a>
                <a href="?accion=listara">Listar compañeros</a>
                <a href="?accion=listarp">Listar profesores</a>
                <a href="?accion=faltasasistencias">Faltas y asistencias</a>
                <a href="?accion=vercalificaciones">Ver calificaciones</a>
                <a href="?accion=clubes">Clubes sabatinos</a>
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

            $accion = $_GET['accion']   ?? '';
            $grupo_id = $_GET['grupo_id'] ?? null;

            /* =====================================================
            LISTAR COMPAÑEROS (USA matriculado + alumno + usuario)
            ===================================================== */
            if ($accion == 'listara') {

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
            elseif ($accion == 'listarp') {

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
            elseif ($accion == 'vercalificaciones' && !$grupo_id) {

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
            elseif ($accion == 'vercalificaciones' && $grupo_id) {

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

            /* =====================================================
            LISTAR ASIGNATURAS para FALTAS DE ASISTENCIA
            ===================================================== */

            /* =====================================================
            SECCIÓN: FALTAS DE ASISTENCIA
            ===================================================== */
            if ($accion == 'faltasasistencias') {

                // El alumno NO ha hecho clic en ninguna materia todavía (Listar materias)
                if (!$grupo_id) {
                    
                    $res = $objAlumno->setCalificacion($alumno_id); 

                    echo "<h2>Selecciona una asignatura para ver tus faltas</h2>";

                    if ($res && $res->num_rows > 0) {
                        echo "<table class='tabla-alumno'>
                                <thead><tr><th>Asignatura</th><th>Acción</th></tr></thead>";
                        while ($fila = $res->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$fila['asignatura']}</td>
                                    <td>
                                        <a class='btn' href='?accion=faltasasistencias&grupo_id={$fila['grupo_id']}'>
                                            Ver faltas
                                        </a>
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No estás matriculado en ninguna asignatura.</p>";
                    }
                } 
                // El alumno YA hizo clic en una materia (Mostrar las faltas de esa materia)
                else {
                    $resFaltas = $objAlumno->verFaltasAsistencia($grupo_id, $alumno_id);
                    
                    echo "<h2>Detalle de Faltas</h2>";
                    echo "<a href='?accion=faltasasistencias' class='btn'>Volver al listado</a><br><br>";

                    if ($resFaltas && $resFaltas->num_rows > 0) {
                        echo "<table class='tabla-alumno'>
                                <thead><tr><th>Fecha</th><th>Estado</th></tr></thead>";
                        while ($falta = $resFaltas->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$falta['fecha']}</td>
                                    <td>{$falta['estado']}</td>
                                </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No tienes faltas en esta asignatura.</p>";
                    }
                }
            }

            if ($accion == 'verhorario') {
                
            // Si no se ha seleccionado grupo, listar asignaturas
            if (!$grupo_id) {
                    
                $resMaterias = $objAlumno->setCalificacion($alumno_id); 
                    
                echo "<h2>Mi horario</h2>";
                if ($resMaterias && $resMaterias->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Asignatura</th><th>Acción</th></tr></thead>";
                    while ($materia = $resMaterias->fetch_assoc()) {
                        echo "<tr>
                                <td>{$materia['asignatura']}</td>
                                <td><a class='btn' href='?accion=verhorario&grupo_id={$materia['grupo_id']}'>Consultar Horario</a></td>
                            </tr>";
                        }
                        echo "</table>";
                    }
                } 
                // Si ya se seleccionó una materia, mostrar su horario
                else {
                    $resHorario = $objAlumno->verHorarioAsignatura($grupo_id);
                    
                    echo "<h2>Horario de asignatura</h2>";
                    echo "<a href='?accion=verhorario' class='btn'>Volver al listado</a><br><br>";

                    if ($resHorario && $resHorario->num_rows > 0) {
                        echo "<table class='tabla-alumno'>
                                <thead>
                                    <tr><th>Día</th><th>Hora Inicio</th><th>Hora Fin</th></tr>
                                </thead>";
                        while ($h = $resHorario->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$h['dia_semana']}</td>
                                    <td>{$h['hora_inicio']}</td>
                                    <td>{$h['hora_fin']}</td>
                                </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No hay un horario registrado</p>";
                    }
                }

            }
            /* =====================================================
            CLUBES SABATINOS
            ===================================================== */
            if ($accion == 'clubes') {
            echo "<div class='seccion-clubes'>";

            // PROCESO DE INSCRIPCIÓN
            if (isset($_GET['inscribir_id'])) {
            $id_del_grupo = $_GET['inscribir_id'];

            $misClubs = $objAlumno->listarMisClubs($alumno_id);
            if ($misClubs && $misClubs->num_rows > 0) {
                echo "<h2>No puedes inscribirte</h2>";
                echo "<p>Ya estás inscrito en un club</p>";
                echo "<a href='?accion=clubes' class='btn'>Volver</a>";
            } else {
                $objAlumno->inscribirClub($alumno_id, $id_del_grupo);
                echo "<h2>Te has inscrito con éxito</h2>";
                echo "<a href='?accion=clubes' class='btn'>Volver a mi panel de clubes</a>";
            }
        }

            // VER ASISTENCIAS
            elseif (isset($_GET['grupo_id'])) {
                $resAsis = $objAlumno->verAsistenciaClub($_GET['grupo_id'], $alumno_id);

                echo "<h2>Control de Asistencia</h2>";
                echo "<a href='?accion=clubes' class='btn'>Volver</a>";
                
                if ($resAsis && $resAsis->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Fecha</th><th>Estado</th></tr></thead>";
                    while ($asist = $resAsis->fetch_assoc()) {
                        echo "<tr><td>{$asist['fecha']}</td><td>{$asist['estado']}</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Aún no se han registrado asistencias para este club</p>";
                }
            }

            // PANTALLA PRINCIPAL de listados de clubes
            else {
                echo "<h2>Clubes Sabatinos</h2>";

                // Tabla de Clubes Inscritos
                $misClubes = $objAlumno->listarMisClubs($alumno_id);
                echo "<h3>Mis Clubes</h3>";
                if ($misClubes && $misClubes->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Club</th><th>Acción</th></tr></thead>";
                    while ($fila = $misClubes->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['club']}</td>
                                <td><a class='btn' href='?accion=clubes&grupo_id={$fila['grupo_id']}'>Ver Asistencias</a></td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No tienes inscripciones activas.</p>";
                }

                // Tabla de Clubes Disponibles para Inscribirse
                $disponibles = $objAlumno->listarClubesDisponibles($alumno_id);
                echo "<h3>Clubes Disponibles para Inscripción</h3>";
                if ($disponibles && $disponibles->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Nombre del Club</th><th>Acción</th></tr></thead>";
                    while ($filaD = $disponibles->fetch_assoc()) {
                        echo "<tr>
                                <td>{$filaD['club']}</td>
                                <td><a class='btn' href='?accion=clubes&inscribir_id={$filaD['grupo_id']}'>Inscribirme</a></td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No se han registrado asistencias para este club</p>";
                }
            }
        }
            
            ?>
            
            </div>
        </main>
    </div>

    </body>
</html>