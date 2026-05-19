<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Panel Alumno</title>
        <link rel="stylesheet" href="visual_maestro.css">
    </head>
    <body>

    <div class="contenedor">

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

        <main class="contenido">
            <header class="barra-superior">
                <h2>Panel del Alumno</h2>
                <div class="perfil">
                <span class="nombre">Alumno</span>
                <div class="circulo"></div>

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
            LISTAR COMPAÑEROS
            ===================================================== */
            if ($accion == 'listara') {

                $grupo_id = 1; 
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
                    echo "<p>No hay compañeros.</p>";
                }
            }

            /* =====================================================
            LISTAR PROFESORES
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
                            </thead>
                            <tbody>";

                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['nombre']}</td>
                                <td>{$fila['especialidad']}</td>
                                <td>{$fila['asignatura']}</td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No se encontraron profesores.</p>";
                }
            }

            /* =====================================================
                VER CALIFICACIONES (TODAS EN UNA SOLA TABLA GENERAL)
                ===================================================== */
                elseif ($accion == 'vercalificaciones') {

                    // Cambiamos a setCalificacion que sí sabe listar todas las materias del alumno de golpe
                    $res = $objAlumno->setCalificacion($alumno_id); 

                    echo "<h2>Calificaciones</h2><br>";
                    echo "<table class='tabla-alumno'>
                            <thead>
                                <tr>
                                    <th>Materia</th>
                                    <th>Parcial 1</th>
                                    <th>Parcial 2</th>
                                    <th>Parcial 3</th>
                                    <th>Promedio</th>
                                </tr>
                            </thead>
                            <tbody>";

                    if ($res && $res->num_rows > 0) {
                        while ($fila = $res->fetch_assoc()) {
                            
                            // Convertimos a flotante. Si los nombres en tu BD son diferentes, asegúrate de poner los correctos aquí
                            $p1 = isset($fila['calificacion_1']) ? floatval($fila['calificacion_1']) : 0;
                            $p2 = isset($fila['calificacion_2']) ? floatval($fila['calificacion_2']) : 0;
                            $p3 = isset($fila['calificacion_3']) ? floatval($fila['calificacion_3']) : 0;

                            // Cálculo automático del promedio con dos decimales
                            $promedio = number_format(($p1 + $p2 + $p3) / 3, 2);

                            echo "<tr>
                                    <td><strong>{$fila['asignatura']}</strong></td>
                                    <td>{$p1}</td>
                                    <td>{$p2}</td>
                                    <td>{$p3}</td>
                                    <td><strong>{$promedio}</strong></td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No tienes materias o calificaciones registradas actualmente.</td></tr>";
                    }
                    echo "</tbody></table>";
                }

            
            /* =====================================================
            SECCIÓN: FALTAS DE ASISTENCIA
            ===================================================== */
            if ($accion == 'faltasasistencias') {


                // El alumno NO ha hecho clic en ninguna materia todavía (Listar materias)
                if (!$grupo_id) {
                    $res = $objAlumno->setCalificacion($alumno_id); 

                    echo "<h2>Selecciona una asignatura para ver tus faltas</h2><br>";

                    if ($res && $res->num_rows > 0) {
                        echo "<table class='tabla-alumno'>
                                <thead><tr><th>Asignatura</th><th>Acción</th></tr></thead>
                                <tbody>";
                        while ($fila = $res->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$fila['asignatura']}</td>
                                    <td>
                                        <a  href='?accion=faltasasistencias&grupo_id={$fila['grupo_id']}'
                                         class='btn-regresar'
                                        style='background:#2ecc71'>
                                            Ver faltas
                                        </a>
                                    </td>
                                  </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No estás matriculado en ninguna asignatura.</p>";
                    }
                } 

                // El alumno YA hizo clic en una materia (Mostrar las faltas de esa materia)

                else {
                    $resFaltas = $objAlumno->verFaltasAsistencia($grupo_id, $alumno_id);
                    
                    echo "<h2>Detalle de Faltas</h2>";
                    

                    if ($resFaltas && $resFaltas->num_rows > 0) {
                        echo "<table class='tabla-alumno'>
                                <thead><tr><th>Fecha</th><th>Estado</th></tr></thead>
                                <tbody>";
                        while ($falta = $resFaltas->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$falta['fecha']}</td>
                                    <td>{$falta['estado']}</td>
                                  </tr>";
                        }
                        echo "</tbody></table> <br>";

                        echo "<a href='?accion=faltasasistencias' class='btn-regresar'>Volver al listado</a><br><br>";
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
                        echo "<a href='?accion=verhorario' class='btn-regresar'>Volver al listado</a><br><br>";
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