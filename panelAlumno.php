<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Alumno</title>
    <link rel="stylesheet" href="visual_maestro.css">
    <style>
        /* ESTILOS PARA EL MODAL DE CONFIRMACIÓN */
        .modal-club {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.5); 
            align-items: center;
            justify-content: center;
        }
        .modal-club-contenido {
            background-color: #fefefe;
            margin: auto;
            padding: 25px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            text-align: left;
        }
        .modal-club-header {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .modal-club-detalles {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #555;
        }
        .modal-club-detalles strong {
            color: #111;
        }
        .modal-club-acciones {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-modal-cancelar {
            background-color: #e74c3c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-modal-confirmar {
            background-color: #2ecc71;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
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
                    <div class="notificaciones">Opciones</div>        
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
            </div>
        </header>
        
        <div class="panel-vacio">

        <?php
        include('clases/claseAlumno.php');
        $objAlumno = new alumno();

        $accion = $_GET['accion'] ?? '';
        $grupo_id = $_GET['grupo_id'] ?? null;
        $club_id = $_GET['club_id'] ?? null;

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
        VER CALIFICACIONES
        ===================================================== */
        elseif ($accion == 'vercalificaciones') {
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
                    $p1 = isset($fila['calificacion_1']) ? floatval($fila['calificacion_1']) : 0;
                    $p2 = isset($fila['calificacion_2']) ? floatval($fila['calificacion_2']) : 0;
                    $p3 = isset($fila['calificacion_3']) ? floatval($fila['calificacion_3']) : 0;

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
        FALTAS DE ASISTENCIA
        ===================================================== */
        elseif ($accion == 'faltasasistencias') {
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
                                    <a href='?accion=faltasasistencias&grupo_id={$fila['grupo_id']}' class='btn-regresar' style='background:#2ecc71'>
                                        Ver faltas
                                    </a>
                                </td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No estás matriculado en ninguna asignatura.</p>";
                }
            } else {
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
                } else {
                    echo "<p>No tienes faltas en esta asignatura.</p><br>";
                }
                echo "<a href='?accion=faltasasistencias' class='btn-regresar'>Volver al listado</a><br><br>";
            }
        }

        /* =====================================================
        VER HORARIO
        ===================================================== */
        elseif ($accion == 'verhorario') {
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
                } else {
                    echo "<p>No estás matriculado en ninguna asignatura.</p>";
                }
            } else {
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
                    echo "</table><br>";
                } else {
                    echo "<p>No hay un horario registrado</p><br>";
                }
                echo "<a href='?accion=verhorario' class='btn-regresar'>Volver al listado</a><br><br>";
            }
        }

        /* =====================================================
        CLUBES SABATINOS
        ===================================================== */
        elseif ($accion == 'clubes') {
            echo "<div class='seccion-clubes'>";

            // Procesar inscripción de un club seleccionado
            if (isset($_GET['inscribir_id'])) {
                $id_del_club = intval($_GET['inscribir_id']);
                $misClubs = $objAlumno->listarMisClubs($alumno_id);

                if ($misClubs && $misClubs->num_rows > 0) {
                    echo "<h2>No puedes inscribirte</h2>";
                    echo "<p>Ya estás inscrito en un club sabatino actualmente.</p><br>";
                } else {
                    $objAlumno->inscribirClub($alumno_id, $id_del_club);
                    echo "<h2>Te has inscrito con éxito</h2>";
                }
                echo "<a href='?accion=clubes' class='btn'>Volver a mi panel de clubes</a>";
            }

            // Ver asistencias del club seleccionado
            elseif (isset($_GET['club_id'])) {
                $id_del_club = intval($_GET['club_id']);
                $resAsis = $objAlumno->verAsistenciaClub($id_del_club, $alumno_id);

                echo "<h2>Control de Asistencia del Club</h2>";
                
                if ($resAsis && $resAsis->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Fecha</th><th>Estado</th></tr></thead><tbody>";
                    while ($asist = $resAsis->fetch_assoc()) {
                        echo "<tr><td>{$asist['fecha']}</td><td>{$asist['estado']}</td></tr>";
                    }
                    echo "</tbody></table><br>";
                } else {
                    echo "<p>Aún no se han registrado asistencias para ti en este club.</p><br>";
                }
                echo "<a href='?accion=clubes' class='btn'>Volver</a>";
            }

            // Listado General Principal de Clubes
            else {
                echo "<h2>Clubes Sabatinos</h2>";

                // 1. Clubes inscritos por el alumno
                $misClubes = $objAlumno->listarMisClubs($alumno_id);
                echo "<h3>Mis Clubes</h3>";
                if ($misClubes && $misClubes->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Club</th><th>Acción</th></tr></thead><tbody>";
                    while ($fila = $misClubes->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['club']}</td>
                                <td><a class='btn' href='?accion=clubes&club_id={$fila['club_id']}'>Ver Asistencias</a></td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No tienes inscripciones activas.</p>";
                }

                // 2. Clubes Disponibles en el sistema
                $disponibles = $objAlumno->listarClubesDisponibles($alumno_id);
                echo "<br><br> <h3>Clubes Disponibles para Inscripción</h3>";
                if ($disponibles && $disponibles->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Nombre del Club</th><th>Cupo Máx.</th><th>Acción</th></tr></thead><tbody>";
                    while ($filaD = $disponibles->fetch_assoc()) {
                        // Resguardamos las variables limpias para inyectar en la función JS
                        $nombre_club = htmlspecialchars($filaD['club'], ENT_QUOTES, 'UTF-8');
                        $descripcion_club = isset($filaD['descripcion']) ? htmlspecialchars($filaD['descripcion'], ENT_QUOTES, 'UTF-8') : 'Sin descripción disponible.';
                        $cupo = intval($filaD['cupo_maximo']);
                        $id_c = intval($filaD['club_id']);

                        echo "<tr>
                                <td>{$nombre_club}</td>
                                <td>{$cupo} vacantes</td>
                                <td>
                                    <button type='button' class='btn' style='cursor:pointer; border:none;' 
                                            onclick=\"abrirConfirmacion('{$id_c}', '{$nombre_club}', '{$descripcion_club}', '{$cupo}')\">
                                        Inscribirme
                                    </button>
                                </td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No hay clubes disponibles para inscripción en este momento o ya estás dentro de uno.</p>";
                }
            }
            echo "</div>";
        }
        ?>
        
        </div>
    </main>
</div>

<div id="modalConfirmarClub" class="modal-club" style="display: none;">
    <div class="modal-club-contenido">
        <div class="modal-club-header" id="modalTitulo">Información del Club</div>
        <div class="modal-club-detalles">
            <p><strong>Descripción:</strong> <span id="modalDescripcion"></span></p>
            <p><strong>Cupo Disponible:</strong> <span id="modalCupo"></span> vacantes</p>
            <p style="margin-top: 15px; color: #c0392b; font-weight: bold;">¿Estás seguro de que deseas inscribirte a este club?</p>
        </div>
        <div class="modal-club-acciones">
            <button class="btn-modal-cancelar" onclick="cerrarConfirmacion()">Cancelar</button>
            <a id="btnConfirmarInscripcion" href="#" class="btn-modal-confirmar">Confirmar e Inscribirme</a>
        </div>
    </div>
</div>

<script>
    // Función para abrir el modal y rellenar la información dinámicamente
    function abrirConfirmacion(id, nombre, descripcion, cupo) {
        document.getElementById('modalTitulo').innerText = "Información del Club: " + nombre;
        document.getElementById('modalDescripcion').innerText = descripcion;
        document.getElementById('modalCupo').innerText = cupo;
        
        // Asignamos la dirección final al botón de confirmación del modal
        document.getElementById('btnConfirmarInscripcion').href = "?accion=clubes&inscribir_id=" + id;
        
        // Mostramos el modal usando flex para centrarlo en pantalla
        document.getElementById('modalConfirmarClub').style.display = 'flex';
    }

    // Función para cerrar el modal si el usuario se arrepiente
    function cerrarConfirmacion() {
        document.getElementById('modalConfirmarClub').style.display = 'none';
    }

    // Cerrar el modal automáticamente si se hace clic fuera del recuadro blanco
    window.onclick = function(event) {
        var modal = document.getElementById('modalConfirmarClub');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>